<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $caseStudies = [
            [
                'title' => 'Exchosoftsite',
                'slug' => 'exchosoftsite',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Exchosoftsite.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Exchosoftsite',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
            [
                'title' => 'Damiigames',
                'slug' => 'damiigames',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Damiigames.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Damiigames',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
            [
                'title' => 'Iconsofghana',
                'slug' => 'iconsofghana',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Iconsofghana.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Iconsofghana',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
            [
                'title' => 'Thetrain',
                'slug' => 'TheTrain',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Thetrain.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Thetrain',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
            [
                'title' => 'Matched',
                'slug' => 'matched',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Matched.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Matched',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
            [
                'title' => 'Oneghana',
                'slug' => 'oneghana',
                'client_name' => 'Open Source',
                'client_industry' => 'Technology',
                'challenge' => 'Develop a robust and scalable solution for Oneghana.',
                'solution' => 'Implemented using modern web technologies. An open source project developed by Abbey Michael.',
                'results' => 'Successfully deployed and maintained on GitHub.',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Oneghana',
                'meta_description' => 'An open source project developed by Abbey Michael.',
            ],
        ];

        foreach ($caseStudies as $item) {
            CaseStudy::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
