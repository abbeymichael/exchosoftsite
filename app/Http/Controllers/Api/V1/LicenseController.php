<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\ValidationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    // Max clock skew allowed for timestamp-based replay protection (seconds)
    private const MAX_TIMESTAMP_SKEW = 300;

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/licenses/validate
    //
    // Validates a license key against a device_id and product.
    //
    // IDEMPOTENCY GUARANTEE (same device_id + same license_key)
    // ──────────────────────────────────────────────────────────────────────────
    // If a device_id that is already ACTIVE on this license sends another
    // validate request, the server returns a success response immediately
    // WITHOUT consuming a new activation slot, WITHOUT changing activated_at,
    // and WITHOUT changing expires_at.  Only last_seen_at / app_version /
    // ip_address are refreshed (heartbeat).
    //
    // Device-ID strategies by app type
    // ──────────────────────────────────────────────────────────────────────────
    //   • Desktop apps  → hardware fingerprint generated client-side
    //                      (CPU ID, MAC, motherboard serial, etc.)
    //   • Web / SaaS    → generate a stable UUID v4 at first login; persist
    //                      in the database or localStorage; pass as device_id.
    //                      If the user clears storage, a new ID is generated
    //                      (treated as a new device — slot is consumed).
    //   • Cloud-hosted  → use the server's hostname or instance-id combined
    //                      with the installation UUID.
    //   • Hybrid        → pick the most stable identifier available:
    //                      prefer hardware > instance-id > user-persisted UUID.
    //
    // The app_type field (desktop|web|cloud|hybrid) is advisory — it is stored
    // on the activation record for analytics but does NOT change validation
    // logic.  max_activations is always enforced.
    // ──────────────────────────────────────────────────────────────────────────

    public function validate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'nullable|string',
            'license_key' => 'required|string',
            'device_id' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'app_type' => 'nullable|string|in:desktop,web,cloud,hybrid',
            'platform' => 'nullable|string|max:50',
            'os' => 'nullable|string|max:64',
            'app_version' => 'nullable|string|max:32',
            'hardware_id' => 'nullable|string|max:255',
            'timestamp' => 'nullable|integer',
            'nonce' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, $validator->errors()->toArray());
        }

        $licenseKey = strtoupper(trim($request->license_key));

        // ─────────────────────────────────────────────
        // Replay protection (timestamp)
        // ─────────────────────────────────────────────
        if ($request->filled('timestamp')) {
            $skew = abs(time() - (int) $request->timestamp);

            if ($skew > self::MAX_TIMESTAMP_SKEW) {
                return $this->errorResponse(
                    'Request timestamp is invalid.',
                    403,
                    [],
                    'timestamp_skew'
                );
            }
        }

        // ─────────────────────────────────────────────
        // Replay protection (nonce)
        // ─────────────────────────────────────────────
        if ($request->filled('nonce')) {
            $nonceKey = 'nonce:'.hash('sha256', $request->nonce);

            if (Cache::has($nonceKey)) {
                return $this->errorResponse(
                    'Replay request detected.',
                    403,
                    [],
                    'replay_attack'
                );
            }

            Cache::put($nonceKey, true, self::MAX_TIMESTAMP_SKEW * 2);
        }

        // ─────────────────────────────────────────────
        // License lookup (no product filtering)
        // ─────────────────────────────────────────────
        $license = License::with(['product', 'customer'])
            ->whereRaw('UPPER(TRIM(license_key)) = ?', [$licenseKey])
            ->first();

        if (! $license) {
            return $this->errorResponse('License not found.', 404, [], 'license_not_found');
        }

        // ─────────────────────────────────────────────
        // Status checks
        // ─────────────────────────────────────────────
        if ($license->status === 'revoked') {
            return $this->errorResponse('License revoked.', 403, [], 'license_revoked');
        }

        if ($license->status === 'suspended') {
            return $this->errorResponse('License suspended.', 403, [], 'license_suspended');
        }

        if ($license->isExpired()) {
            return $this->errorResponse('License expired.', 403, [], 'license_expired');
        }

        // ─────────────────────────────────────────────
        // Version check
        // ─────────────────────────────────────────────
        if (! $license->isAppVersionAllowed($request->app_version)) {
            return $this->errorResponse(
                'App version not supported.',
                403,
                [
                    'app_version' => $request->app_version,
                    'min' => $license->min_app_version ?? $license->product?->min_app_version,
                    'max' => $license->max_app_version ?? $license->product?->max_app_version,
                ],
                'version_not_allowed'
            );
        }

        // ─────────────────────────────────────────────
        // Suspicion detection
        // ─────────────────────────────────────────────
        $suspiciousReason = LicenseActivation::detectSuspicious($license, [
            'device_id' => $request->device_id,
            'ip' => $request->ip(),
        ]);

        // ─────────────────────────────────────────────
        // ATOMIC ACTIVATION SECTION
        // ─────────────────────────────────────────────
        [$license, $activation, $isNewActivation] = DB::transaction(function () use ($license, $request, $suspiciousReason) {

            $license = License::where('id', $license->id)
                ->lockForUpdate()
                ->first();

            $activation = LicenseActivation::where('license_id', $license->id)
                ->where('device_id', $request->device_id)
                ->first();

            $isNew = false;

            // ── Existing active device (heartbeat) ──
            if ($activation && $activation->status === 'active') {

                $activation->update([
                    'last_seen_at' => now(),
                    'app_version' => $request->app_version ?? $activation->app_version,
                    'device_name' => $request->device_name ?? $activation->device_name,
                    'ip_address' => $request->ip(),
                ]);

            } else {

                // revoked device cannot return
                if ($activation && $activation->status === 'revoked') {
                    abort(403, 'Device permanently revoked');
                }

                $activeCount = LicenseActivation::where('license_id', $license->id)
                    ->where('status', 'active')
                    ->count();

                if ($activeCount >= $license->max_activations) {
                    abort(403, 'Activation limit reached');
                }

                if ($activation && $activation->status === 'deactivated') {

                    $activation->update([
                        'status' => 'active',
                        'last_seen_at' => now(),
                        'ip_address' => $request->ip(),
                        'app_version' => $request->app_version,
                        'device_name' => $request->device_name,
                    ]);

                } else {

                    $activation = LicenseActivation::create([
                        'license_id' => $license->id,
                        'device_id' => $request->device_id,
                        'hardware_id' => $request->hardware_id,
                        'device_name' => $request->device_name,
                        'app_type' => $request->app_type ?? 'desktop',
                        'platform' => $request->platform,
                        'os' => $request->os,
                        'app_version' => $request->app_version,
                        'fingerprint' => LicenseActivation::buildFingerprint([
                            'device_id' => $request->device_id,
                            'hardware_id' => $request->hardware_id,
                            'platform' => $request->platform,
                            'os' => $request->os,
                        ]),
                        'ip_address' => $request->ip(),
                        'status' => 'active',
                        'is_suspicious' => (bool) $suspiciousReason,
                        'suspicious_reason' => $suspiciousReason,

                        // FIXED: stable issuance values
                        'activated_at' => now(),
                        'issued_at' => now(),

                        'last_seen_at' => now(),
                    ]);

                    $isNew = true;
                }
            }

            return [$license, $activation, $isNew];
        });

        // ─────────────────────────────────────────────
        // Derived values
        // ─────────────────────────────────────────────
        $activeCount = LicenseActivation::where('license_id', $license->id)
            ->where('status', 'active')
            ->count();

        $offlineTtl = $license->product->offline_ttl_hours_effective ?? 168;

        // ─────────────────────────────────────────────
        // BUILD PAYLOAD
        // ─────────────────────────────────────────────
        $payload = [
            'license_id' => $license->uuid,
            'license_key' => $license->license_key,
            'product' => $license->product?->product_code,

            'edition' => $license->edition,
            'type' => $license->type,
            'status' => $license->status,
            'expires_at' => $license->expires_at?->toDateString(),

            'device_id' => $request->device_id,
            'app_type' => $activation->app_type ?? 'desktop',

            // FIXED: stable issued_at from DB
            'issued_at' => $activation->issued_at?->toISOString()
                ?? $activation->created_at?->toISOString(),

            'activated_at' => $activation->activated_at?->toISOString(),

            'max_devices' => $license->max_activations,
            'activations_used' => $activeCount,
            'is_new_activation' => $isNewActivation,

            'offline_valid_until' => now()->addHours($offlineTtl)->toISOString(),
            'offline_ttl_hours' => $offlineTtl,

            'response_nonce' => Str::random(32),

            'min_app_version' => $license->min_app_version ?? $license->product?->min_app_version,
            'max_app_version' => $license->max_app_version ?? $license->product?->max_app_version,

            'grace_period_days' => $license->grace_period_days
                ?? $license->product?->grace_period_days
                ?? 0,

            'revocation_checksum' => $license->revocation_checksum,
            'validation_source' => $license->isInGracePeriod() ? 'grace_period' : 'online',
        ];

        // ─────────────────────────────────────────────
        // SIGN ONLY STABLE DATA (NO NONCE, NO DYNAMIC FIELDS)
        // ─────────────────────────────────────────────
        $signablePayload = [
            'license_id' => $payload['license_id'],
            'license_key' => $payload['license_key'],
            'product' => $payload['product'],
            'edition' => $payload['edition'],
            'type' => $payload['type'],
            'status' => $payload['status'],
            'expires_at' => $payload['expires_at'],
            'device_id' => $payload['device_id'],
            'activated_at' => $payload['activated_at'],
            'issued_at' => $payload['issued_at'],
            'max_devices' => $payload['max_devices'],
            'activations_used' => $payload['activations_used'],
            'min_app_version' => $payload['min_app_version'],
            'max_app_version' => $payload['max_app_version'],
            'grace_period_days' => $payload['grace_period_days'],
            'revocation_checksum' => $payload['revocation_checksum'],
        ];

        ksort($signablePayload);

        $signature = base64_encode(
            hash_hmac(
                'sha256',
                json_encode($signablePayload),
                config('licensing.signing_key'),
                true
            )
        );

        return response()->json([
            'valid' => true,
            'message' => $isNewActivation
                ? 'License activated successfully.'
                : 'License validated successfully.',

            'timestamp' => now()->toISOString(),

            'license' => [
                'payload' => $payload,
                'signature' => $signature,
            ],
        ]);
    }
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/licenses/status
    // ──────────────────────────────────────────────────────────────────────────

    public function status(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'product' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed.',
                422,
                $validator->errors()->toArray()
            );
        }

        $licenseKey = strtoupper(trim($request->input('license_key')));
        $productInput = $request->input('product');

        /**
         * STEP 1: Get license ONLY (no product filtering)
         */
        $license = License::with('product')
            ->whereRaw('UPPER(TRIM(license_key)) = ?', [$licenseKey])
            ->first();

        if (! $license) {
            $this->captureLog($request, null, false, 'license_not_found', 'status');

            return $this->errorResponse(
                'License not found.',
                404,
                [],
                'license_not_found'
            );
        }

        /**
         * STEP 2: Optional product validation (non-blocking)
         */
        $productMatch = true;

        if (! empty($productInput)) {
            $productUpper = strtoupper($productInput);

            $productMatch =
                $license->product &&
                (
                    $license->product->slug === $productInput ||
                    strtoupper($license->product->product_code) === $productUpper ||
                    $license->product->app_identifier === $productInput
                );
        }

        /**
         * STEP 3: License validity check
         */
        $isValid = $license->isValid();

        $this->captureLog($request, $license, true, null, 'status');

        return response()->json([
            'valid' => $isValid,
            'product_match' => $productMatch,
            'message' => $isValid ? 'License is valid.' : 'License is not valid.',
            'timestamp' => now()->toIso8601String(),
            'license' => [
                'payload' => [
                    'license_id' => $license->uuid,
                    'license_key' => $license->license_key,
                    'product' => $license->product?->product_code,
                    'edition' => $license->edition,
                    'type' => $license->type,
                    'status' => $license->status,
                    'expires_at' => $license->expires_at?->toDateString(),
                    'max_activations' => $license->max_activations,
                    'current_activations' => $license->current_activations,
                    'in_grace_period' => $license->isInGracePeriod(),
                    'grace_period_days' => $license->grace_period_days
                        ?? $license->product?->grace_period_days
                        ?? 0,
                    'support_tier' => $license->support_tier,
                    'features' => $license->features ?? [],
                    'min_app_version' => $license->min_app_version ?? $license->product?->min_app_version,
                    'max_app_version' => $license->max_app_version ?? $license->product?->max_app_version,
                    'revocation_checksum' => $license->revocation_checksum,
                    'validation_source' => 'online',
                ],
            ],
        ]);
    }
    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/licenses/renew
    // ──────────────────────────────────────────────────────────────────────────

    public function renew(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'product' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, $validator->errors()->toArray());
        }

        $licenseQuery = License::where('license_key', strtoupper($request->license_key));

        if ($request->filled('product')) {
            $licenseQuery->whereHas(
                'product',
                fn ($q) => $q->where('slug', $request->product)
                    ->orWhere('product_code', strtoupper($request->product))
            );
        }

        $license = $licenseQuery->first();

        if (! $license) {
            return $this->errorResponse('License not found.', 404, [], 'license_not_found');
        }

        if (! $license->is_renewable) {
            return $this->errorResponse(
                'This license type is not eligible for renewal.',
                422,
                ['type' => $license->type],
                'not_renewable'
            );
        }

        $this->captureLog($request, $license, true, null, 'renew');

        return response()->json([
            'valid' => true,
            'message' => 'Renewal request received. Please complete payment to activate the extension.',
            'timestamp' => now()->toISOString(),
            'license' => [
                'payload' => [
                    'license_id' => $license->uuid,
                    'license_key' => $license->license_key,
                    'status' => $license->status,
                    'expires_at' => $license->expires_at?->toDateString(),
                    'is_renewable' => $license->is_renewable,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/licenses/deactivate
    // ──────────────────────────────────────────────────────────────────────────

    public function deactivate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, $validator->errors()->toArray());
        }

        $license = License::where('license_key', strtoupper($request->license_key))->first();

        if (! $license) {
            return $this->errorResponse('License not found.', 404, [], 'license_not_found');
        }

        $activation = LicenseActivation::where('license_id', $license->id)
            ->where('device_id', $request->device_id)
            ->where('status', 'active')
            ->first();

        if (! $activation) {
            return $this->errorResponse(
                'No active activation found for this device.',
                404,
                ['device_id' => $request->device_id],
                'activation_not_found'
            );
        }

        $activation->update([
            'status' => 'deactivated',
            'deactivated_at' => now(),
        ]);

        $license->decrement('current_activations');

        $this->captureLog($request, $license, true, null, 'deactivate');

        return response()->json([
            'valid' => true,
            'message' => 'Device deactivated successfully. The activation slot has been freed.',
            'timestamp' => now()->toISOString(),
            'license' => [
                'payload' => [
                    'license_id' => $license->uuid,
                    'license_key' => $license->license_key,
                    'activation_id' => $activation->uuid,
                    'device_id' => $request->device_id,
                    'deactivated_at' => now()->toISOString(),
                    'current_activations' => max(0, $license->current_activations - 1),
                    'max_activations' => $license->max_activations,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function errorResponse(
        string $message,
        int $status,
        array $errors = [],
        string $errorCode = ''
    ): JsonResponse {
        $body = [
            'valid' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errorCode) {
            $body['error_code'] = $errorCode;
        }
        if ($errors) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }

    private function captureLog(
        Request $request,
        ?License $license,
        bool $success,
        ?string $reason = null,
        string $action = 'validate',
        array $extra = []
    ): void {
        try {
            ValidationLog::capture([
                'license_key' => $request->license_key ?? $request->query('license_key'),
                'license_id' => $license?->id,
                'product_slug' => $request->product ?? $request->query('product'),
                'action' => $action,
                'success' => $success,
                'failure_reason' => $reason,
                'device_id' => $request->device_id,
                'app_version' => $request->app_version,
                'platform' => $request->platform,
                'nonce' => $request->nonce,
                'timestamp' => $request->timestamp
                    ? now()->setTimestamp((int) $request->timestamp)
                    : null,
                'response_nonce' => $extra['response_nonce'] ?? null,
                'validation_source' => $extra['validation_source'] ?? 'online',
                'offline_valid_until' => $extra['offline_valid_until'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('ValidationLog write failed: '.$e->getMessage());
        }
    }
}
