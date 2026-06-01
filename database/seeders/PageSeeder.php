<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * PageSeeder
 *
 * Seeds one Page record per front-facing public route.
 * Keys are matched 1-to-1 with route names so the admin
 * "Pages" index can map back to the live URL.
 *
 * Route map (routes/web.php public pages):
 *   home            → /home
 *   site.about      → /about
 *   site.services   → /services
 *   site.contact    → /contact
 *   site.products   → /products          (index; product-detail is dynamic)
 *   site.portfolio  → /portfolio         (index; portfolio-detail is dynamic)
 *   site.case-studies → /case-studies    (index; detail is dynamic)
 *   site.white-papers → /white-papers
 *   site.blog       → /blog              (index; blog-post is dynamic)
 *   site.consulting → /consulting
 *   site.book-demo  → /book-demo
 *   site.privacy-policy              → /privacy-policy
 *   site.terms-of-service            → /terms-of-service
 *   site.security                    → /security
 *   site.cookie-policy               → /cookie-policy
 *   site.data-processing-agreement   → /data-processing-agreement
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [

            // ── Home ────────────────────────────────────────────────────────
            [
                'key'              => 'home',
                'title'            => 'Exchosoft Consult — Software Development & Technology Consultancy',
                'banner_heading'   => 'Technology Consultancy Built on Real-World Experience',
                'banner_subheading'=> 'Ghana-Based · Africa · Caribbean · Diaspora',
                'meta_title'       => 'Exchosoft Consult — Custom Software & Technology Consultancy',
                'meta_description' => 'Exchosoft Consult is a Ghana-based software development and technology consultancy. We build offline-first, custom systems for Africa, the Caribbean, and the diaspora.',
                'meta_keywords'    => 'software development Ghana, technology consultancy Africa, offline-first software, custom software Ghana',
                'og_title'         => 'Exchosoft Consult — Technology Consultancy',
                'og_description'   => 'Custom software built for the real conditions of doing business across Africa, the Caribbean, and the diaspora.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/home',
            ],

            // ── About ────────────────────────────────────────────────────────
            [
                'key'              => 'about',
                'title'            => 'About Us — Exchosoft Consult',
                'banner_heading'   => 'Built From Here. Built For Here.',
                'banner_subheading'=> 'A Ghana-based technology consultancy that builds software for the real conditions of doing business across Africa, the Caribbean, and the diaspora.',
                'meta_title'       => 'About Exchosoft Consult — Our Story & Values',
                'meta_description' => 'Learn about Exchosoft Consult — a Ghana-based software development firm specialising in offline-first systems for Africa, the Caribbean, and the diaspora.',
                'meta_keywords'    => 'about Exchosoft, Ghana tech company, software consultancy Africa, offline-first',
                'og_title'         => 'About Exchosoft Consult',
                'og_description'   => 'Built From Here. Built For Here. Our story, values, and why we build software differently.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/about',
            ],

            // ── Services ─────────────────────────────────────────────────────
            [
                'key'              => 'services',
                'title'            => 'Our Services — Exchosoft Consult',
                'banner_heading'   => 'What We Do',
                'banner_subheading'=> 'From custom software development to full technology consulting — everything built for your specific context.',
                'meta_title'       => 'Services — Custom Software Development & Tech Consulting | Exchosoft',
                'meta_description' => 'Exchosoft offers custom software development, technology consulting, system architecture, digital transformation, and long-term tech partnership.',
                'meta_keywords'    => 'software development services, tech consulting Ghana, custom software Africa',
                'og_title'         => 'Services — Exchosoft Consult',
                'og_description'   => 'Custom software development, technology consulting, and digital transformation services for African and diaspora businesses.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/services',
            ],

            // ── Contact ──────────────────────────────────────────────────────
            [
                'key'              => 'contact',
                'title'            => 'Contact Us — Exchosoft Consult',
                'banner_heading'   => "Let's Talk",
                'banner_subheading'=> 'Tell us what you need. We\'ll tell you honestly if we can build it.',
                'meta_title'       => 'Contact Exchosoft Consult — Get in Touch',
                'meta_description' => 'Get in touch with Exchosoft Consult. We work with businesses across Africa, the Caribbean, and the diaspora. Tell us about your project.',
                'meta_keywords'    => 'contact Exchosoft, get in touch, software project enquiry Ghana',
                'og_title'         => 'Contact Exchosoft Consult',
                'og_description'   => "Tell us what you need. We'll tell you honestly if we can build it.",
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/contact',
            ],

            // ── Products (index) ─────────────────────────────────────────────
            [
                'key'              => 'products',
                'title'            => 'Our Software Products — Exchosoft Consult',
                'banner_heading'   => 'Products Built for African Businesses',
                'banner_subheading'=> 'Offline-first, custom-built software products for healthcare, faith, laundry, and more.',
                'meta_title'       => 'Software Products — Exchosoft Consult',
                'meta_description' => 'Explore Exchosoft\'s range of offline-first software products — WashOps, ChurchOps, and more. Built for the real conditions of African businesses.',
                'meta_keywords'    => 'software products Ghana, WashOps, ChurchOps, offline software Africa',
                'og_title'         => 'Software Products — Exchosoft Consult',
                'og_description'   => 'Offline-first software products built for African businesses — laundry, church, healthcare, and more.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/products',
            ],

            // ── Portfolio (index) ────────────────────────────────────────────
            [
                'key'              => 'portfolio',
                'title'            => 'Our Portfolio — Exchosoft Consult',
                'banner_heading'   => 'Work We\'re Proud Of',
                'banner_subheading'=> 'Real systems, real clients, real outcomes — across healthcare, finance, heritage, and more.',
                'meta_title'       => 'Portfolio — Exchosoft Consult',
                'meta_description' => 'Browse the Exchosoft Consult portfolio. Real projects across healthcare, faith, finance, laundry, heritage, and more — all built in Africa for African conditions.',
                'meta_keywords'    => 'Exchosoft portfolio, software projects Ghana, tech portfolio Africa',
                'og_title'         => 'Portfolio — Exchosoft Consult',
                'og_description'   => 'Real systems built for real clients across Africa, the Caribbean, and the diaspora.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/portfolio',
            ],

            // ── Case Studies (index) ─────────────────────────────────────────
            [
                'key'              => 'case-studies',
                'title'            => 'Case Studies — Exchosoft Consult',
                'banner_heading'   => 'How We Solved It',
                'banner_subheading'=> 'In-depth looks at the problems we\'ve tackled and the systems we built to solve them.',
                'meta_title'       => 'Case Studies — Exchosoft Consult',
                'meta_description' => 'Read Exchosoft case studies — detailed accounts of how we approached and solved complex technology challenges for clients across Africa and the Caribbean.',
                'meta_keywords'    => 'case studies software Ghana, tech case study Africa, Exchosoft case study',
                'og_title'         => 'Case Studies — Exchosoft Consult',
                'og_description'   => 'In-depth looks at the problems we\'ve tackled and the systems we built to solve them.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/case-studies',
            ],

            // ── White Papers ─────────────────────────────────────────────────
            [
                'key'              => 'white-papers',
                'title'            => 'White Papers — Exchosoft Consult',
                'banner_heading'   => 'Research & Technical Thinking',
                'banner_subheading'=> 'Our published thinking on technology, architecture, and building software for emerging markets.',
                'meta_title'       => 'White Papers — Exchosoft Consult',
                'meta_description' => 'Download Exchosoft white papers on offline-first architecture, software development in Africa, and technology for emerging markets.',
                'meta_keywords'    => 'white papers technology Africa, offline-first architecture, Exchosoft research',
                'og_title'         => 'White Papers — Exchosoft Consult',
                'og_description'   => 'Research and technical writing on building software for the conditions of doing business in Africa.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/white-papers',
            ],

            // ── Blog (index) ─────────────────────────────────────────────────
            [
                'key'              => 'blog',
                'title'            => 'Tech Blog — Exchosoft Consult',
                'banner_heading'   => 'From the Exchosoft Blog',
                'banner_subheading'=> 'Practical insights on software development, technology consulting, and building in emerging markets.',
                'meta_title'       => 'Tech Blog — Exchosoft Consult',
                'meta_description' => 'The Exchosoft blog — articles on software development, offline-first architecture, technology consulting, and doing tech in Africa.',
                'meta_keywords'    => 'tech blog Ghana, software development blog Africa, Exchosoft articles',
                'og_title'         => 'Tech Blog — Exchosoft Consult',
                'og_description'   => 'Practical insights from the Exchosoft team on software, technology, and building in Africa.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/blog',
            ],

            // ── Consulting ───────────────────────────────────────────────────
            [
                'key'              => 'consulting',
                'title'            => 'Consulting Services — Exchosoft Consult',
                'banner_heading'   => 'Technology Consulting That Tells the Truth',
                'banner_subheading'=> 'We help businesses understand exactly what technology they need — and what they don\'t.',
                'meta_title'       => 'Technology Consulting — Exchosoft Consult',
                'meta_description' => 'Exchosoft technology consulting — system audits, architecture advice, digital transformation, and honest technology guidance for African businesses.',
                'meta_keywords'    => 'technology consulting Ghana, digital transformation Africa, IT consulting Accra',
                'og_title'         => 'Consulting — Exchosoft Consult',
                'og_description'   => 'Honest technology consulting for African businesses — what you need and what you don\'t.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/consulting',
            ],

            // ── Book Demo ────────────────────────────────────────────────────
            [
                'key'              => 'book-demo',
                'title'            => 'Book a Free Demo — Exchosoft Consult',
                'banner_heading'   => 'See Our Software in Action',
                'banner_subheading'=> 'Book a live demonstration and see how our platforms handle your specific industry\'s challenges.',
                'meta_title'       => 'Book a Free Demo — Exchosoft Consult',
                'meta_description' => 'Book a free live demo of Exchosoft software. See WashOps, ChurchOps, or any of our platforms in action — tailored to your industry.',
                'meta_keywords'    => 'book demo software Ghana, free software demo Africa, Exchosoft demo',
                'og_title'         => 'Book a Free Demo — Exchosoft Consult',
                'og_description'   => 'Schedule a live demonstration of our software, tailored to your industry and business context.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'canonical_url'    => '/book-demo',
            ],

            // ── Legal: Privacy Policy ────────────────────────────────────────
            [
                'key'              => 'privacy-policy',
                'title'            => 'Privacy Policy — Exchosoft Consult',
                'banner_heading'   => 'Privacy Policy',
                'banner_subheading'=> 'How we collect, use, and protect your personal information.',
                'meta_title'       => 'Privacy Policy — Exchosoft Consult',
                'meta_description' => 'Read the Exchosoft Consult privacy policy. Learn how we collect, use, store, and protect your personal information.',
                'meta_keywords'    => 'Exchosoft privacy policy, data privacy Ghana',
                'og_title'         => 'Privacy Policy — Exchosoft Consult',
                'og_description'   => 'How Exchosoft collects, uses, and protects your personal information.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary',
                'canonical_url'    => '/privacy-policy',
                'extra'            => ['section' => 'legal'],
            ],

            // ── Legal: Terms of Service ──────────────────────────────────────
            [
                'key'              => 'terms-of-service',
                'title'            => 'Terms of Service — Exchosoft Consult',
                'banner_heading'   => 'Terms of Service',
                'banner_subheading'=> 'The terms that govern your use of Exchosoft products and services.',
                'meta_title'       => 'Terms of Service — Exchosoft Consult',
                'meta_description' => 'Read the Exchosoft Consult terms of service governing use of our website, software products, and consulting services.',
                'meta_keywords'    => 'Exchosoft terms of service, terms and conditions',
                'og_title'         => 'Terms of Service — Exchosoft Consult',
                'og_description'   => 'Terms governing your use of Exchosoft products and services.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary',
                'canonical_url'    => '/terms-of-service',
                'extra'            => ['section' => 'legal'],
            ],

            // ── Legal: Security ──────────────────────────────────────────────
            [
                'key'              => 'security',
                'title'            => 'Security — Exchosoft Consult',
                'banner_heading'   => 'Our Security Commitment',
                'banner_subheading'=> 'How we protect your data and keep our systems secure.',
                'meta_title'       => 'Security — Exchosoft Consult',
                'meta_description' => 'Read about Exchosoft\'s approach to security — how we protect client data, manage vulnerabilities, and maintain secure systems.',
                'meta_keywords'    => 'Exchosoft security, software security Ghana, data protection',
                'og_title'         => 'Security — Exchosoft Consult',
                'og_description'   => 'Our commitment to protecting your data and keeping our systems secure.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary',
                'canonical_url'    => '/security',
                'extra'            => ['section' => 'legal'],
            ],

            // ── Legal: Cookie Policy ─────────────────────────────────────────
            [
                'key'              => 'cookie-policy',
                'title'            => 'Cookie Policy — Exchosoft Consult',
                'banner_heading'   => 'Cookie Policy',
                'banner_subheading'=> 'How we use cookies and similar tracking technologies on our website.',
                'meta_title'       => 'Cookie Policy — Exchosoft Consult',
                'meta_description' => 'Read the Exchosoft cookie policy — what cookies we use, why we use them, and how you can manage your preferences.',
                'meta_keywords'    => 'Exchosoft cookie policy, cookies Ghana website',
                'og_title'         => 'Cookie Policy — Exchosoft Consult',
                'og_description'   => 'How we use cookies and how you can manage your preferences.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary',
                'canonical_url'    => '/cookie-policy',
                'extra'            => ['section' => 'legal'],
            ],

            // ── Legal: Data Processing Agreement ─────────────────────────────
            [
                'key'              => 'data-processing-agreement',
                'title'            => 'Data Processing Agreement — Exchosoft Consult',
                'banner_heading'   => 'Data Processing Agreement',
                'banner_subheading'=> 'The terms under which Exchosoft processes personal data on behalf of clients.',
                'meta_title'       => 'Data Processing Agreement — Exchosoft Consult',
                'meta_description' => 'Read the Exchosoft Data Processing Agreement (DPA) — the terms under which we process personal data on behalf of clients and partners.',
                'meta_keywords'    => 'DPA, data processing agreement, GDPR Ghana, Exchosoft data',
                'og_title'         => 'Data Processing Agreement — Exchosoft Consult',
                'og_description'   => 'Terms under which Exchosoft processes personal data on behalf of clients.',
                'og_type'          => 'website',
                'twitter_card'     => 'summary',
                'canonical_url'    => '/data-processing-agreement',
                'extra'            => ['section' => 'legal'],
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }

        $this->command->info('✅ PageSeeder: ' . count($pages) . ' pages seeded / updated.');
    }
}
