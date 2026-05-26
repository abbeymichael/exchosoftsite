<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        $products  = Product::all()->keyBy('product_code');
        $customers = Customer::all()->values();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('  Skipping licenses — run ProductSeeder and CustomerSeeder first.');
            return;
        }

        $licenseRows = [
            // product_code,  customer_idx, edition,        type,         max, status,    expires_at
            ['ESYNC', 0, 'standard',     'lifetime',   1,  'active',   null],
            ['EDBS',  1, 'professional', 'annual',     3,  'active',   now()->addYear()],
            ['ECLDS', 2, 'enterprise',   'monthly',    5,  'active',   now()->addMonth()],
            ['EVLT',  3, 'trial',        'trial',      1,  'trial',    now()->addDays(14)],
            ['ESYNC', 4, 'professional', 'lifetime',   3,  'active',   null],
            ['EDBS',  5, 'standard',     'annual',     2,  'active',   now()->addDays(25)], // expiring soon
            ['ECLDS', 6, 'standard',     'monthly',    1,  'expired',  now()->subMonth()],
            ['EVLT',  7, 'enterprise',   'lifetime',   10, 'active',   null],
            ['EANL',  8, 'professional', 'annual',     5,  'active',   now()->addMonths(8)],
            ['ESYNC', 9, 'standard',     'monthly',    1,  'active',   now()->addDays(10)], // expiring soon
            ['EDBS',  0, 'enterprise',   'lifetime',   10, 'revoked',  null],
            ['ECLDS', 1, 'trial',        'trial',      1,  'trial',    now()->addDays(7)],
        ];

        $created = 0;
        foreach ($licenseRows as [$code, $custIdx, $edition, $type, $max, $status, $expiresAt]) {
            $product  = $products->get($code);
            $customer = $customers->get($custIdx % $customers->count());

            if (! $product || ! $customer) {
                continue;
            }

            $prefix = strtoupper(substr($product->product_code, 0, 4));

            License::create([
                'product_id'          => $product->id,
                'customer_id'         => $customer->id,
                'license_key'         => License::generateKey($prefix),
                'edition'             => $edition,
                'type'                => $type,
                'max_activations'     => $max,
                'current_activations' => 0,
                'status'              => $status,
                'expires_at'          => $expiresAt,
                'notes'               => null,
            ]);

            $created++;
        }

        $this->command->info("  {$created} licenses seeded.");
    }
}
