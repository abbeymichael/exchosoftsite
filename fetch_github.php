<?php
$url = "https://api.github.com/users/abbeymichael/repos?sort=updated&per_page=10";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Exchosoft');
$result = curl_exec($ch);
curl_close($ch);

$repos = json_decode($result, true);

$caseStudies = [];
$portfolioItems = [];

foreach ($repos as $index => $repo) {
    if ($index < 5) {
        $caseStudies[] = [
            'title' => ucwords(str_replace('-', ' ', $repo['name'])),
            'client_name' => 'Open Source',
            'client_industry' => 'Technology',
            'challenge' => 'Develop a robust and scalable solution for ' . $repo['name'] . '.',
            'solution' => 'Implemented using modern web technologies. ' . ($repo['description'] ?? ''),
            'results' => 'Successfully deployed and maintained on GitHub.',
            'status' => 'published',
        ];
    } else {
        $portfolioItems[] = [
            'title' => ucwords(str_replace('-', ' ', $repo['name'])),
            'description' => $repo['description'] ?? 'An open source project developed by Abbey Michael.',
            'github_url' => $repo['html_url'],
            'project_url' => $repo['homepage'] ?? $repo['html_url'],
            'status' => 'published',
            'category' => 'software',
            'sort_order' => $index,
        ];
    }
}

file_put_contents('github_data.json', json_encode(['caseStudies' => $caseStudies, 'portfolioItems' => $portfolioItems]));
