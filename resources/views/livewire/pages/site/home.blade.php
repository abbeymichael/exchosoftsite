<?php

use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Models\ShopProduct;
use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Exchosoft Consult — Software Development & Technology Consultancy')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        $featuredProducts = ShopProduct::published()->featured()->orderBy('sort_order')->limit(3)->get();
        $latestPosts      = BlogPost::published()->latest('published_at')->limit(3)->get();
        $featuredCases    = CaseStudy::published()->featured()->limit(3)->get();
        $featuredWork     = PortfolioItem::published()->featured()->orderBy('sort_order')->limit(4)->get();

        // Load all homepage settings from DB
        $s = SiteSetting::getGroup('homepage');

        // Helper to parse JSON safely
        $j = fn($key, $default = []) => isset($s[$key]) ? (is_string($s[$key]) ? (json_decode($s[$key], true) ?? $default) : $s[$key]) : $default;

        $cms = [
            // Hero
            'hero_tag'           => $s['home_hero_tag'] ?? 'Ghana-Based · Africa · Caribbean · Diaspora',
            'hero_title_raw'     => $s['home_hero_title'] ?? 'Technology Consultancy Built on **Real-World** Experience',
            'hero_subtitle'      => $s['home_hero_subtitle'] ?? "We're a software development and consultancy firm serving Black businesses across Africa, the Caribbean, and the diaspora.",
            'hero_btn_primary'   => $s['home_hero_btn_primary_label'] ?? 'Talk to Us',
            'hero_btn_secondary' => $s['home_hero_btn_secondary_label'] ?? 'Our Products',
            // Stats
            'stats' => $j('home_stats', [
                ['num' => '10+', 'label' => 'Industries served'],
                ['num' => '3',   'label' => 'Continents reached'],
                ['num' => '100%','label' => 'Custom-built solutions'],
                ['num' => 'Offline','label' => 'First architecture'],
            ]),
            // About
            'about_tag'     => $s['home_about_tag'] ?? 'Who We Are',
            'about_title'   => $s['home_about_title'] ?? 'Built for the Conditions You Actually Operate In',
            'about_content' => $s['home_about_content'] ?? '',
            'about_cards'   => $j('home_about_cards', [
                ['title' => 'Intermittent connectivity', 'body' => 'We build systems that keep working when the internet drops.'],
                ['title' => 'Power challenges',          'body' => 'Offline-first architecture means no data is lost during outages.'],
                ['title' => 'Mobile-first users',        'body' => 'Designed from the ground up for how your customers actually access technology.'],
                ['title' => 'Local payment systems',     'body' => 'Integrated with the payment infrastructure your market already uses.'],
            ]),
            // Products
            'products_tag'   => $s['home_products_tag'] ?? 'Our Software',
            'products_title' => $s['home_products_title'] ?? 'Products Built for African Businesses',
            // Approach
            'approach_tag'   => $s['home_approach_tag'] ?? 'Our Approach',
            'approach_title' => $s['home_approach_title'] ?? "What We've Learned Building Software Across Industries",
            'approach_cards' => $j('home_approach_cards', []),
            // Industries
            'industries_tag'   => $s['home_industries_tag'] ?? 'Experience',
            'industries_title' => $s['home_industries_title'] ?? "Industries We've Served",
            'industries_cards' => $j('home_industries_cards', []),
            // Why Us
            'why_tag'   => $s['home_why_tag'] ?? 'Why Exchosoft',
            'why_title' => $s['home_why_title'] ?? 'The Exchosoft Difference',
            'why_items' => $j('home_why_items', []),
            // Trust
            'trust_tag'     => $s['home_trust_tag'] ?? 'Trusted By',
            'trust_title'   => $s['home_trust_title'] ?? 'Organisations That Trust Exchosoft',
            'trust_subtitle'=> $s['home_trust_subtitle'] ?? '',
            'trust_clients' => $j('home_trust_clients', ['Healthcare Facilities','Church Networks','Laundry Businesses','Financial Institutions']),
            // CTA
            'cta_title'      => $s['home_cta_title'] ?? 'Ready to Build Something That Actually Works?',
            'cta_subtitle'   => $s['home_cta_subtitle'] ?? "Tell us what you need. We'll tell you honestly if we can build it.",
            'cta_btn'        => $s['home_cta_btn'] ?? 'Start a Conversation',
            'cta_email_note' => $s['home_cta_email_note'] ?? '',
            // Demo CTA
            'demo_cta_title'    => $s['home_demo_cta_title'] ?? 'See Our Software in Action',
            'demo_cta_subtitle' => $s['home_demo_cta_subtitle'] ?? "Book a live demonstration and see how our platforms handle your specific industry's challenges.",
        ];

        return view('livewire.pages.site.home', compact('featuredProducts', 'latestPosts', 'featuredCases', 'featuredWork', 'cms'));
    }
}; ?>

<div>
<style>
  /* ── HOME PAGE STYLES ── */
  .home-hero {
    min-height: 100vh;
    background: #08121d;
    display: grid;
    grid-template-columns: 1fr 1fr;
    position: relative;
    overflow: hidden;
  }
  .hero-bg-pattern {
    position: absolute; inset: 0;
    background-image:
      radial-gradient(circle at 70% 50%, rgba(0,184,219,0.12) 0%, transparent 60%),
      radial-gradient(circle at 20% 80%, rgba(122,207,232,0.08) 0%, transparent 50%);
    pointer-events: none;
  }
  .hero-grid-lines {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(0,184,219,0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0,184,219,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
  }
  .hero-content {
    display: flex; flex-direction: column; justify-content: center;
    padding: 8rem 4rem 6rem 6rem;
    position: relative; z-index: 2;
  }
  .hero-tag {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(0,184,219,0.12); border: 1px solid rgba(0,184,219,0.25);
    color: var(--cyan); padding: 0.35rem 0.9rem; border-radius: 100px;
    font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em;
    margin-bottom: 2rem; width: fit-content;
  }
  .hero-tag span { width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; display: block; }
  .hero-h1 {
    font-family: var(--font-display);
    font-size: clamp(2.4rem, 4.5vw, 3.8rem);
    font-weight: 800; color: var(--white);
    line-height: 1.1; letter-spacing: -0.03em;
    margin-bottom: 1.5rem;
  }
  .hero-h1 em { color: var(--cyan); font-style: normal; }
  .hero-sub {
    font-size: 1.05rem; color: rgba(255,255,255,0.65);
    max-width: 480px; margin-bottom: 2.5rem; line-height: 1.7; font-weight: 300;
  }
  .hero-buttons { display: flex; gap: 1rem; flex-wrap: wrap; }
  .btn-primary-cyan {
    background: var(--cyan); color: var(--white);
    padding: 0.85rem 2rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 0.95rem; font-weight: 600;
    text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block;
  }
  .btn-primary-cyan:hover { background: var(--cyan-dark); transform: translateY(-1px); }
  .btn-secondary-outline {
    background: transparent; color: var(--white);
    padding: 0.85rem 2rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);
    font-family: var(--font-display); font-size: 0.95rem; font-weight: 600;
    text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block;
  }
  .btn-secondary-outline:hover { border-color: var(--cyan); background: rgba(0,184,219,0.08); }
  .hero-visual {
    display: flex; align-items: center; justify-content: center;
    padding: 4rem; position: relative; z-index: 2;
  }
  .hero-logo-wrap {
    width: 420px; height: 420px;
    display: flex; align-items: center; justify-content: center;
    position: relative;
  }
  /* Core logo */
  .orbit-core {
    width: 140px; height: 140px;
    background: rgba(8, 18, 29, 0.95);
    border: 1px solid rgba(0,184,219,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    position: relative; z-index: 20;
    box-shadow: 0 0 50px rgba(0, 184, 219, 0.1);
  }
  .orbit-core img { width: 88px; height: auto; position: relative; z-index: 2; }
  .orbit-core-fallback {
    font-family: var(--font-display); font-size: 2.5rem; font-weight: 800;
    color: var(--cyan); position: relative; z-index: 2;
  }
  /* Radar canvas container */
  .radar-container {
    position: absolute; inset: -40px;
    pointer-events: none; z-index: 1;
    display: flex; align-items: center; justify-content: center;
  }
  #radarSweep { width: 500px; height: 500px; }
  /* Icon nodes on ring */
  .orbit-icon {
    position: absolute;
    width: 48px; height: 48px;
    background: rgba(13, 33, 55, 0.8);
    border: 1px solid rgba(0, 184, 219, 0.15);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    top: 50%; left: 50%;
    margin-left: -24px; margin-top: -24px;
    transition: box-shadow 0.25s, border-color 0.25s, background 0.25s;
    z-index: 30;
    backdrop-filter: blur(4px);
  }
  .orbit-icon svg {
    width: 22px; height: 22px;
    fill: none; stroke: var(--cyan);
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
    opacity: 0.6;
    transition: opacity 0.2s, filter 0.2s;
  }
  .orbit-icon::after {
    content: attr(data-label);
    position: absolute; bottom: -28px;
    left: 50%; transform: translateX(-50%);
    background: rgba(0, 184, 219, 0.15);
    border: 1px solid rgba(0, 184, 219, 0.2);
    color: var(--cyan); font-size: 0.65rem;
    font-family: var(--font-display); font-weight: 600;
    padding: 2px 8px; border-radius: 4px;
    white-space: nowrap; opacity: 0;
    pointer-events: none; transition: opacity 0.3s;
  }
  .orbit-icon:hover::after { opacity: 1; }

  /* STATS BAR */
  .stats-bar {
    background: var(--ice);
    border-bottom: 1px solid var(--border);
    padding: 2.5rem 6rem;
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }
  .stat { text-align: center; }
  .stat-num {
    font-family: var(--font-display); font-size: 2rem; font-weight: 800;
    color: var(--cyan); letter-spacing: -0.03em;
  }
  .stat-label { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; }

  /* SECTION BASE */
  .home-section { padding: 6rem; }
  .section-tag-label {
    font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em;
    color: var(--cyan); text-transform: uppercase; margin-bottom: 1rem;
  }
  .section-h2 {
    font-family: var(--font-display); font-size: clamp(1.8rem, 3vw, 2.6rem);
    font-weight: 800; letter-spacing: -0.03em; color: var(--navy); line-height: 1.15;
  }
  .section-h2-light { color: var(--white); }
  .section-h3 {
    font-family: var(--font-display); font-size: 1.15rem;
    font-weight: 700; color: var(--navy); letter-spacing: -0.01em;
  }

  /* WHO WE ARE */
  .intro-section {
    background: var(--white);
    display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: center;
  }
  .intro-text p { font-size: 1rem; color: var(--text-secondary); margin-top: 1.5rem; line-height: 1.8; }
  .intro-cards { display: flex; flex-direction: column; gap: 1rem; }
  .reality-card {
    background: var(--ice); border-left: 3px solid var(--cyan);
    padding: 1.1rem 1.4rem; border-radius: 0 8px 8px 0;
    font-size: 0.9rem; color: var(--text-secondary); font-weight: 400;
  }
  .reality-card strong { color: var(--navy); font-weight: 600; display: block; margin-bottom: 0.2rem; font-family: var(--font-display); }

  /* PRODUCTS SECTION (dynamic) */
  .products-preview { background: var(--ice); }
  .products-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-top: 3rem; }
  .product-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 14px;
    overflow: hidden; text-decoration: none;
    transition: border-color 0.2s, box-shadow 0.2s; display: block;
  }
  .product-card:hover { border-color: var(--cyan); box-shadow: 0 8px 32px rgba(0,184,219,0.12); }
  .product-card-img {
    height: 160px; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
    display: flex; align-items: center; justify-content: center; position: relative;
    overflow: hidden;
  }
  .product-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .product-card-img-placeholder {
    font-family: var(--font-display); font-size: 2rem; font-weight: 800;
    color: rgba(0,184,219,0.4);
  }
  .product-card-badge {
    position: absolute; top: 0.75rem; left: 0.75rem;
    background: rgba(0,184,219,0.15); border: 1px solid rgba(0,184,219,0.3);
    color: var(--cyan); padding: 0.2rem 0.6rem; border-radius: 100px;
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
  }
  .product-card-sale {
    position: absolute; top: 0.75rem; right: 0.75rem;
    background: #ef4444; color: white; padding: 0.2rem 0.6rem;
    border-radius: 100px; font-size: 0.65rem; font-weight: 700;
  }
  .product-card-body { padding: 1.25rem 1.5rem; }
  .product-card-body p.name {
    font-family: var(--font-display); font-size: 1rem; font-weight: 700;
    color: var(--navy); margin-bottom: 0.4rem;
  }
  .product-card-body p.tagline { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.6; }
  .product-card-footer {
    padding: 0.85rem 1.5rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .product-price { font-family: var(--font-display); font-size: 1rem; font-weight: 800; color: var(--navy); }
  .product-price-sale { color: #16a34a; }
  .product-price-old { font-size: 0.78rem; color: var(--text-muted); text-decoration: line-through; }
  .product-view-link {
    font-size: 0.78rem; font-weight: 600; color: var(--cyan);
    font-family: var(--font-display); text-transform: uppercase; letter-spacing: 0.05em;
  }

  /* APPROACH */
  .approach-section { background: var(--navy); }
  .approach-section .section-tag-label { color: var(--sky); }
  .approach-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 3.5rem; }
  .approach-card {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(0,184,219,0.15);
    border-radius: 12px; padding: 2rem;
    transition: border-color 0.25s, background 0.25s;
  }
  .approach-card:hover { border-color: rgba(0,184,219,0.4); background: rgba(0,184,219,0.06); }
  .approach-icon {
    width: 44px; height: 44px; border-radius: 10px;
    background: rgba(0,184,219,0.15); display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
  }
  .approach-icon svg { width: 22px; height: 22px; stroke: var(--cyan); fill: none; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
  .approach-card h3 { color: var(--white); margin-bottom: 0.75rem; font-family: var(--font-display); font-size: 1rem; font-weight: 700; }
  .approach-card p { font-size: 0.9rem; color: rgba(255,255,255,0.55); line-height: 1.75; font-weight: 300; }

  /* INDUSTRIES */
  .industries-section { background: var(--ice); }
  .industries-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 3.5rem; }
  .industry-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 12px; padding: 1.75rem;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .industry-card:hover { border-color: var(--cyan); box-shadow: 0 8px 32px rgba(0,184,219,0.1); }
  .industry-dot { width: 8px; height: 8px; background: var(--cyan); border-radius: 50%; margin-bottom: 1rem; }
  .industry-card h3 { margin-bottom: 0.6rem; font-size: 1rem; font-family: var(--font-display); font-weight: 700; color: var(--navy); }
  .industry-card p { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.7; }

  /* WHY US */
  .why-section { background: var(--navy); }
  .why-section .section-tag-label { color: var(--sky); }
  .why-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-top: 3.5rem; }
  .why-item { display: flex; gap: 1.25rem; }
  .why-bar { width: 3px; background: var(--cyan); border-radius: 3px; flex-shrink: 0; }
  .why-item h3 { color: var(--white); margin-bottom: 0.5rem; font-family: var(--font-display); font-size: 1rem; font-weight: 700; }
  .why-item p { font-size: 0.9rem; color: rgba(255,255,255,0.55); line-height: 1.75; font-weight: 300; }

  /* TRUST */
  .trust-section { background: var(--ice); text-align: center; }
  .trust-sub { color: var(--text-secondary); margin-bottom: 3rem; margin-top: 0.75rem; }
  .clients-wrap { display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; }
  .client-pill {
    background: var(--white); border: 1px solid var(--border);
    padding: 0.7rem 1.4rem; border-radius: 100px;
    font-size: 0.875rem; font-weight: 500; color: var(--text-secondary);
    transition: border-color 0.2s, color 0.2s;
  }
  .client-pill:hover { border-color: var(--cyan); color: var(--cyan-deep); }

  /* BLOG POSTS PREVIEW */
  .blog-preview { background: var(--white); }
  .blog-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-top: 3rem; }
  .blog-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 14px;
    overflow: hidden; text-decoration: none; display: block;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .blog-card:hover { border-color: var(--cyan); box-shadow: 0 8px 24px rgba(0,184,219,0.1); }
  .blog-card-img {
    height: 140px; background: var(--ice);
    display: flex; align-items: center; justify-content: center;
  }
  .blog-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .blog-card-body { padding: 1.25rem 1.5rem; }
  .blog-card-cat { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--cyan); }
  .blog-card-title { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--navy); margin: 0.4rem 0 0.5rem; line-height: 1.4; }
  .blog-card-excerpt { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.65; }
  .blog-card-meta { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem; }

  /* HOME CTA */
  .home-cta { background: var(--cyan); text-align: center; padding: 6rem; }
  .home-cta h2 { font-family: var(--font-display); color: var(--white); font-size: clamp(2rem, 3.5vw, 3rem); margin-bottom: 1rem; font-weight: 800; letter-spacing: -0.03em; }
  .home-cta p { color: rgba(255,255,255,0.8); max-width: 520px; margin: 0 auto 2.5rem; font-size: 1rem; }
  .btn-white-cta {
    background: var(--white); color: var(--cyan-deep);
    padding: 1rem 2.5rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 1rem; font-weight: 700;
    text-decoration: none; display: inline-block;
    transition: transform 0.15s, box-shadow 0.2s;
  }
  .btn-white-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
  .cta-email-note { display: block; margin-top: 1.5rem; font-size: 0.9rem; color: rgba(255,255,255,0.7); }
  .cta-email-note a { color: var(--white); }

  /* DEMO CTA */
  .demo-cta { background: var(--navy); text-align: center; padding: 5rem 6rem; }
  .demo-cta h2 { font-family: var(--font-display); font-size: clamp(1.7rem,2.8vw,2.4rem); font-weight: 800; color: var(--white); letter-spacing: -0.03em; margin-bottom: 1rem; }
  .demo-cta p { font-size: 0.95rem; color: rgba(255,255,255,0.55); max-width: 480px; margin: 0 auto 2rem; font-weight: 300; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
  .hero-content > * { animation: fadeUp 0.7s ease both; }
  .hero-tag { animation-delay: 0.1s; }
  .hero-h1 { animation-delay: 0.2s; }
  .hero-sub { animation-delay: 0.3s; }
  .hero-buttons { animation-delay: 0.4s; }
  .hero-logo-wrap { animation: fadeUp 0.9s 0.3s ease both; }

  /* CMS Markdown Prose */
  .cms-prose { margin-top: 1rem; }
  .cms-prose p  { font-size: 1rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 0.9rem; }
  .cms-prose h1,.cms-prose h2,.cms-prose h3 {
    font-family: var(--font-display); font-weight: 700; color: var(--navy);
    margin: 1.25rem 0 0.5rem; letter-spacing: -0.02em;
  }
  .cms-prose h2 { font-size: 1.2rem; }
  .cms-prose h3 { font-size: 1rem; }
  .cms-prose strong { color: var(--navy); font-weight: 700; }
  .cms-prose em { color: var(--cyan); font-style: normal; font-weight: 600; }
  .cms-prose ul { list-style: none; padding: 0; margin: 0.5rem 0 1rem; }
  .cms-prose ul li { font-size: 0.9rem; color: var(--text-secondary); padding: 0.2rem 0;
    display: flex; align-items: flex-start; gap: 0.5rem; line-height: 1.6; }
  .cms-prose ul li::before { content: ''; width: 5px; height: 5px; border-radius: 50%;
    background: var(--cyan); flex-shrink: 0; margin-top: 0.45rem; }
  .cms-prose ol { padding-left: 1.5rem; margin: 0.5rem 0 1rem; }
  .cms-prose ol li { font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.3rem; line-height: 1.6; }
  .cms-prose blockquote { border-left: 3px solid var(--cyan); padding-left: 1rem;
    margin: 1rem 0; color: var(--text-muted); font-style: italic; }

  @media (max-width: 1024px) {
    .home-section { padding: 4rem 2rem; }
    .home-hero { grid-template-columns: 1fr; }
    .hero-visual { display: none; }
    .hero-content { padding: 7rem 2rem 4rem; }
    .stats-bar { padding: 2rem; grid-template-columns: repeat(2,1fr) !important; }
    .intro-section { grid-template-columns: 1fr; gap: 3rem; }
    .approach-grid, .industries-grid, .why-grid, .products-grid, .blog-grid { grid-template-columns: 1fr; }
    .home-cta, .demo-cta { padding: 4rem 2rem; }
  }
  @media (max-width: 640px) {
    .hero-content { padding: 6rem 1.25rem 3rem; }
    .hero-h1 { font-size: clamp(2rem, 8vw, 2.8rem); }
  }
</style>

@php
  // Helper: parse **word** → <em>word</em> for hero highlight
  $heroTitle = preg_replace('/\*\*(.+?)\*\*/', '<em>$1</em>', e($cms['hero_title_raw']));
  // Helper: render markdown about content safely
  $aboutContent = $cms['about_content'];
@endphp

<!-- HERO -->
<section class="home-hero">
  <div class="hero-bg-pattern"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-content">
    <div class="hero-tag"><span></span> {{ $cms['hero_tag'] }}</div>
    <h1 class="hero-h1">{!! $heroTitle !!}</h1>
    <p class="hero-sub">{{ $cms['hero_subtitle'] }}</p>
    <div class="hero-buttons">
      <a class="btn-primary-cyan" href="{{ route('site.book-demo') }}" wire:navigate>{{ $cms['hero_btn_primary'] }}</a>
      <a class="btn-secondary-outline" href="{{ route('site.products') }}" wire:navigate>{{ $cms['hero_btn_secondary'] }}</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-logo-wrap" id="orbitWrap">
      {{-- Radar canvas background --}}
      <div class="radar-container">
        <canvas id="radarSweep" width="500" height="500"></canvas>
      </div>
      {{-- Core logo --}}
      <div class="orbit-core">
        @php $logoPath = public_path('assets/images/logo.svg'); $hasLogo = file_exists($logoPath) && filesize($logoPath) > 0; @endphp
        @if($hasLogo)
          <img src="{{ asset('assets/images/logo.svg') }}" alt="Exchosoft">
        @else
          <div class="orbit-core-fallback">E</div>
        @endif
      </div>
      {{-- Software icons - Outer ring --}}
      <div class="orbit-icon" data-angle="0" data-label="Web Dev">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 3c0 0-4 3.5-4 9s4 9 4 9 4-3.5 4-9-4-9-4-9z"></path><line x1="3" x2="21" y1="12" y2="12"></line></svg>
      </div>
      <div class="orbit-icon" data-angle="45" data-label="Mobile Apps">
        <svg viewBox="0 0 24 24"><rect height="20" rx="2" width="10" x="7" y="2"></rect><line x1="12" x2="12" y1="18" y2="18"></line></svg>
      </div>
      <div class="orbit-icon" data-angle="90" data-label="Databases">
        <svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
      </div>
      <div class="orbit-icon" data-angle="135" data-label="Cloud">
        <svg viewBox="0 0 24 24"><path d="M18 10a6 6 0 0 0-12 0 4 4 0 0 0 0 8h12a4 4 0 0 0 0-8z"></path></svg>
      </div>
      <div class="orbit-icon" data-angle="180" data-label="Custom Software">
        <svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
      </div>
      <div class="orbit-icon" data-angle="225" data-label="Security">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
      </div>
      <div class="orbit-icon" data-angle="270" data-label="Analytics">
        <svg viewBox="0 0 24 24"><line x1="18" x2="18" y1="20" y2="10"></line><line x1="12" x2="12" y1="20" y2="4"></line><line x1="6" x2="6" y1="20" y2="14"></line></svg>
      </div>
      <div class="orbit-icon" data-angle="315" data-label="API & Integration">
        <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3m8 0h3a2 2 0 0 0 2-2v-3"></path><circle cx="12" cy="12" r="3"></circle></svg>
      </div>
    </div>
  </div>
</section>
<script>
(function() {
  const ORBIT_RADIUS = 205;
  const CANVAS_SIZE  = 500;
  const SWEEP_SPEED  = 0.8;
  const TAIL_ANGLE   = Math.PI / 4;
  const LIT_WINDOW   = 0.15;
  const canvas = document.getElementById('radarSweep');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const CX = CANVAS_SIZE / 2, CY = CANVAS_SIZE / 2;
  const icons = Array.from(document.querySelectorAll('.orbit-icon'));
  icons.forEach(icon => {
    const deg = parseFloat(icon.dataset.angle);
    const rad = (deg - 90) * (Math.PI / 180);
    const tx = ORBIT_RADIUS * Math.cos(rad);
    const ty = ORBIT_RADIUS * Math.sin(rad);
    icon.style.transform = `translate(${tx}px, ${ty}px)`;
    icon._rad = rad; icon._glow = 0;
  });
  let sweepAngle = -Math.PI / 2, lastTime = null;
  function normalize(a) { return ((a % (Math.PI*2)) + Math.PI*2) % (Math.PI*2); }
  function draw(ts) {
    if (!lastTime) lastTime = ts;
    const dt = (ts - lastTime) / 1000; lastTime = ts;
    sweepAngle += SWEEP_SPEED * dt;
    const ns = normalize(sweepAngle);
    ctx.clearRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);
    ctx.strokeStyle = 'rgba(0,184,219,0.05)'; ctx.lineWidth = 1;
    for (let r = 50; r <= 240; r += 40) { ctx.beginPath(); ctx.arc(CX,CY,r,0,Math.PI*2); ctx.stroke(); }
    ctx.save();
    for (let i = 0; i < 60; i++) {
      const step = TAIL_ANGLE/60;
      const start = sweepAngle - TAIL_ANGLE + (i*step), end = start+step;
      const opacity = Math.pow(i/60,2)*0.22;
      ctx.beginPath(); ctx.moveTo(CX,CY);
      ctx.arc(CX,CY,250,start,end); ctx.closePath();
      ctx.fillStyle = `rgba(0,184,219,${opacity})`; ctx.fill();
    }
    ctx.restore();
    ctx.beginPath(); ctx.moveTo(CX,CY);
    ctx.lineTo(CX+250*Math.cos(sweepAngle), CY+250*Math.sin(sweepAngle));
    ctx.strokeStyle = 'rgba(0,230,255,0.6)'; ctx.lineWidth = 1.8; ctx.stroke();
    const cg = ctx.createRadialGradient(CX,CY,0,CX,CY,15);
    cg.addColorStop(0,'rgba(0,230,255,0.9)'); cg.addColorStop(0.5,'rgba(0,184,219,0.4)'); cg.addColorStop(1,'rgba(0,184,219,0)');
    ctx.beginPath(); ctx.arc(CX,CY,15,0,Math.PI*2); ctx.fillStyle=cg; ctx.fill();
    icons.forEach(icon => {
      const ir = normalize(icon._rad);
      const diff = Math.abs(ns-ir);
      const isHit = diff < LIT_WINDOW || (Math.PI*2-diff) < LIT_WINDOW;
      if (isHit) { icon._glow = 1.0; } else { icon._glow = Math.max(0, icon._glow - dt*2.5); }
      const g = icon._glow;
      if (g > 0.05) {
        icon.style.background = `rgba(13,33,55,${0.8+0.1*g})`;
        icon.style.borderColor = `rgba(0,184,219,${0.15+0.7*g})`;
        icon.style.boxShadow = `0 0 ${12+20*g}px rgba(0,184,219,${0.1+0.4*g})`;
        icon.querySelector('svg').style.opacity = 0.6+0.4*g;
        icon.querySelector('svg').style.filter = `drop-shadow(0 0 ${4*g}px rgba(0,230,255,0.8))`;
      } else {
        icon.style.background='rgba(13,33,55,0.8)'; icon.style.borderColor='rgba(0,184,219,0.15)';
        icon.style.boxShadow='none'; icon.querySelector('svg').style.opacity='0.6'; icon.querySelector('svg').style.filter='none';
      }
    });
    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();
</script>

<!-- STATS BAR -->
<div class="stats-bar" style="grid-template-columns: repeat({{ count($cms['stats']) }}, 1fr);">
  @foreach($cms['stats'] as $stat)
  <div class="stat">
    <div class="stat-num">{{ $stat['num'] ?? '' }}</div>
    <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
  </div>
  @endforeach
</div>

<!-- WHO WE ARE -->
<section class="home-section intro-section" id="about">
  <div class="intro-text">
    <p class="section-tag-label">{{ $cms['about_tag'] }}</p>
    <h2 class="section-h2">{{ $cms['about_title'] }}</h2>
    @if($aboutContent)
      @php
        // Simple markdown paragraph rendering
        $paras = array_filter(explode("\n\n", $aboutContent));
      @endphp
      @foreach($paras as $para)
        <p>{{ strip_tags($para) }}</p>
      @endforeach
    @else
      <p>Exchosoft Consult is a Ghana-based technology consultancy and software development company. We've built systems for churches, hospitals, pharmacies, laboratories, laundries, heritage organizations, and more—each one custom-designed for that specific business.</p>
      <p>We understand the conditions our clients operate in because we're here too.</p>
    @endif
  </div>
  <div class="intro-cards">
    @foreach($cms['about_cards'] as $card)
    <div class="reality-card"><strong>{{ $card['title'] ?? '' }}</strong>{{ $card['body'] ?? '' }}</div>
    @endforeach
  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="home-section products-preview" id="products">
  <p class="section-tag-label">{{ $cms['products_tag'] }}</p>
  <h2 class="section-h2">{{ $cms['products_title'] }}</h2>

  @if($featuredProducts->isNotEmpty())
  <div class="products-grid">
    @foreach($featuredProducts as $product)
    <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate class="product-card">
      <div class="product-card-img">
        @if($product->cover_image)
          <img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}">
        @else
          <div class="product-card-img-placeholder">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
        @endif
        <span class="product-card-badge">{{ $product->category }}</span>
        @if($product->is_on_sale)<span class="product-card-sale">SALE</span>@endif
      </div>
      <div class="product-card-body">
        <p class="name">{{ $product->name }}</p>
        @if($product->tagline)<p class="tagline">{{ $product->tagline }}</p>@endif
      </div>
      <div class="product-card-footer">
        <div>
          @if($product->is_on_sale)
            <div class="product-price-old">GHS {{ number_format($product->price, 2) }}</div>
            <div class="product-price product-price-sale">GHS {{ number_format($product->sale_price, 2) }}</div>
          @else
            <div class="product-price">GHS {{ number_format($product->price, 2) }}</div>
          @endif
        </div>
        <span class="product-view-link">View →</span>
      </div>
    </a>
    @endforeach
  </div>
  @else
  {{-- Placeholder cards —show our actual products --}}
  <div class="products-grid">
    @foreach([
      ['WashOps', 'wash', 'Complete Laundry Management System', 'Enterprise-grade desktop POS with analytics, kanban orders board, and cloud sync.'],
      ['ChurchOps', 'church', 'Church Management Platform', 'Offline-first system for churches — members, finances, SMS, multi-branch.'],
      ['Custom Build', 'custom', 'Built For Your Industry', 'Tell us your problem. We build the solution from the ground up.'],
    ] as [$name, $type, $tagline, $desc])
    <a href="{{ route('site.products') }}" wire:navigate class="product-card">
      <div class="product-card-img" style="background: {{ $type === 'church' ? '#0f2d1f' : 'var(--navy)' }};">
        <div class="product-card-img-placeholder" style="color: {{ $type === 'church' ? 'rgba(76,175,130,0.5)' : 'rgba(0,184,219,0.4)' }};">{{ strtoupper(substr($name,0,2)) }}</div>
        <span class="product-card-badge" style="{{ $type === 'church' ? 'background:rgba(76,175,130,0.15);border-color:rgba(76,175,130,0.3);color:#4caf82;' : '' }}">{{ $name }}</span>
      </div>
      <div class="product-card-body">
        <p class="name">{{ $tagline }}</p>
        <p class="tagline">{{ $desc }}</p>
      </div>
      <div class="product-card-footer">
        <div><div class="product-price" style="color:var(--cyan);">Learn More</div></div>
        <span class="product-view-link">View →</span>
      </div>
    </a>
    @endforeach
  </div>
  @endif

  <div style="text-align:center;margin-top:2.5rem;">
    <a href="{{ route('site.products') }}" wire:navigate class="btn-secondary-outline" style="color:var(--navy);border-color:var(--border);">View All Products</a>
  </div>
</section>

<!-- OUR APPROACH -->
<section class="home-section approach-section" id="approach">
  <p class="section-tag-label">{{ $cms['approach_tag'] }}</p>
  <h2 class="section-h2 section-h2-light">{{ $cms['approach_title'] }}</h2>
  @php
    $approachIcons = [
      'grid'    => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
      'offline' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>',
      'data'    => '<svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/></svg>',
      'lan'     => '<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
      'shield'  => '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
      'partner' => '<svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
    ];
    $defaultIcons = array_values($approachIcons);
  @endphp
  <div class="approach-grid">
    @if(count($cms['approach_cards']) > 0)
      @foreach($cms['approach_cards'] as $idx => $card)
      <div class="approach-card">
        <div class="approach-icon">
          {!! $approachIcons[$card['icon'] ?? ''] ?? $defaultIcons[$idx % count($defaultIcons)] !!}
        </div>
        <h3>{{ $card['title'] ?? '' }}</h3>
        <p>{{ $card['body'] ?? '' }}</p>
      </div>
      @endforeach
    @else
      {{-- fallback static --}}
      @foreach([
        ['grid','Every Business Needs Its Own Solution','Off-the-shelf software forces unacceptable compromises.'],
        ['offline','Offline-First When It Matters','Automatic cloud sync when online. Full functionality when offline.'],
        ['data','Unified Systems, Clear Insights','Cohesive systems with management visibility and actionable analytics.'],
        ['lan','LAN Collaboration','Real-time collaboration even when external connectivity fails.'],
        ['shield','Security & Reliability','Security baked in — not bolted on — for every client.'],
        ['partner','Long-Term Partnership','Systems that grow with your business. We stay involved.'],
      ] as [$icon, $title, $body])
      <div class="approach-card">
        <div class="approach-icon">{!! $approachIcons[$icon] !!}</div>
        <h3>{{ $title }}</h3><p>{{ $body }}</p>
      </div>
      @endforeach
    @endif
  </div>
</section>

<!-- INDUSTRIES -->
<section class="home-section industries-section" id="industries">
  <p class="section-tag-label">{{ $cms['industries_tag'] }}</p>
  <h2 class="section-h2">{{ $cms['industries_title'] }}</h2>
  <div class="industries-grid">
    @if(count($cms['industries_cards']) > 0)
      @foreach($cms['industries_cards'] as $card)
      <div class="industry-card">
        <div class="industry-dot"></div>
        <h3>{{ $card['title'] ?? '' }}</h3>
        <p>{{ $card['body'] ?? '' }}</p>
      </div>
      @endforeach
    @else
      @foreach([
        ['Healthcare & Medical','Hospital management systems, pharmacy solutions, and laboratory platforms — offline-first.'],
        ['Faith-Based Organizations','Comprehensive management systems for churches covering membership, events, and finances.'],
        ['Laundry & Service Industries','End-to-end business management with order tracking and customer management.'],
        ['Heritage & Cultural','Digital preservation and management tools for cultural organizations and archives.'],
        ['Financial Services','Secure financial management systems with loan tracking, savings, and reporting.'],
        ['Retail & Distribution','Inventory, POS, and supply chain systems built for African markets.'],
      ] as [$title, $body])
      <div class="industry-card"><div class="industry-dot"></div><h3>{{ $title }}</h3><p>{{ $body }}</p></div>
      @endforeach
    @endif
  </div>
</section>

<!-- WHY US -->
<section class="home-section why-section">
  <p class="section-tag-label">{{ $cms['why_tag'] }}</p>
  <h2 class="section-h2 section-h2-light">{{ $cms['why_title'] }}</h2>
  <div class="why-grid">
    @if(count($cms['why_items']) > 0)
      @foreach($cms['why_items'] as $item)
      <div class="why-item">
        <div class="why-bar"></div>
        <div>
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['body'] ?? '' }}</p>
        </div>
      </div>
      @endforeach
    @else
      @foreach([
        ['Built Here, For Here','We operate in the same environment as our clients.'],
        ['No Generic Solutions','Every engagement starts from scratch.'],
        ['Offline-First by Default','No data loss through power outages and internet disruptions.'],
        ['Long-Term Relationships','We stay involved, providing support and evolution as you grow.'],
      ] as [$title, $body])
      <div class="why-item"><div class="why-bar"></div><div><h3>{{ $title }}</h3><p>{{ $body }}</p></div></div>
      @endforeach
    @endif
  </div>
</section>

<!-- TRUST -->
<section class="home-section trust-section">
  <p class="section-tag-label">{{ $cms['trust_tag'] }}</p>
  <h2 class="section-h2">{{ $cms['trust_title'] }}</h2>
  @if($cms['trust_subtitle'])<p class="trust-sub">{{ $cms['trust_subtitle'] }}</p>@endif
  <div class="clients-wrap">
    @foreach($cms['trust_clients'] as $client)
    <div class="client-pill">{{ $client }}</div>
    @endforeach
  </div>
</section>

<!-- BLOG POSTS (if any) -->
@if($latestPosts->isNotEmpty())
<section class="home-section blog-preview">
  <p class="section-tag-label">Stay Informed</p>
  <h2 class="section-h2">From the Blog</h2>
  <div class="blog-grid">
    @foreach($latestPosts as $post)
    <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate class="blog-card">
      <div class="blog-card-img">
        @if($post->cover_image)
          <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}">
        @else
          <svg style="width:40px;height:40px;color:var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        @endif
      </div>
      <div class="blog-card-body">
        <div class="blog-card-cat">{{ $post->category }}</div>
        <div class="blog-card-title">{{ $post->title }}</div>
        @if($post->excerpt)<div class="blog-card-excerpt">{{ Str::limit($post->excerpt, 100) }}</div>@endif
        <div class="blog-card-meta">{{ $post->published_at?->format('d M Y') }} · {{ $post->read_time_minutes }} min read</div>
      </div>
    </a>
    @endforeach
  </div>
  <div style="text-align:center;margin-top:2.5rem;">
    <a href="{{ route('site.blog') }}" wire:navigate class="btn-secondary-outline" style="color:var(--navy);border-color:var(--border);">Read More Posts</a>
  </div>
</section>
@endif

<!-- DEMO CTA -->
<section class="demo-cta">
  <h2>{{ $cms['demo_cta_title'] }}</h2>
  <p>{{ $cms['demo_cta_subtitle'] }}</p>
  <a href="{{ route('site.book-demo') }}" wire:navigate class="btn-primary-cyan">
    Book a Free Demo
  </a>
</section>

<!-- MAIN CTA -->
<section class="home-cta" id="cta">
  <h2>{{ $cms['cta_title'] }}</h2>
  <p>{{ $cms['cta_subtitle'] }}</p>
  <a class="btn-white-cta" href="{{ route('site.book-demo') }}" wire:navigate>{{ $cms['cta_btn'] }}</a>
  @if($cms['cta_email_note'])
  <span class="cta-email-note">{{ $cms['cta_email_note'] }}</span>
  @endif
</section>

</div>
