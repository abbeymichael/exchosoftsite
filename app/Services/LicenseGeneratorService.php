<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\License;
use App\Models\LicenseBatch;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LicenseGeneratorService
{
    /**
     * Generate a bulk batch of licenses for a product.
     *
     * @param  array{
     *   product_id: int,
     *   label: string,
     *   quantity: int,
     *   key_prefix: string,
     *   license_type: string,
     *   edition: string,
     *   max_activations: int,
     *   expires_at: string|null,
     *   duration_days: int|null,
     *   reseller_tag: string|null,
     *   notes: string|null,
     *   metadata: array|null,
     * } $params
     * @param  int  $createdBy  User ID
     */
    public function generateBatch(array $params, int $createdBy): LicenseBatch
    {
        $product = Product::query()
            ->when($params['product_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->when($params['product_code'] ?? null, fn ($q, $code) => $q->orWhere('product_code', strtoupper($code)))
            ->firstOrFail();
        $prefix = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $params['key_prefix'] ?? $product->product_code), 0, 8));
        $quantity = min((int) $params['quantity'], 10_000); // safety cap

        return DB::transaction(function () use ($params, $product, $prefix, $quantity, $createdBy) {

            $expiresAt = $this->resolveExpiry(
                $params['license_type'],
                $params['expires_at'] ?? null,
                $params['duration_days'] ?? null
            );

            // Create the batch record
            $batch = LicenseBatch::create([
                'product_id' => $product->id,
                'created_by' => $createdBy,
                'label' => $params['label'],
                'key_prefix' => $prefix,
                'quantity' => $quantity,
                'reseller_tag' => $params['reseller_tag'] ?? null,
                'license_type' => $params['license_type'] ?? 'lifetime',
                'edition' => $params['edition'] ?? 'standard',
                'max_activations' => $params['max_activations'] ?? $product->max_devices ?? 1,
                'expires_at' => $expiresAt,
                'duration_days' => $params['duration_days'] ?? null,
                'notes' => $params['notes'] ?? null,
                'metadata' => $params['metadata'] ?? null,
                'total_generated' => 0,
            ]);

            // Generate licenses in chunks to avoid memory pressure
            $chunkSize = 500;
            $generated = 0;
            $existingKeys = [];

            while ($generated < $quantity) {
                $toGenerate = min($chunkSize, $quantity - $generated);
                $chunk = [];
                $attempts = 0;

                while (count($chunk) < $toGenerate && $attempts < $toGenerate * 5) {
                    $key = License::generateKey($prefix);

                    if (! isset($existingKeys[$key]) && ! License::where('license_key', $key)->exists()) {
                        $existingKeys[$key] = true;
                        $now = now();

                        $chunk[] = [
                            'product_id' => $product->id,
                            'customer_id' => null,
                            'batch_id' => $batch->id,
                            'license_key' => $key,
                            'key_prefix' => $prefix,
                            'edition' => $params['edition'] ?? 'standard',
                            'type' => $params['license_type'] ?? 'lifetime',
                            'max_activations' => $params['max_activations'] ?? $product->max_devices ?? 1,
                            'current_activations' => 0,
                            'status' => 'active',
                            'expires_at' => $expiresAt,
                            'is_renewable' => in_array($params['license_type'] ?? '', ['monthly', 'annual', 'yearly', 'trial']),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    $attempts++;
                }

                License::insert($chunk);
                $generated += count($chunk);
            }

            $batch->update(['total_generated' => $generated]);

            AuditService::log('batch.generated', $batch, [
                'quantity' => $generated,
                'product' => $product->name,
                'prefix' => $prefix,
            ]);

            return $batch->fresh();
        });
    }

    /**
     * Create a single customer-linked license dynamically.
     */
    public function createCustomerLicense(array $params): License
    {
        $product = Product::findOrFail($params['product_id']);
        $customer = Customer::findOrFail($params['customer_id']);

        $prefix = strtoupper(substr($params['key_prefix'] ?? $product->product_code, 0, 8));
        $expiresAt = $this->resolveExpiry(
            $params['type'] ?? 'lifetime',
            $params['expires_at'] ?? null,
            $params['duration_days'] ?? null
        );

        $license = License::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'batch_id' => $params['batch_id'] ?? null,
            'license_key' => License::generateUniqueKey($prefix),
            'key_prefix' => $prefix,
            'edition' => $params['edition'] ?? 'standard',
            'type' => $params['type'] ?? 'lifetime',
            'max_activations' => $params['max_activations'] ?? $product->max_devices ?? 1,
            'status' => $params['status'] ?? 'active',
            'expires_at' => $expiresAt,
            'order_id' => $params['order_id'] ?? null,
            'transaction_id' => $params['transaction_id'] ?? null,
            'reseller_id' => $params['reseller_id'] ?? null,
            'support_tier' => $params['support_tier'] ?? 'standard',
            'is_renewable' => $params['is_renewable'] ?? true,
            'grace_period_days' => $params['grace_period_days'] ?? 0,
            'notes' => $params['notes'] ?? null,
            'metadata' => $params['metadata'] ?? null,
            // Enterprise licensing fields (Phase 2)
            'features' => $params['features'] ?? null,
            'min_app_version' => $params['min_app_version'] ?? null,
            'max_app_version' => $params['max_app_version'] ?? null,
        ]);

        AuditService::log('license.created', $license, [
            'customer' => $customer->email,
            'product' => $product->name,
            'type' => $license->type,
        ]);

        // Fire webhook if product has one configured
        if ($product->webhook_url) {
            WebhookService::dispatch($product, 'license.created', [
                'license_key' => $license->license_key,
                'customer_email' => $customer->email,
                'order_id' => $license->order_id,
                'expires_at' => $license->expires_at?->toDateString(),
            ]);
        }

        return $license->fresh(['product', 'customer']);
    }

    /**
     * Create a trial license (short-lived, marked as trial type).
     */
    public function createTrial(array $params): License
    {
        $product = Product::findOrFail($params['product_id']);
        $trialDays = $params['trial_days'] ?? 14;
        $prefix = strtoupper(substr($product->product_code, 0, 4)).'T';

        $license = License::create([
            'product_id' => $product->id,
            'customer_id' => $params['customer_id'] ?? null,
            'license_key' => License::generateUniqueKey($prefix),
            'key_prefix' => $prefix,
            'edition' => $params['edition'] ?? 'standard',
            'type' => 'trial',
            'max_activations' => $params['max_activations'] ?? 1,
            'status' => 'trial',
            'expires_at' => now()->addDays($trialDays),
            'is_renewable' => false,
            'notes' => "Trial license – {$trialDays} days",
            'metadata' => $params['metadata'] ?? null,
            // Enterprise licensing fields (Phase 2)
            'features' => $params['features'] ?? null,
            'min_app_version' => $params['min_app_version'] ?? null,
            'max_app_version' => $params['max_app_version'] ?? null,
        ]);

        AuditService::log('license.trial_created', $license, [
            'trial_days' => $trialDays,
            'product' => $product->name,
        ]);

        return $license->fresh(['product', 'customer']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    protected function resolveExpiry(string $type, ?string $expiresAt, ?int $durationDays): ?Carbon
    {
        if ($type === 'lifetime') {
            return null;
        }

        if ($expiresAt) {
            return Carbon::parse($expiresAt)->endOfDay();
        }

        if ($durationDays) {
            return now()->addDays($durationDays)->endOfDay();
        }

        return match ($type) {
            'trial' => now()->addDays(14)->endOfDay(),
            'monthly' => now()->addMonth()->endOfDay(),
            'annual',
            'yearly' => now()->addYear()->endOfDay(),
            default => null,
        };
    }
}
