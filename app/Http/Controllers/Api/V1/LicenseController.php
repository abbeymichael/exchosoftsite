<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\ValidationLog;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'product'     => 'nullable|string',
            'license_key' => 'required|string',
            'device_id'   => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'app_type'    => 'nullable|string|in:desktop,web,cloud,hybrid',
            'platform'    => 'nullable|string|max:50',
            'os'          => 'nullable|string|max:64',
            'app_version' => 'nullable|string|max:32',
            'hardware_id' => 'nullable|string|max:255',
            // Offline activation support — client supplies a challenge nonce
            'timestamp'   => 'nullable|integer',
            'nonce'       => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed.',
                422,
                $validator->errors()->toArray()
            );
        }

        // ── Replay-attack prevention ──────────────────────────────────────────
        if ($request->filled('timestamp')) {
            $clientTs = (int) $request->timestamp;
            $skew     = abs(time() - $clientTs);

            if ($skew > self::MAX_TIMESTAMP_SKEW) {
                AuditService::security('timestamp_skew', [
                    'client_ts' => $clientTs,
                    'skew'      => $skew,
                ]);

                return $this->errorResponse(
                    'Request timestamp is too old or too far in the future.',
                    403,
                    [],
                    'timestamp_skew'
                );
            }
        }

        if ($request->filled('nonce')) {
            $nonceCacheKey = 'nonce:' . $request->nonce;

            if (Cache::has($nonceCacheKey)) {
                AuditService::security('replay_attack', [
                    'nonce'       => $request->nonce,
                    'license_key' => $request->license_key,
                ]);

                return $this->errorResponse(
                    'Duplicate request detected (replay attack prevention).',
                    403,
                    [],
                    'replay_attack'
                );
            }

            Cache::put($nonceCacheKey, 1, self::MAX_TIMESTAMP_SKEW * 2);
        }

        // ── License lookup ────────────────────────────────────────────────────
        $licenseQuery = License::with(['product', 'customer'])
            ->where('license_key', strtoupper($request->license_key));

        if ($request->filled('product')) {
            $licenseQuery->whereHas(
                'product',
                fn ($q) => $q->where('slug', $request->product)
                    ->orWhere('product_code', strtoupper($request->product))
                    ->orWhere('app_identifier', $request->product)
            );
        }

        $license = $licenseQuery->first();

        if (! $license) {
            $this->captureLog($request, null, false, 'license_not_found');
            return $this->errorResponse('License not found.', 404, [], 'license_not_found');
        }

        // ── Status checks ─────────────────────────────────────────────────────
        if ($license->status === 'revoked') {
            $this->captureLog($request, $license, false, 'revoked');
            return $this->errorResponse('License has been revoked.', 403, [], 'license_revoked');
        }

        if ($license->status === 'suspended') {
            $this->captureLog($request, $license, false, 'suspended');
            return $this->errorResponse('License is currently suspended.', 403, [], 'license_suspended');
        }

        if ($license->isExpired()) {
            $this->captureLog($request, $license, false, 'expired');
            return $this->errorResponse('License has expired.', 403, [], 'license_expired');
        }

        // ── App version gating ────────────────────────────────────────────────
        if (! $license->isAppVersionAllowed($request->app_version)) {
            $min = $license->min_app_version ?? $license->product->min_app_version;
            $max = $license->max_app_version ?? $license->product->max_app_version;

            $this->captureLog($request, $license, false, 'version_not_allowed');

            return $this->errorResponse(
                'App version ' . $request->app_version . ' is not compatible with this license. '
                . 'Supported range: ' . ($min ?? '*') . ' – ' . ($max ?? '*'),
                403,
                [
                    'app_version'     => $request->app_version,
                    'min_app_version' => $min ?? '*',
                    'max_app_version' => $max ?? '*',
                ],
                'version_not_allowed'
            );
        }

        // ── Validation source ─────────────────────────────────────────────────
        $validationSource = $license->isInGracePeriod() ? 'grace_period' : 'online';

        // ── Suspicious activation detection ──────────────────────────────────
        $suspiciousReason = LicenseActivation::detectSuspicious($license, [
            'device_id' => $request->device_id,
            'ip'        => $request->ip(),
        ]);

        // ── IDEMPOTENT Device Activation ──────────────────────────────────────
        //
        // RULE: if the same device_id is already ACTIVE on this license,
        // return a successful validation response immediately.
        // - activated_at  : NEVER changed after first activation
        // - expires_at    : NEVER changed here (provisioning API only)
        // - max_activations: NEVER exceeded; slots only consumed for new devices
        //
        $isNewActivation    = false;
        $existingActivation = LicenseActivation::where('license_id', $license->id)
            ->where('device_id', $request->device_id)
            ->where('status', 'active')
            ->first();

        if ($existingActivation) {
            // ── EXISTING ACTIVE DEVICE — heartbeat only ───────────────────────
            // The device is already registered and active.
            // We ONLY update heartbeat fields. No slot is consumed.
            // activated_at, expires_at — NEVER touched.
            $existingActivation->update([
                'last_seen_at' => now(),
                'app_version'  => $request->app_version ?? $existingActivation->app_version,
                'ip_address'   => $request->ip(),
                'device_name'  => $request->device_name ?? $existingActivation->device_name,
            ]);
            // $isNewActivation stays false — tells client it is a re-validation
        } else {
            // ── Check for previously deactivated record for this device ───────
            $deactivatedRecord = LicenseActivation::where('license_id', $license->id)
                ->where('device_id', $request->device_id)
                ->whereIn('status', ['deactivated', 'revoked'])
                ->latest()
                ->first();

            // Count currently active slots
            $activeCount = LicenseActivation::where('license_id', $license->id)
                ->where('status', 'active')
                ->count();

            if ($activeCount >= $license->max_activations) {
                $this->captureLog($request, $license, false, 'activation_limit_reached');

                return $this->errorResponse(
                    "Activation limit reached. This license allows {$license->max_activations} device(s).",
                    403,
                    [
                        'max_activations'     => $license->max_activations,
                        'current_activations' => $activeCount,
                    ],
                    'activation_limit_reached'
                );
            }

            // Build fingerprint from hardware identifiers
            $fingerprint = LicenseActivation::buildFingerprint([
                'device_id'   => $request->device_id,
                'hardware_id' => $request->hardware_id,
                'platform'    => $request->platform,
                'os'          => $request->os,
            ]);

            if ($deactivatedRecord) {
                // Re-activate existing record — PRESERVE original activated_at
                $deactivatedRecord->update([
                    'status'            => 'active',
                    'app_version'       => $request->app_version ?? $deactivatedRecord->app_version,
                    'app_type'          => $request->app_type ?? $deactivatedRecord->app_type,
                    'device_name'       => $request->device_name ?? $deactivatedRecord->device_name,
                    'ip_address'        => $request->ip(),
                    'last_seen_at'      => now(),
                    'deactivated_at'    => null,
                    'is_suspicious'     => (bool) $suspiciousReason,
                    'suspicious_reason' => $suspiciousReason,
                ]);
                $existingActivation = $deactivatedRecord->fresh();
            } else {
                // Brand-new device — consume one slot
                $existingActivation = LicenseActivation::create([
                    'license_id'        => $license->id,
                    'device_id'         => $request->device_id,
                    'hardware_id'       => $request->hardware_id,
                    'device_name'       => $request->device_name,
                    'app_type'          => $request->app_type ?? 'desktop',
                    'platform'          => $request->platform,
                    'os'                => $request->os,
                    'app_version'       => $request->app_version,
                    'fingerprint'       => $fingerprint,
                    'ip_address'        => $request->ip(),
                    'activation_source' => 'api',
                    'status'            => 'active',
                    'is_suspicious'     => (bool) $suspiciousReason,
                    'suspicious_reason' => $suspiciousReason,
                    // activated_at is set ONCE here — NEVER updated
                    'activated_at'      => now(),
                    'last_seen_at'      => now(),
                ]);

                $isNewActivation = true;
            }

            $license->increment('current_activations');

            // Record first_activated_at on first-ever activation for this license
            if (! $license->first_activated_at) {
                $license->update([
                    'first_activated_at' => now(),
                    'status'             => 'active',
                ]);
            }
        }

        // ── Generate server-side response nonce ───────────────────────────────
        $responseNonce = Str::random(32);

        // ── Offline validity window ───────────────────────────────────────────
        $offlineTtlHours   = $license->product->offline_ttl_hours_effective ?? 168;
        $offlineValidUntil = now()->addHours($offlineTtlHours);

        // ── Signed payload ────────────────────────────────────────────────────
        $activatedAt = $existingActivation->activated_at ?? now();

        $payload = [
            'license_id'  => $license->uuid,
            'license_key' => $license->license_key,

            'product_id'  => $license->product->uuid,
            'product'     => $license->product->product_code,

            'edition'     => $license->edition,
            'type'        => $license->type,
            'status'      => $license->status,
            'expires_at'  => $license->expires_at?->toDateString(),

            // Activation context — dates are STABLE after first activation
            'device_id'          => $request->device_id,
            'app_type'           => $existingActivation->app_type ?? ($request->app_type ?? 'desktop'),
            'activated_at'       => $activatedAt->toISOString(),
            'issued_at'          => now()->toISOString(),
            'max_devices'        => $license->max_activations,
            'activations_used'   => $license->fresh()->current_activations,
            'is_new_activation'  => $isNewActivation,

            'support_tier' => $license->support_tier,
            'features'     => $license->features ?? [],

            'min_app_version' => $license->min_app_version ?? $license->product->min_app_version,
            'max_app_version' => $license->max_app_version ?? $license->product->max_app_version,

            'grace_period_days' => $license->grace_period_days
                ?? $license->product->grace_period_days
                ?? 0,

            'offline_valid_until' => $offlineValidUntil->toISOString(),
            'offline_ttl_hours'   => $offlineTtlHours,

            'response_nonce' => $responseNonce,

            'revocation_checksum' => $license->revocation_checksum,

            'validation_source' => $validationSource,
        ];

        $signingKey = $license->product->secret_key ?? config('app.key');
        $signature  = base64_encode(hash_hmac('sha256', json_encode($payload), $signingKey, true));

        $this->captureLog($request, $license, true, null, 'validate', [
            'response_nonce'      => $responseNonce,
            'validation_source'   => $validationSource,
            'offline_valid_until' => $offlineValidUntil,
        ]);

        return response()->json([
            'valid'     => true,
            'message'   => $isNewActivation
                ? 'License activated successfully on this device.'
                : 'License validated successfully.',
            'timestamp' => now()->toISOString(),
            'license'   => [
                'payload'   => $payload,
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
            'product'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, $validator->errors()->toArray());
        }

        $licenseQuery = License::with('product')
            ->where('license_key', strtoupper($request->query('license_key', '')));

        if ($request->filled('product')) {
            $licenseQuery->whereHas(
                'product',
                fn ($q) => $q->where('slug', $request->product)
                    ->orWhere('product_code', strtoupper($request->product))
                    ->orWhere('app_identifier', $request->product)
            );
        }

        $license = $licenseQuery->first();

        if (! $license) {
            $this->captureLog($request, null, false, 'license_not_found', 'status');
            return $this->errorResponse('License not found.', 404, [], 'license_not_found');
        }

        $this->captureLog($request, $license, true, null, 'status');

        return response()->json([
            'valid'     => $license->isValid(),
            'message'   => $license->isValid() ? 'License is valid.' : 'License is not valid.',
            'timestamp' => now()->toISOString(),
            'license'   => [
                'payload' => [
                    'license_id'          => $license->uuid,
                    'license_key'         => $license->license_key,
                    'product'             => $license->product?->product_code,
                    'edition'             => $license->edition,
                    'type'                => $license->type,
                    'status'              => $license->status,
                    'expires_at'          => $license->expires_at?->toDateString(),
                    'max_activations'     => $license->max_activations,
                    'current_activations' => $license->current_activations,
                    'in_grace_period'     => $license->isInGracePeriod(),
                    'grace_period_days'   => $license->grace_period_days
                        ?? $license->product?->grace_period_days
                        ?? 0,
                    'support_tier'        => $license->support_tier,
                    'features'            => $license->features ?? [],
                    'min_app_version'     => $license->min_app_version ?? $license->product?->min_app_version,
                    'max_app_version'     => $license->max_app_version ?? $license->product?->max_app_version,
                    'revocation_checksum' => $license->revocation_checksum,
                    'validation_source'   => 'online',
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
            'product'     => 'nullable|string',
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
            'valid'     => true,
            'message'   => 'Renewal request received. Please complete payment to activate the extension.',
            'timestamp' => now()->toISOString(),
            'license'   => [
                'payload' => [
                    'license_id'   => $license->uuid,
                    'license_key'  => $license->license_key,
                    'status'       => $license->status,
                    'expires_at'   => $license->expires_at?->toDateString(),
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
            'device_id'   => 'required|string',
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
            'status'         => 'deactivated',
            'deactivated_at' => now(),
        ]);

        $license->decrement('current_activations');

        $this->captureLog($request, $license, true, null, 'deactivate');

        return response()->json([
            'valid'     => true,
            'message'   => 'Device deactivated successfully. The activation slot has been freed.',
            'timestamp' => now()->toISOString(),
            'license'   => [
                'payload' => [
                    'license_id'          => $license->uuid,
                    'license_key'         => $license->license_key,
                    'activation_id'       => $activation->uuid,
                    'device_id'           => $request->device_id,
                    'deactivated_at'      => now()->toISOString(),
                    'current_activations' => max(0, $license->current_activations - 1),
                    'max_activations'     => $license->max_activations,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function errorResponse(
        string $message,
        int    $status,
        array  $errors    = [],
        string $errorCode = ''
    ): JsonResponse {
        $body = [
            'valid'     => false,
            'message'   => $message,
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
        Request  $request,
        ?License $license,
        bool     $success,
        ?string  $reason  = null,
        string   $action  = 'validate',
        array    $extra   = []
    ): void {
        try {
            ValidationLog::capture([
                'license_key'         => $request->license_key ?? $request->query('license_key'),
                'license_id'          => $license?->id,
                'product_slug'        => $request->product ?? $request->query('product'),
                'action'              => $action,
                'success'             => $success,
                'failure_reason'      => $reason,
                'device_id'           => $request->device_id,
                'app_version'         => $request->app_version,
                'platform'            => $request->platform,
                'nonce'               => $request->nonce,
                'timestamp'           => $request->timestamp
                    ? now()->setTimestamp((int) $request->timestamp)
                    : null,
                'response_nonce'      => $extra['response_nonce'] ?? null,
                'validation_source'   => $extra['validation_source'] ?? 'online',
                'offline_valid_until' => $extra['offline_valid_until'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('ValidationLog write failed: ' . $e->getMessage());
        }
    }
}
