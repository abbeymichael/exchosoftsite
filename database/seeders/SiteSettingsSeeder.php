<?php

namespace Database\Seeders;

use App\Models\ProductPageSection;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // ── HOMEPAGE SETTINGS ────────────────────────────────────────────────

        $homepage = [
            // Hero
            ['key' => 'home_hero_tag',     'value' => 'Ghana-Based · Africa · Caribbean · Diaspora', 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Tag Line'],
            ['key' => 'home_hero_title',   'value' => 'Technology Consultancy Built on **Real-World** Experience', 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Title (use **text** for cyan highlight)'],
            ['key' => 'home_hero_subtitle','value' => "We're a software development and consultancy firm serving Black businesses across Africa, the Caribbean, and the diaspora—building custom solutions that work in your reality, not just in theory.", 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Subtitle'],
            ['key' => 'home_hero_btn_primary_label', 'value' => 'Talk to Us', 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Primary Button Label'],
            ['key' => 'home_hero_btn_secondary_label', 'value' => 'Our Products', 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Secondary Button Label'],

            // Stats bar (JSON array of {num, label})
            ['key' => 'home_stats', 'value' => json_encode([
                ['num' => '10+',     'label' => 'Industries served'],
                ['num' => '3',       'label' => 'Continents reached'],
                ['num' => '100%',    'label' => 'Custom-built solutions'],
                ['num' => 'Offline', 'label' => 'First architecture'],
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'Stats Bar (JSON)'],

            // Who We Are section
            ['key' => 'home_about_tag',     'value' => 'Who We Are', 'type' => 'text', 'group' => 'homepage', 'label' => 'About Section Tag'],
            ['key' => 'home_about_title',   'value' => 'Built for the Conditions You Actually Operate In', 'type' => 'text', 'group' => 'homepage', 'label' => 'About Section Title'],
            ['key' => 'home_about_content', 'value' => "Exchosoft Consult is a Ghana-based technology consultancy and software development company. We've built systems for churches, hospitals, pharmacies, laboratories, laundries, heritage organizations, and more—each one custom-designed for that specific business.\n\nWe understand the conditions our clients operate in because we're here too.", 'type' => 'markdown', 'group' => 'homepage', 'label' => 'About Section Content (Markdown)'],
            ['key' => 'home_about_cards', 'value' => json_encode([
                ['title' => 'Intermittent connectivity', 'body' => 'We build systems that keep working when the internet drops.'],
                ['title' => 'Power challenges',          'body' => 'Offline-first architecture means no data is lost during outages.'],
                ['title' => 'Mobile-first users',       'body' => 'Designed from the ground up for how your customers actually access technology.'],
                ['title' => 'Local payment systems',    'body' => 'Integrated with the payment infrastructure your market already uses.'],
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'About Reality Cards (JSON)'],

            // Products section
            ['key' => 'home_products_tag',   'value' => 'Our Software',                   'type' => 'text', 'group' => 'homepage', 'label' => 'Products Section Tag'],
            ['key' => 'home_products_title', 'value' => 'Products Built for African Businesses', 'type' => 'text', 'group' => 'homepage', 'label' => 'Products Section Title'],

            // Approach section
            ['key' => 'home_approach_tag',   'value' => 'Our Approach', 'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Section Tag'],
            ['key' => 'home_approach_title', 'value' => "What We've Learned Building Software Across Industries", 'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Section Title'],
            ['key' => 'home_approach_cards', 'value' => json_encode([
                ['icon' => 'grid',    'title' => 'Every Business Needs Its Own Solution',   'body' => 'Off-the-shelf software forces unacceptable compromises. Each business has unique workflows, and they deserve technology built specifically for how they operate.'],
                ['icon' => 'offline', 'title' => 'Offline-First When It Matters',          'body' => "We pioneered offline-first architecture for clients who can't afford downtime — hospitals, pharmacies, churches — with automatic cloud sync when online."],
                ['icon' => 'data',    'title' => 'Unified Systems, Clear Insights',        'body' => 'We unify business workflows into cohesive systems that give management complete visibility and analytics that clearly identify where improvements are needed.'],
                ['icon' => 'lan',     'title' => 'LAN Collaboration',                      'body' => 'For businesses with multiple locations or devices, we implement local network capabilities — real-time collaboration even when external connectivity fails.'],
                ['icon' => 'shield',  'title' => 'Security & Reliability',                 'body' => 'From financial institutions to healthcare providers, we build with security baked in — not bolted on — because your clients\' data deserves that standard.'],
                ['icon' => 'partner', 'title' => 'Long-Term Partnership',                  'body' => "We're not just building software — we're building systems that grow with your business and adapt as your needs change. We stay involved."],
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'Approach Cards (JSON)'],

            // Industries section
            ['key' => 'home_industries_tag',   'value' => 'Experience',          'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Section Tag'],
            ['key' => 'home_industries_title', 'value' => "Industries We've Served", 'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Section Title'],
            ['key' => 'home_industries_cards', 'value' => json_encode([
                ['title' => 'Healthcare & Medical',        'body' => 'Hospital management systems, pharmacy solutions, and laboratory platforms — offline-first, designed to work when connectivity doesn\'t.'],
                ['title' => 'Faith-Based Organizations',  'body' => 'Comprehensive management systems for churches covering membership, events, donations, multi-branch, and SMS communication.'],
                ['title' => 'Laundry & Dry Cleaning',     'body' => 'End-to-end laundry business management with order tracking, customer management, and multi-branch capabilities.'],
                ['title' => 'Heritage & Culture',         'body' => 'Digital preservation and management tools for cultural organizations, archives, and heritage institutions.'],
                ['title' => 'Finance & Microfinance',     'body' => 'Secure financial management systems with loan tracking, savings, and reporting designed for local institutions.'],
                ['title' => 'Retail & Distribution',      'body' => 'Inventory, point-of-sale, and supply chain systems built for the realities of retail in African markets.'],
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'Industries Cards (JSON)'],

            // Why Us section
            ['key' => 'home_why_tag',   'value' => 'Why Exchosoft',       'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Section Tag'],
            ['key' => 'home_why_title', 'value' => 'The Exchosoft Difference', 'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Section Title'],
            ['key' => 'home_why_items', 'value' => json_encode([
                ['title' => 'Built Here, For Here',         'body' => "We operate in the same environment as our clients. We know the connectivity issues, the power challenges, and the payment systems because we live them."],
                ['title' => 'No Generic Solutions',         'body' => 'Every engagement starts from scratch. We study your business, understand your unique needs, and build specifically for you.'],
                ['title' => 'Offline-First by Default',     'body' => 'Our software keeps working through power outages and internet disruptions — no data loss, no downtime.'],
                ['title' => 'Long-Term Relationships',      'body' => "We don't just deliver and disappear. We stay involved, providing support, updates, and evolution as your business grows."],
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'Why Us Items (JSON)'],

            // Trust / clients section
            ['key' => 'home_trust_tag',     'value' => 'Trusted By',                                                'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Section Tag'],
            ['key' => 'home_trust_title',   'value' => 'Organisations That Trust Exchosoft',                        'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Section Title'],
            ['key' => 'home_trust_subtitle','value' => 'We\'ve delivered solutions across a range of industries and business sizes.',  'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Section Subtitle'],
            ['key' => 'home_trust_clients', 'value' => json_encode([
                'Healthcare Facilities', 'Church Networks', 'Laundry Businesses',
                'Financial Institutions', 'Heritage Organisations', 'Retail Chains',
                'Educational Institutions', 'Government Agencies',
            ]), 'type' => 'json', 'group' => 'homepage', 'label' => 'Client Pills (JSON array of strings)'],

            // CTA section
            ['key' => 'home_cta_title',    'value' => 'Ready to Build Something That Actually Works?', 'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Section Title'],
            ['key' => 'home_cta_subtitle', 'value' => "Tell us what you need. We'll tell you honestly if we can build it — and if we can, we'll do it right.", 'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Section Subtitle'],
            ['key' => 'home_cta_btn',      'value' => 'Start a Conversation', 'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Button Label'],
            ['key' => 'home_cta_email_note','value' => 'Or email us directly at hello@exchosoft.com', 'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Email Note'],

            // Demo CTA
            ['key' => 'home_demo_cta_title',    'value' => 'See Our Software in Action', 'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Title'],
            ['key' => 'home_demo_cta_subtitle', 'value' => "Book a live demonstration and see how our platforms handle your specific industry's challenges.", 'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Subtitle'],
        ];

        foreach ($homepage as $item) {
            SiteSetting::updateOrCreate(['key' => $item['key']], $item);
        }

        // ── PRODUCT PAGE SECTIONS ────────────────────────────────────────────

        $washHeroContent = <<<'MD'
## Complete Laundry Management for **Modern Businesses**

Enterprise-grade desktop application with powerful POS, real-time analytics, automated workflows, and cloud synchronization. Everything you need to run and scale your laundry business.
MD;

        $churchHeroContent = <<<'MD'
## Complete Church Management for **Growing Congregations**

Offline-first church management platform built for African realities. Manage members, finances, events, communications, and multi-branch operations from a single unified system.
MD;

        $sections = [
            // WashOps
            [
                'product_code' => 'washops',
                'section_key'  => 'hero',
                'label'        => 'WashOps Hero Section',
                'content'      => $washHeroContent,
                'data'         => [
                    'badge'      => 'WashOps',
                    'btn_primary_label'    => 'Start Free Trial',
                    'btn_secondary_label'  => 'Read White Paper',
                    'badge_class'  => 'badge-wash',
                    'theme'        => 'wash',
                    'accent_class' => 'accent-wash',
                ],
                'type'        => 'markdown',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'product_code' => 'washops',
                'section_key'  => 'features',
                'label'        => 'WashOps Features',
                'content'      => 'Powerful Features Built for Laundry Businesses',
                'data'         => [
                    'subtitle' => 'Everything you need to manage orders, delight customers, and grow your business — all in one place.',
                    'bg_class' => 'bg-ice',
                    'features' => [
                        ['icon' => '📊', 'title' => 'Analytics Dashboard',      'items' => ['Revenue tracking and forecasting', 'Order volume analytics', 'Daily bottleneck identification', 'Staff performance metrics', 'Customer behavior insights', 'Custom date range reporting']],
                        ['icon' => '🖥️', 'title' => 'Advanced Point of Sale',   'items' => ['Quick order intake with pricing rules', 'Multi-item order management', 'Custom pricing tiers per customer', 'Receipt printing and SMS notifications', 'Barcode scanning support', 'Discount and coupon handling']],
                        ['icon' => '📋', 'title' => 'Kanban Order Management',  'items' => ['Visual order pipeline (Intake → Processing → Ready → Delivered)', 'Drag-and-drop status updates', 'Priority flagging for urgent orders', 'Staff assignment per order', 'Real-time order status updates', 'Customer notification on completion']],
                        ['icon' => '☁️', 'title' => 'Cloud Synchronization',   'items' => ['Automatic sync when internet available', 'Full offline capability', 'Multi-branch data consolidation', 'Conflict resolution engine', 'Audit trail for all sync events', 'Real-time backup to secure cloud']],
                        ['icon' => '👥', 'title' => 'Customer Management',      'items' => ['Customer profiles with order history', 'Loyalty points system', 'Customer segmentation', 'Automated birthday promotions', 'Bulk SMS campaigns', 'Customer satisfaction tracking']],
                        ['icon' => '💰', 'title' => 'Financial Management',     'items' => ['Daily revenue reconciliation', 'Expense tracking', 'Staff commission calculation', 'Profit and loss reporting', 'Multi-payment method support', 'Export to accounting software']],
                    ],
                ],
                'type'       => 'json',
                'sort_order' => 2,
                'is_active'  => true,
            ],
            [
                'product_code' => 'washops',
                'section_key'  => 'roi',
                'label'        => 'WashOps ROI Callout',
                'content'      => null,
                'data'         => [
                    'number'   => '40%',
                    'title'    => 'Average Revenue Increase',
                    'subtitle' => 'Laundry businesses that implement WashOps typically see 40% revenue growth within the first 6 months through improved capacity utilization, reduced losses, and better customer retention.',
                ],
                'type'       => 'json',
                'sort_order' => 3,
                'is_active'  => true,
            ],
            // ChurchOps
            [
                'product_code' => 'churchops',
                'section_key'  => 'hero',
                'label'        => 'ChurchOps Hero Section',
                'content'      => $churchHeroContent,
                'data'         => [
                    'badge'      => 'ChurchOps',
                    'btn_primary_label'   => 'Request Demo',
                    'btn_secondary_label' => 'View Features',
                    'badge_class'  => 'badge-church',
                    'theme'        => 'church',
                    'accent_class' => 'accent-church',
                ],
                'type'       => 'markdown',
                'sort_order' => 1,
                'is_active'  => true,
            ],
            [
                'product_code' => 'churchops',
                'section_key'  => 'features',
                'label'        => 'ChurchOps Features',
                'content'      => 'Comprehensive Church Management Features',
                'data'         => [
                    'subtitle' => 'Everything your church needs to grow, connect, and serve your congregation effectively.',
                    'bg_class' => 'bg-church',
                    'features' => [
                        ['icon' => '👥', 'title' => 'Member Management',          'items' => ['Complete member profiles with photos', 'Family unit management', 'Attendance tracking per service', 'Membership class management', 'Visitor follow-up system', 'Member directory and search']],
                        ['icon' => '💰', 'title' => 'Financial Management',       'items' => ['Tithe and offering recording', 'Multi-fund management', 'Pledge tracking and reminders', 'Financial reporting and statements', 'Bank reconciliation', 'Budget planning and tracking']],
                        ['icon' => '📱', 'title' => 'Communications',             'items' => ['Bulk SMS to members or groups', 'Email newsletter system', 'Automated birthday messages', 'Service reminders and announcements', 'WhatsApp integration', 'Multi-language message support']],
                        ['icon' => '🏛️', 'title' => 'Multi-Branch Management',   'items' => ['Unlimited branch/campus support', 'Centralized reporting across branches', 'Consolidated financial view', 'Branch-specific settings', 'Staff access control per branch', 'Inter-branch transfer management']],
                        ['icon' => '📅', 'title' => 'Events & Programs',          'items' => ['Event scheduling and management', 'Volunteer coordination', 'Resource booking system', 'Registration and RSVP tracking', 'Post-event reporting', 'Integration with communication tools']],
                        ['icon' => '📊', 'title' => 'Reports & Analytics',        'items' => ['Attendance trend analysis', 'Giving pattern reports', 'Member growth analytics', 'Departmental activity reports', 'Year-over-year comparisons', 'Export to Excel and PDF']],
                    ],
                ],
                'type'       => 'json',
                'sort_order' => 2,
                'is_active'  => true,
            ],
            [
                'product_code' => 'churchops',
                'section_key'  => 'roi',
                'label'        => 'ChurchOps Impact Callout',
                'content'      => null,
                'data'         => [
                    'number'   => '60%',
                    'title'    => 'Admin Time Saved',
                    'subtitle' => 'Church administrators using ChurchOps report spending 60% less time on administrative tasks, freeing them to focus on ministry and community engagement.',
                ],
                'type'       => 'json',
                'sort_order' => 3,
                'is_active'  => true,
            ],
        ];

        foreach ($sections as $section) {
            ProductPageSection::upsertSection($section['product_code'], $section['section_key'], $section);
        }

        $this->command->info('✅ Site settings and product page sections seeded.');
    }
}
