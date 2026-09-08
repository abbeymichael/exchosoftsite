<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Network Monitor',
                'slug' => 'network-monitor',
                'description' => 'An open source project developed by Abbey Michael.',
                'github_url' => 'https://github.com/abbeymichael/network-monitor',
                'project_url' => 'https://github.com/abbeymichael/network-monitor',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 6,
            ],
            [
                'title' => 'Pitchdesk',
                'slug' => 'pitchdesk',
                'description' => 'An open source project developed by Abbey Michael.',
                'github_url' => 'https://github.com/abbeymichael/pitchdesk',
                'project_url' => 'https://github.com/abbeymichael/pitchdesk',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 7,
            ],
            [
                'title' => 'Abbeymichael',
                'slug' => 'abbeymichael',
                'description' => 'Config files for my GitHub profile.',
                'github_url' => 'https://github.com/abbeymichael/abbeymichael',
                'project_url' => 'https://github.com/abbeymichael',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 8,
            ],
            [
                'title' => 'Laravel Paystack',
                'slug' => 'laravel-paystack',
                'description' => ':credit_card: :package: :moneybag: Laravel 6, 7, 8, 9, 10 and 11 Package for Paystack',
                'github_url' => 'https://github.com/abbeymichael/laravel-paystack',
                'project_url' => 'https://paystack.co',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 9,
            ],
            [
                'title' => 'Restaurant',
                'slug' => 'restaurant',
                'description' => 'An open source project developed by Abbey Michael.',
                'github_url' => 'https://github.com/abbeymichael/restaurant',
                'project_url' => 'https://github.com/abbeymichael/restaurant',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 10,
            ],
            [
                'title' => 'Sportsdataupdated',
                'slug' => 'sportsdataupdated',
                'description' => 'Exported from PHPSandbox.',
                'github_url' => 'https://github.com/abbeymichael/sportsdataupdated',
                'project_url' => 'https://phpsandbox.io/n/vp3lc',
                'status' => 'published',
                'category' => 'software',
                'sort_order' => 11,
            ],
        ];

        foreach ($items as $item) {
            PortfolioItem::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
