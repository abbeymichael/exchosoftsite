<?php

use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Models\ShopProduct;
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

        return view('livewire.pages.site.home', compact('featuredProducts', 'latestPosts', 'featuredCases', 'featuredWork'));
    }
}; ?>

<div>
<style>
  /* ── HOME PAGE STYLES ── */
  .home-hero {
    min-height: 100vh;
    background: var(--navy);
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
    width: 320px; height: 320px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(0,184,219,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    position: relative;
    font-family: var(--font-display); font-size: 5rem; font-weight: 800;
    color: var(--cyan);
  }
  .hero-logo-wrap::before {
    content: ''; position: absolute; inset: -20px;
    border-radius: 50%; border: 1px solid rgba(0,184,219,0.08);
  }
  .hero-logo-wrap::after {
    content: ''; position: absolute; inset: -40px;
    border-radius: 50%; border: 1px solid rgba(0,184,219,0.05);
  }
  .hero-logo-wrap img { width: 200px; height: auto; }

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

  @media (max-width: 1024px) {
    .home-section { padding: 4rem 2rem; }
    .home-hero { grid-template-columns: 1fr; }
    .hero-visual { display: none; }
    .hero-content { padding: 6rem 2rem 4rem; }
    .stats-bar { padding: 2rem; }
    .intro-section { grid-template-columns: 1fr; gap: 3rem; }
    .approach-grid, .industries-grid, .why-grid, .products-grid, .blog-grid { grid-template-columns: 1fr; }
    .stats-bar { grid-template-columns: repeat(2,1fr); }
    .home-cta, .demo-cta { padding: 4rem 2rem; }
  }
</style>

<!-- HERO -->
<section class="home-hero">
  <div class="hero-bg-pattern"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-content">
    <div class="hero-tag"><span></span> Ghana-Based · Africa · Caribbean · Diaspora</div>
    <h1 class="hero-h1">Technology Consultancy Built on <em>Real-World</em> Experience</h1>
    <p class="hero-sub">We're a software development and consultancy firm serving Black businesses across Africa, the Caribbean, and the diaspora—building custom solutions that work in your reality, not just in theory.</p>
    <div class="hero-buttons">
      <a class="btn-primary-cyan" href="{{ route('site.book-demo') }}" wire:navigate>Talk to Us</a>
      <a class="btn-secondary-outline" href="{{ route('site.products') }}" wire:navigate>Our Products</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-logo-wrap">
        @php $logoPath = public_path('assets/images/logo.svg'); $hasLogo = file_exists($logoPath) && filesize($logoPath) > 0; @endphp
        @if($hasLogo)
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Exchosoft Consult">
        @else
            EC
        @endif
    </div>
  </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
  <div class="stat">
    <div class="stat-num">10+</div>
    <div class="stat-label">Industries served</div>
  </div>
  <div class="stat">
    <div class="stat-num">3</div>
    <div class="stat-label">Continents reached</div>
  </div>
  <div class="stat">
    <div class="stat-num">100%</div>
    <div class="stat-label">Custom-built solutions</div>
  </div>
  <div class="stat">
    <div class="stat-num">Offline</div>
    <div class="stat-label">First architecture</div>
  </div>
</div>

<!-- WHO WE ARE -->
<section class="home-section intro-section" id="about">
  <div class="intro-text">
    <p class="section-tag-label">Who We Are</p>
    <h2 class="section-h2">Built for the Conditions You Actually Operate In</h2>
    <p>Exchosoft Consult is a Ghana-based technology consultancy and software development company. We've built systems for churches, hospitals, pharmacies, laboratories, laundries, heritage organizations, and more—each one custom-designed for that specific business.</p>
    <p>We understand the conditions our clients operate in because we're here too.</p>
  </div>
  <div class="intro-cards">
    <div class="reality-card"><strong>Intermittent connectivity</strong>We build systems that keep working when the internet drops.</div>
    <div class="reality-card"><strong>Power challenges</strong>Offline-first architecture means no data is lost during outages.</div>
    <div class="reality-card"><strong>Mobile-first users</strong>Designed from the ground up for how your customers actually access technology.</div>
    <div class="reality-card"><strong>Local payment systems</strong>Integrated with the payment infrastructure your market already uses.</div>
  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="home-section products-preview" id="products">
  <p class="section-tag-label">Our Software</p>
  <h2 class="section-h2">Products Built for African Businesses</h2>

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
  {{-- Placeholder cards showing our actual products --}}
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
  <p class="section-tag-label">Our Approach</p>
  <h2 class="section-h2 section-h2-light">What We've Learned Building Software Across Industries</h2>
  <div class="approach-grid">
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      </div>
      <h3>Every Business Needs Its Own Solution</h3>
      <p>Off-the-shelf software forces unacceptable compromises. Each business has unique workflows, and they deserve technology built specifically for how they operate.</p>
    </div>
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
      </div>
      <h3>Offline-First When It Matters</h3>
      <p>We pioneered offline-first architecture for clients who can't afford downtime — hospitals, pharmacies, churches — with automatic cloud sync when online.</p>
    </div>
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/></svg>
      </div>
      <h3>Unified Systems, Clear Insights</h3>
      <p>We unify business workflows into cohesive systems that give management complete visibility and analytics that clearly identify where improvements are needed.</p>
    </div>
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
      </div>
      <h3>LAN Collaboration</h3>
      <p>For businesses with multiple locations or devices, we implement local network capabilities — real-time collaboration even when external connectivity fails.</p>
    </div>
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <h3>Security & Reliability</h3>
      <p>From financial institutions to healthcare providers, we build with security baked in — not bolted on — because your clients' data deserves that standard.</p>
    </div>
    <div class="approach-card">
      <div class="approach-icon">
        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div>
      <h3>Long-Term Partnership</h3>
      <p>We're not just building software — we're building systems that grow with your business and adapt as your needs change. We stay involved.</p>
    </div>
  </div>
</section>

<!-- INDUSTRIES -->
<section class="home-section industries-section" id="industries">
  <p class="section-tag-label">Experience</p>
  <h2 class="section-h2">Industries We've Served</h2>
  <div class="industries-grid">
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Healthcare & Medical</h3>
      <p>Hospital management systems, pharmacy solutions, and laboratory platforms — offline-first, designed to work when connectivity doesn't.</p>
    </div>
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Faith-Based Organizations</h3>
      <p>Comprehensive management systems for churches covering membership, events, donations, and complete financial transparency.</p>
    </div>
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Service Industries</h3>
      <p>From laundry management to operational platforms — tracking orders, managing workflows, and handling billing seamlessly.</p>
    </div>
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Heritage & Cultural</h3>
      <p>Partnering with Black History Walks and African Odysseys, building platforms for cultural preservation and diaspora engagement.</p>
    </div>
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Financial Services</h3>
      <p>Working with institutions like Ghana Union Assurance, building secure, reliable financial systems that meet institutional standards.</p>
    </div>
    <div class="industry-card">
      <div class="industry-dot"></div>
      <h3>Cross-Continental Initiatives</h3>
      <p>Supporting the African Caribbean Summit and ACIS — technology that bridges communities across continents.</p>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="home-section why-section">
  <p class="section-tag-label">Why Exchosoft</p>
  <h2 class="section-h2 section-h2-light">Why Work With Us</h2>
  <div class="why-grid">
    <div class="why-item">
      <div class="why-bar"></div>
      <div>
        <h3>We Build What You Actually Need</h3>
        <p>Not what we think you should need. Not what worked for someone else. We listen, understand your operations, and build specifically for you.</p>
      </div>
    </div>
    <div class="why-item">
      <div class="why-bar"></div>
      <div>
        <h3>We Understand Your Context</h3>
        <p>From Lagos to London, Accra to Atlanta, Kingston to Kumasi — we understand the infrastructure challenges and operational conditions of doing business across our markets.</p>
      </div>
    </div>
    <div class="why-item">
      <div class="why-bar"></div>
      <div>
        <h3>We've Done This Before</h3>
        <p>Our experience across healthcare, faith organizations, service industries, heritage preservation, and financial services means proven expertise with full customization.</p>
      </div>
    </div>
    <div class="why-item">
      <div class="why-bar"></div>
      <div>
        <h3>We Think Long-Term</h3>
        <p>We're not just building software — we're building systems that will grow with your business and adapt as your needs change over time.</p>
      </div>
    </div>
  </div>
</section>

<!-- TRUST -->
<section class="home-section trust-section">
  <p class="section-tag-label">Organizations We've Worked With</p>
  <h2 class="section-h2">Trusted Across Industries</h2>
  <p class="trust-sub">Proud to have partnered with organizations across Africa, the Caribbean, and the diaspora.</p>
  <div class="clients-wrap">
    <div class="client-pill">Black History Walks</div>
    <div class="client-pill">African Odysseys</div>
    <div class="client-pill">African Caribbean Summit</div>
    <div class="client-pill">Ghana Union Assurance</div>
    <div class="client-pill">ACIS</div>
    <div class="client-pill">Churches across West Africa</div>
    <div class="client-pill">Hospitals & Pharmacies</div>
    <div class="client-pill">Laboratories</div>
    <div class="client-pill">SMEs</div>
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
  <h2>See Our Products in Action</h2>
  <p>Book a personalized demo and see how Exchosoft products can transform your business operations.</p>
  <a href="{{ route('site.book-demo') }}" wire:navigate class="btn-primary-cyan">
    Book a Free Demo
  </a>
</section>

<!-- MAIN CTA -->
<section class="home-cta" id="cta">
  <h2>Let's Talk About Your Business</h2>
  <p>Every business is different. Every challenge is unique. Let's discuss what you're trying to achieve and explore how we can build technology that actually fits your operations.</p>
  <a class="btn-white-cta" href="{{ route('site.consulting') }}" wire:navigate>Schedule a Consultation</a>
  <span class="cta-email-note">Or email us directly at <a href="mailto:contact@exchosoft.com">contact@exchosoft.com</a></span>
</section>

</div>
