<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BulkCreateLicenseRequest;
use App\Http\Requests\Api\CreateLicenseRequest;
use App\Http\Requests\Api\CreateTrialRequest;
use App\Http\Requests\Api\ExtendLicenseRequest;
use App\Http\Requests\Api\RevokeLicenseRequest;
use App\Http\Resources\Api\LicenseBatchResource;
use App\Http\Resources\Api\LicenseResource;
use App\Models\Customer;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Services\AuditService;
use App\Services\LicenseGeneratorService;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Internal Provisioning API
 *
 * All routes protected by auth:sanctum middleware.
 * Rate-limited to 120 rpm by default (see api.php).
 */
class ProvisioningController extends Controller
{
    public function __construct(
        protected LicenseGeneratorService $generator
    ) {}

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/create
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a single license, optionally tied to a customer.
     * If customer_email is supplied and no customer_id, the customer is
     * auto-created or matched by email.
     */
    public function create(CreateLicenseRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Resolve customer
        if (empty($data['customer_id']) && ! empty($data['customer_email'])) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                [
                    'name'    => $data['customer_name'] ?? $data['customer_email'],
                    'company' => $data['company'] ?? null,
                    'type'    => 'individual',
                ]
            );
            $data['customer_id'] = $customer->id;
        }

        $license = $this->generator->createCustomerLicense($data);

        return response()->json([
            'success' => true,
            'message' => 'License created successfully.',
            'data'    => new LicenseResource($license),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/bulk-create
    // ──────────────────────────────────────────────────────────────────────────

    public function bulkCreate(BulkCreateLicenseRequest $request): JsonResponse
    {
        $batch = $this->generator->generateBatch(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => "Batch generated: {$batch->total_generated} licenses created.",
            'data'    => new LicenseBatchResource($batch->load('product')),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/extend
    // ──────────────────────────────────────────────────────────────────────────

    public function extend(ExtendLicenseRequest $request): JsonResponse
    {
        $license = License::where('license_key', strtoupper($request->license_key))
            ->firstOrFail();

        $oldExpiry = $license->expires_at;

        if ($request->filled('expires_at')) {
            $newExpiry = \Carbon\Carbon::parse($request->expires_at)->endOfDay();
        } else {
            $base      = $license->expires_at && $license->expires_at->isFuture()
                ? $license->expires_at
                : now();
            $newExpiry = $base->addDays((int) $request->days)->endOfDay();
        }

        $license->update([
            'expires_at' => $newExpiry,
            'status'     => 'active',
        ]);

        AuditService::log('license.extended', $license, [
            'new_expiry' => $newExpiry->toDateString(),
            'old_expiry' => $oldExpiry?->toDateString() ?? 'lifetime',
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'License expiry extended.',
            'license_id' => $license->uuid,
            'license_key' => $license->license_key,
            'expires_at' => $newExpiry->toDateString(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/revoke
    // ──────────────────────────────────────────────────────────────────────────

    public function revoke(RevokeLicenseRequest $request): JsonResponse
    {
        $license = License::where('license_key', strtoupper($request->license_key))
            ->firstOrFail();

        $oldStatus = $license->status;

        $license->update([
            'status'     => 'revoked',
            'revoked_at' => now(),
        ]);

        // Deactivate all active activations
        LicenseActivation::where('license_id', $license->id)
            ->where('status', 'active')
            ->update([
                'status'         => 'revoked',
                'deactivated_at' => now(),
            ]);

        $license->update(['current_activations' => 0]);

        AuditService::log('license.revoked', $license, [
            'reason'     => $request->reason ?? 'Manual revocation via API',
            'old_status' => $oldStatus,
        ]);

        // Webhook
        if ($license->product->webhook_url) {
            WebhookService::dispatch($license->product, 'license.revoked', [
                'license_key' => $license->license_key,
                'reason'      => $request->reason,
            ]);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'License revoked successfully.',
            'license_id'  => $license->uuid,
            'license_key' => $license->license_key,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/suspend
    // ──────────────────────────────────────────────────────────────────────────

    public function suspend(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key',
            'reason'      => 'nullable|string|max:500',
        ]);

        $license = License::where('license_key', strtoupper($request->license_key))->firstOrFail();

        if ($license->status === 'revoked') {
            return response()->json(['success' => false, 'message' => 'Cannot suspend a revoked license.'], 422);
        }

        $license->update([
            'status'       => 'suspended',
            'suspended_at' => now(),
        ]);

        AuditService::log('license.suspended', $license, [
            'reason' => $request->reason ?? 'Suspended via API',
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'License suspended.',
            'license_key' => $license->license_key,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/unsuspend
    // ──────────────────────────────────────────────────────────────────────────

    public function unsuspend(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key',
        ]);

        $license = License::where('license_key', strtoupper($request->license_key))->firstOrFail();

        $license->update([
            'status'       => 'active',
            'suspended_at' => null,
        ]);

        AuditService::log('license.unsuspended', $license);

        return response()->json(['success' => true, 'message' => 'License unsuspended.']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/reset-devices
    // ──────────────────────────────────────────────────────────────────────────

    public function resetDevices(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key',
        ]);

        $license = License::where('license_key', strtoupper($request->license_key))->firstOrFail();

        $deactivatedCount = LicenseActivation::where('license_id', $license->id)
            ->where('status', 'active')
            ->update([
                'status'         => 'deactivated',
                'deactivated_at' => now(),
            ]);

        $license->update(['current_activations' => 0]);

        AuditService::log('license.devices_reset', $license, ['deactivated' => $deactivatedCount]);

        return response()->json([
            'success'          => true,
            'message'          => "Reset {$deactivatedCount} activation(s).",
            'deactivated_count' => $deactivatedCount,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/regenerate-key
    // ──────────────────────────────────────────────────────────────────────────

    public function regenerateKey(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key',
        ]);

        $license = License::where('license_key', strtoupper($request->license_key))->firstOrFail();
        $oldKey  = $license->license_key;
        $prefix  = $license->key_prefix ?? 'EXCL';

        $newKey  = License::generateUniqueKey($prefix);

        $license->update(['license_key' => $newKey]);

        AuditService::log('license.key_regenerated', $license, [
            'old_key' => $oldKey,
            'new_key' => $newKey,
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'License key regenerated.',
            'license_id'      => $license->uuid,
            'old_license_key' => $oldKey,
            'new_license_key' => $newKey,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/attach-notes
    // ──────────────────────────────────────────────────────────────────────────

    public function attachNotes(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key',
            'notes'       => 'required|string|max:2000',
            'append'      => 'nullable|boolean',
        ]);

        $license = License::where('license_key', strtoupper($request->license_key))->firstOrFail();

        $newNotes = $request->boolean('append', false)
            ? trim(($license->notes ?? '') . "\n" . $request->notes)
            : $request->notes;

        $license->update(['notes' => $newNotes]);

        AuditService::log('license.notes_updated', $license);

        return response()->json(['success' => true, 'message' => 'Notes updated.']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/internal/licenses/create-trial
    // ──────────────────────────────────────────────────────────────────────────

    public function createTrial(CreateTrialRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Resolve customer from email
        if (empty($data['customer_id']) && ! empty($data['customer_email'])) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                ['name' => $data['customer_email'], 'type' => 'individual']
            );
            $data['customer_id'] = $customer->id;
        }

        $license = $this->generator->createTrial($data);

        return response()->json([
            'success' => true,
            'message' => 'Trial license created.',
            'data'    => new LicenseResource($license),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/internal/licenses/{key}
    // ──────────────────────────────────────────────────────────────────────────

    public function show(string $key): JsonResponse
    {
        $license = License::with(['product', 'customer', 'batch', 'activeActivations'])
            ->where('license_key', strtoupper($key))
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new LicenseResource($license),
        ]);
    }
}
