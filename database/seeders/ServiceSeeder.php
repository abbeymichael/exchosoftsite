<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Custom Software Development',
                'description' => 'We build systems from the ground up — designed around how your business actually works, not how an off-the-shelf product assumes it works.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>',
                'tag' => 'Development'
            ],
            [
                'name' => 'Technology Consulting',
                'description' => 'We help organisations understand what technology they actually need — and what they don\'t. Honest advice, clear direction.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                'tag' => 'Consulting'
            ],
            [
                'name' => 'System Architecture & Design',
                'description' => 'We design systems that are built to last — scalable, maintainable, and resilient under the actual conditions of African markets.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/>',
                'tag' => 'Architecture'
            ],
            [
                'name' => 'Digital Transformation',
                'description' => 'We help businesses transition from manual or legacy processes to modern, efficient digital systems — with minimal disruption and maximum adoption.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                'tag' => 'Transformation'
            ],
            [
                'name' => 'Offline-First Engineering',
                'description' => 'We specialise in systems that function fully without internet access — syncing when connectivity is available, never losing data when it isn\'t.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>',
                'tag' => 'Specialist'
            ],
            [
                'name' => 'Long-Term Tech Partnership',
                'description' => 'We stay involved after launch — providing ongoing support, feature development, and strategic guidance as your business grows.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                'tag' => 'Partnership'
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                [
                    'slug' => Str::slug($service['name']),
                    'description' => $service['description'],
                    'icon' => $service['icon'],
                    'tag' => $service['tag']
                ]
            );
        }
    }
}
