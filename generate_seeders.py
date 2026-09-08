import urllib.request
import json

url = "https://api.github.com/users/abbeymichael/repos?sort=updated&per_page=12"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
with urllib.request.urlopen(req) as response:
    data = json.loads(response.read().decode())

case_studies = []
portfolio_items = []

for i, repo in enumerate(data):
    name = repo.get('name', '').replace('-', ' ').title()
    desc = repo.get('description', '') or 'An open source project developed by Abbey Michael.'
    url = repo.get('html_url', '')
    homepage = repo.get('homepage', '') or url
    
    if i < 6:
        case_studies.append({
            'title': name.replace("'", "\\'"),
            'slug': repo.get('name', ''),
            'client_name': 'Open Source',
            'client_industry': 'Technology',
            'challenge': f'Develop a robust and scalable solution for {name}.'.replace("'", "\\'"),
            'solution': f'Implemented using modern web technologies. {desc}'.replace("'", "\\'"),
            'results': 'Successfully deployed and maintained on GitHub.',
            'status': 'published',
            'meta_title': name.replace("'", "\\'"),
            'meta_description': desc.replace("'", "\\'")[:150]
        })
    else:
        portfolio_items.append({
            'title': name.replace("'", "\\'"),
            'slug': repo.get('name', ''),
            'description': desc.replace("'", "\\'"),
            'github_url': url,
            'project_url': homepage,
            'status': 'published',
            'category': 'software',
            'sort_order': i
        })

cs_php = """<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $caseStudies = [
"""
for cs in case_studies:
    cs_php += f"""            [
                'title' => '{cs['title']}',
                'slug' => '{cs['slug']}',
                'client_name' => '{cs['client_name']}',
                'client_industry' => '{cs['client_industry']}',
                'challenge' => '{cs['challenge']}',
                'solution' => '{cs['solution']}',
                'results' => '{cs['results']}',
                'status' => '{cs['status']}',
                'published_at' => now(),
                'meta_title' => '{cs['meta_title']}',
                'meta_description' => '{cs['meta_description']}',
            ],
"""
cs_php += """        ];

        foreach ($caseStudies as $item) {
            CaseStudy::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
"""

pf_php = """<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
"""
for pf in portfolio_items:
    pf_php += f"""            [
                'title' => '{pf['title']}',
                'slug' => '{pf['slug']}',
                'description' => '{pf['description']}',
                'github_url' => '{pf['github_url']}',
                'project_url' => '{pf['project_url']}',
                'status' => '{pf['status']}',
                'category' => '{pf['category']}',
                'sort_order' => {pf['sort_order']},
            ],
"""
pf_php += """        ];

        foreach ($items as $item) {
            PortfolioItem::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
"""

with open('database/seeders/CaseStudySeeder.php', 'w') as f:
    f.write(cs_php)

with open('database/seeders/PortfolioSeeder.php', 'w') as f:
    f.write(pf_php)

