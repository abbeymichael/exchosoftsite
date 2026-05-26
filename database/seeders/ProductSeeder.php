<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'            => 'ExchoSync Pro',
                'slug'            => 'exchosync-pro',
                'product_code'    => 'ESYNC',
                'platform'        => 'desktop',
                'current_version' => '3.2.1',
                'pricing_type'    => 'subscription',
                'description'     => 'Real-time file synchronisation for power users.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoDB Studio',
                'slug'            => 'exchodb-studio',
                'product_code'    => 'EDBS',
                'platform'        => 'desktop',
                'current_version' => '2.0.4',
                'pricing_type'    => 'lifetime',
                'description'     => 'Visual database management for SQLite and beyond.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoCloud SaaS',
                'slug'            => 'exchocloud-saas',
                'product_code'    => 'ECLDS',
                'platform'        => 'saas',
                'current_version' => '1.5.0',
                'pricing_type'    => 'subscription',
                'description'     => 'Cloud-first collaboration and storage.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoVault',
                'slug'            => 'exchovault',
                'product_code'    => 'EVLT',
                'platform'        => 'offline-first',
                'current_version' => '1.1.0',
                'pricing_type'    => 'lifetime',
                'description'     => 'Offline-first encrypted secret manager.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoAnalytics',
                'slug'            => 'exchoanalytics',
                'product_code'    => 'EANL',
                'platform'        => 'saas',
                'current_version' => '2.1.0',
                'pricing_type'    => 'subscription',
                'description'     => 'Real-time analytics and reporting dashboard.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoAPI Gateway',
                'slug'            => 'exchoapi-gateway',
                'product_code'    => 'EAPIG',
                'platform'        => 'hybrid',
                'current_version' => '1.0.0',
                'pricing_type'    => 'subscription',
                'description'     => 'Managed API gateway with rate limiting.',
                'is_active'       => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }

        $this->command->info('  ' . count($products) . ' products seeded.');
    }
}
