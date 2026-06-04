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
                'description'     => 'Real-time file synchronisation for power users.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoDB Studio',
                'slug'            => 'exchodb-studio',
                'product_code'    => 'EDBS',
                'platform'        => 'desktop',
                'current_version' => '2.0.4',
                'description'     => 'Visual database management for SQLite and beyond.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoCloud SaaS',
                'slug'            => 'exchocloud-saas',
                'product_code'    => 'ECLDS',
                'platform'        => 'saas',
                'current_version' => '1.5.0',
                'description'     => 'Cloud-first collaboration and storage.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoVault',
                'slug'            => 'exchovault',
                'product_code'    => 'EVLT',
                'platform'        => 'offline-first',
                'current_version' => '1.1.0',
                'description'     => 'Offline-first encrypted secret manager.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoAnalytics',
                'slug'            => 'exchoanalytics',
                'product_code'    => 'EANL',
                'platform'        => 'saas',
                'current_version' => '2.1.0',
                'description'     => 'Real-time analytics and reporting dashboard.',
                'is_active'       => true,
            ],
            [
                'name'            => 'ExchoAPI Gateway',
                'slug'            => 'exchoapi-gateway',
                'product_code'    => 'EAPIG',
                'platform'        => 'hybrid',
                'current_version' => '1.0.0',
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
