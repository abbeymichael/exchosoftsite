<?php

use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Products — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterCategory = '';
    public string $activeTab     = 'all';

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }

    public function render(): \Illuminate\View\View
    {
        $products = ShopProduct::published()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('tagline', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('sort_order')->latest()
            ->paginate(12);

        // For the "featured" dynamic sections — group by linked_product_code or use all
        $featuredGroups = ShopProduct::published()
            ->whereNotNull('linked_product_code')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('linked_product_code');

        $allPublished = ShopProduct::published()->orderBy('sort_order')->get();

        return view('livewire.pages.site.products', compact('products', 'featuredGroups', 'allPublished'));
    }
}; ?>

<div>
<style>
  .products-banner {
    min-height: 440px;
    background: var(--navy);
    position: relative; overflow: hidden;
    display: flex; align-items: center;
  }
  .banner-canvas-bg {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(0,184,219,0.14) 1px, transparent 1px);
    background-size: 32px 32px; pointer-events: none;
  }
  .banner-glow {
    position: absolute; inset: 0;
    background-image:
      radial-gradient(circle at 75% 50%, rgba(0,184,219,0.1) 0%, transparent 60%),
      radial-gradient(circle at 20% 80%, rgba(122,207,232,0.06) 0%, transparent 50%);
    pointer-events: none;
  }
  .products-banner-content {
    position: relative; z-index: 2;
    padding: 4rem 6rem; max-width: 780px;
  }
  .banner-breadcrumb-row {
    display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;
  }
  .banner-breadcrumb-row a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .banner-breadcrumb-row a:hover { color: var(--cyan); }
  .crumb-sep { color: rgba(255,255,255,0.2); font-size: 0.75rem; }
  .crumb-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--cyan); display: inline-block; margin-right: 0.2rem; vertical-align: middle; }
  .crumb-current { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .banner-tag {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
    color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px;
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em;
    margin-bottom: 1.25rem; text-transform: uppercase;
  }
  .products-banner h1 {
    font-family: var(--font-display);
    font-size: clamp(2rem, 3.8vw, 3.2rem);
    font-weight: 800; color: var(--white);
    line-height: 1.1; letter-spacing: -0.03em;
    margin-bottom: 1rem;
  }
  .products-banner h1 em { color: var(--cyan); font-style: normal; }
  .products-banner-sub {
    font-size: 1rem; color: rgba(255,255,255,0.55);
    max-width: 540px; line-height: 1.75; font-weight: 300;
  }

  /* FILTER BAR */
  .products-filter-bar {
    background: var(--ice);
    border-bottom: 1px solid var(--border);
    padding: 0 6rem;
    display: flex; align-items: center; gap: 0;
    position: sticky; top: 62px; z-index: 50;
    overflow-x: auto;
  }
  .filter-tab {
    padding: 1.1rem 1.75rem;
    font-family: var(--font-display); font-size: 0.85rem; font-weight: 600;
    color: var(--text-muted); border-bottom: 2px solid transparent;
    cursor: pointer; transition: color 0.2s, border-color 0.2s;
    text-decoration: none; white-space: nowrap; background: none; border-top: none; border-left: none; border-right: none;
  }
  .filter-tab:hover { color: var(--text-primary); }
  .filter-tab.active { color: var(--cyan); border-bottom-color: var(--cyan); }
  .filter-count {
    display: inline-block; font-size: 0.7rem; font-weight: 700;
    background: var(--sky-light); color: var(--cyan-deep);
    padding: 1px 6px; border-radius: 100px; margin-left: 6px;
  }

  /* PRODUCTS WRAP */
  .products-wrap { padding-bottom: 4rem; }

  /* ── PRODUCT HERO SECTIONS ── */
  .product-section { position: relative; }

  .product-hero-dark {
    padding: 5rem 6rem;
    display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;
    position: relative; overflow: hidden;
  }
  .product-hero-dark.wash { background: var(--navy); }
  .product-hero-dark.church { background: #0f2d1f; }

  .product-hero-glow-wash {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 80% 50%, rgba(0,184,219,0.1) 0%, transparent 60%);
    pointer-events: none;
  }
  .product-hero-glow-church {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 80% 40%, rgba(76,175,130,0.12) 0%, transparent 60%);
    pointer-events: none;
  }
  .product-hero-grid-wash {
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(0,184,219,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0,184,219,0.03) 1px, transparent 1px);
    background-size: 48px 48px; pointer-events: none;
  }
  .product-hero-grid-church {
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(76,175,130,0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(76,175,130,0.04) 1px, transparent 1px);
    background-size: 48px 48px; pointer-events: none;
  }

  .product-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.3rem 0.9rem; border-radius: 100px;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
    margin-bottom: 1rem; text-transform: uppercase; width: fit-content;
  }
  .badge-wash { background: rgba(0,184,219,0.15); border: 1px solid rgba(0,184,219,0.25); color: var(--cyan); }
  .badge-church { background: rgba(76,175,130,0.15); border: 1px solid rgba(76,175,130,0.25); color: #4caf82; }
  .badge-custom { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.7); }

  .product-hero-dark h2 {
    font-family: var(--font-display); font-size: clamp(1.8rem, 3vw, 2.8rem);
    font-weight: 800; color: var(--white); line-height: 1.1;
    letter-spacing: -0.03em; margin-bottom: 1rem;
  }
  .product-hero-dark h2 .accent-wash { color: var(--cyan); }
  .product-hero-dark h2 .accent-church { color: #4caf82; }
  .product-hero-dark p { font-size: 0.95rem; color: rgba(255,255,255,0.55); line-height: 1.8; font-weight: 300; margin-bottom: 2rem; }

  .btn-row { display: flex; gap: 0.85rem; flex-wrap: wrap; }
  .btn-cyan {
    background: var(--cyan); color: var(--white);
    padding: 0.75rem 1.6rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 0.875rem; font-weight: 700;
    text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block;
  }
  .btn-cyan:hover { background: var(--cyan-dark); transform: translateY(-1px); }
  .btn-outline-wash {
    background: transparent; color: var(--cyan);
    padding: 0.75rem 1.6rem; border-radius: 8px; border: 1px solid rgba(0,184,219,0.35);
    font-family: var(--font-display); font-size: 0.875rem; font-weight: 700;
    text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block;
  }
  .btn-outline-wash:hover { border-color: var(--cyan); background: rgba(0,184,219,0.08); }
  .btn-green {
    background: #1a6b4a; color: var(--white);
    padding: 0.75rem 1.6rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 0.875rem; font-weight: 700;
    text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block;
  }
  .btn-green:hover { background: #155a3e; transform: translateY(-1px); }
  .btn-outline-green {
    background: transparent; color: #4caf82;
    padding: 0.75rem 1.6rem; border-radius: 8px; border: 1px solid rgba(76,175,130,0.35);
    font-family: var(--font-display); font-size: 0.875rem; font-weight: 700;
    text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block;
  }
  .btn-outline-green:hover { border-color: #4caf82; background: rgba(76,175,130,0.08); }

  /* Mock UI card */
  .product-ui-card {
    border-radius: 14px; overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    position: relative; z-index: 1;
  }
  .product-ui-card.church-card { border-color: rgba(76,175,130,0.15); }
  .ui-titlebar {
    padding: 0.7rem 1rem; display: flex; align-items: center; gap: 0.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
  }
  .ui-titlebar.church { background: rgba(76,175,130,0.08); border-color: rgba(76,175,130,0.12); }
  .ui-dot { width: 10px; height: 10px; border-radius: 50%; }
  .ui-dot.r { background: #ff5f57; }
  .ui-dot.y { background: #ffbd2e; }
  .ui-dot.g { background: #28ca41; }
  .ui-titlebar-label { font-size: 0.7rem; color: rgba(255,255,255,0.3); margin-left: auto; font-family: var(--font-display); }
  .ui-offline-badge {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: rgba(76,175,130,0.2); border: 1px solid rgba(76,175,130,0.3);
    color: #4caf82; padding: 0.2rem 0.6rem; border-radius: 4px;
    font-size: 0.58rem; font-weight: 700; letter-spacing: 0.05em;
  }
  .pulse-dot { width: 5px; height: 5px; border-radius: 50%; background: #4caf82; animation: blink 1.2s ease-in-out infinite; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
  .ui-body { padding: 1.25rem; }
  .ui-stat-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 0.6rem; margin-bottom: 0.75rem; }
  .ui-stat {
    background: rgba(255,255,255,0.05); border-radius: 8px; padding: 0.75rem;
    border: 1px solid rgba(255,255,255,0.06);
  }
  .church-stat { background: rgba(76,175,130,0.08); border-color: rgba(76,175,130,0.12); }
  .ui-stat-num { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; }
  .ui-stat-label { font-size: 0.65rem; color: rgba(255,255,255,0.35); margin-top: 2px; }
  .ui-bar-row { display: flex; flex-direction: column; gap: 0.4rem; }
  .ui-bar-item { display: flex; align-items: center; gap: 0.6rem; }
  .ui-bar-label { font-size: 0.65rem; color: rgba(255,255,255,0.4); width: 60px; flex-shrink: 0; }
  .ui-bar-track { flex: 1; height: 5px; background: rgba(255,255,255,0.07); border-radius: 3px; overflow: hidden; }
  .ui-bar-fill { height: 100%; border-radius: 3px; }
  .ui-bar-val { font-size: 0.65rem; color: rgba(255,255,255,0.4); width: 36px; text-align: right; }
  .ui-kanban { display: grid; grid-template-columns: repeat(3,1fr); gap: 0.5rem; margin-bottom: 0.75rem; }
  .kanban-col { background: rgba(255,255,255,0.04); border-radius: 6px; padding: 0.5rem; }
  .kanban-col-title { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 0.5rem; opacity: 0.5; }
  .kanban-card { background: rgba(255,255,255,0.07); border-radius: 4px; padding: 0.4rem 0.5rem; margin-bottom: 0.35rem; font-size: 0.6rem; color: rgba(255,255,255,0.6); }
  .k-tag { display: inline-block; font-size: 0.55rem; padding: 1px 5px; border-radius: 3px; margin-top: 3px; }
  .church-activity { display: flex; flex-direction: column; gap: 0.4rem; }
  .church-activity-item {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.4rem 0.6rem; border-radius: 6px;
    background: rgba(255,255,255,0.04); font-size: 0.62rem; color: rgba(255,255,255,0.5);
  }
  .activity-icon { width: 18px; height: 18px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0; }
  .church-stat-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 0.5rem; margin-bottom: 0.75rem; }

  /* Features section */
  .features-section { padding: 4rem 6rem; }
  .features-section.bg-ice { background: var(--ice); }
  .features-section.bg-church { background: #e8f5ee; }
  .features-header { margin-bottom: 3rem; }
  .features-header h3 {
    font-family: var(--font-display); font-size: clamp(1.3rem, 2vw, 1.8rem);
    font-weight: 800; letter-spacing: -0.02em; color: var(--navy); margin-bottom: 0.5rem;
  }
  .features-header p { color: var(--text-secondary); font-size: 0.9rem; }
  .features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
  .feature-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 12px; padding: 1.75rem;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .feature-card:hover { border-color: var(--cyan); box-shadow: 0 6px 24px rgba(0,184,219,0.08); }
  .feature-card.church-feat:hover { border-color: #4caf82; box-shadow: 0 6px 24px rgba(26,107,74,0.08); }
  .feature-icon-wrap {
    width: 40px; height: 40px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1rem; font-size: 18px;
    background: var(--sky-light);
  }
  .feature-icon-wrap.green { background: #e8f5ee; }
  .feature-card h4 {
    font-family: var(--font-display); font-size: 0.95rem; font-weight: 700;
    color: var(--navy); margin-bottom: 0.6rem;
  }
  .feature-card ul { list-style: none; padding: 0; margin: 0; }
  .feature-card ul li {
    font-size: 0.82rem; color: var(--text-secondary); padding: 0.2rem 0;
    display: flex; align-items: flex-start; gap: 0.5rem;
  }
  .feature-card ul li::before {
    content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
    margin-top: 0.45rem; background: var(--cyan);
  }
  .feature-card.church-feat ul li::before { background: #4caf82; }

  /* ROI callout */
  .roi-callout {
    background: var(--navy); padding: 3rem 6rem;
    display: flex; align-items: center; gap: 4rem;
    border-top: 1px solid rgba(76,175,130,0.1);
    flex-wrap: wrap;
  }
  .roi-num {
    font-family: var(--font-display); font-size: 3.5rem; font-weight: 800;
    color: #4caf82; letter-spacing: -0.04em; line-height: 1; flex-shrink: 0;
  }
  .roi-text h4 { font-family: var(--font-display); font-weight: 700; color: var(--white); margin-bottom: 0.4rem; font-size: 1rem; }
  .roi-text p { font-size: 0.875rem; color: rgba(255,255,255,0.5); font-weight: 300; }

  /* Divider */
  .product-divider {
    height: 4px;
    background: linear-gradient(90deg, var(--cyan) 0%, var(--cyan) 50%, #1a6b4a 50%, #1a6b4a 100%);
  }

  /* All products grid (search/filter mode) */
  .all-products-section { padding: 4rem 6rem; }
  .search-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 2.5rem; }
  .search-input-wrap { position: relative; flex: 1; min-width: 200px; max-width: 360px; }
  .search-input-wrap svg { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); }
  .search-input-wrap input {
    width: 100%; padding: 0.65rem 0.85rem 0.65rem 2.5rem;
    border: 1px solid var(--border); border-radius: 8px;
    font-size: 0.875rem; font-family: var(--font-body);
    background: var(--white); color: var(--text-primary);
    transition: border-color 0.2s;
  }
  .search-input-wrap input:focus { outline: none; border-color: var(--cyan); }
  .search-select {
    padding: 0.65rem 1rem; border: 1px solid var(--border); border-radius: 8px;
    font-size: 0.875rem; font-family: var(--font-body); background: var(--white); color: var(--text-secondary);
  }
  .search-select:focus { outline: none; border-color: var(--cyan); }

  .all-products-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; }
  .prod-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 12px;
    overflow: hidden; text-decoration: none; display: block;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .prod-card:hover { border-color: var(--cyan); box-shadow: 0 8px 24px rgba(0,184,219,0.1); }
  .prod-card-img {
    height: 150px; background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;
  }
  .prod-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .prod-card-placeholder { font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; color: rgba(0,184,219,0.3); }
  .prod-card-body { padding: 1.1rem 1.25rem; }
  .prod-card-cat { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); }
  .prod-card-name { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--navy); margin: 0.3rem 0 0.4rem; }
  .prod-card-tagline { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.5; }
  .prod-card-footer {
    padding: 0.75rem 1.25rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .prod-price { font-family: var(--font-display); font-size: 0.95rem; font-weight: 800; color: var(--navy); }
  .prod-price.sale { color: #16a34a; }

  /* CTA strip */
  .products-cta-strip {
    background: var(--navy); padding: 4rem 6rem;
    display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap;
    border-top: 1px solid rgba(0,184,219,0.1);
  }
  .products-cta-strip h2 { font-family: var(--font-display); font-weight: 800; color: var(--white); font-size: 1.5rem; letter-spacing: -0.02em; margin-bottom: 0.4rem; }
  .products-cta-strip p { color: rgba(255,255,255,0.55); font-size: 0.9rem; max-width: 420px; font-weight: 300; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
  .products-banner h1 { animation: fadeUp 0.6s 0.2s ease both; }
  .products-banner-sub { animation: fadeUp 0.6s 0.35s ease both; }

  @media (max-width: 1024px) {
    .products-banner-content { padding: 3rem 2rem; }
    .products-filter-bar { padding: 0 2rem; }
    .product-hero-dark { grid-template-columns: 1fr; padding: 3.5rem 2rem; gap: 2.5rem; }
    .features-section { padding: 3rem 2rem; }
    .features-grid { grid-template-columns: 1fr; }
    .roi-callout { padding: 2.5rem 2rem; gap: 1.5rem; }
    .all-products-section { padding: 3rem 2rem; }
    .all-products-grid { grid-template-columns: repeat(2,1fr); }
    .products-cta-strip { padding: 3rem 2rem; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 640px) {
    .all-products-grid { grid-template-columns: 1fr; }
    .roi-callout { flex-direction: column; }
  }
</style>

<!-- BANNER -->
<div class="products-banner">
  <div class="banner-canvas-bg"></div>
  <div class="banner-glow"></div>
  <div class="products-banner-content">
    <div class="banner-breadcrumb-row">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="crumb-sep">/</span>
      <span class="crumb-dot"></span>
      <span class="crumb-current">Our Products</span>
    </div>
    <div class="banner-tag">Software built for Africa</div>
    <h1>Real Software for <em>Real Conditions</em></h1>
    <p class="products-banner-sub">Industry-specific platforms built offline-first, designed for the realities of doing business in Ghana and across our markets. Not adapted from elsewhere — built here, for here.</p>
  </div>
</div>

<!-- FILTER TABS -->
<div class="products-filter-bar" x-data="{ tab: 'washops' }">
  <button class="filter-tab" :class="tab === 'washops' ? 'active' : ''" @click="tab='washops'; document.getElementById('washops').scrollIntoView({behavior:'smooth'})">
    WashOps <span class="filter-count">4</span>
  </button>
  <button class="filter-tab" :class="tab === 'churchops' ? 'active' : ''" @click="tab='churchops'; document.getElementById('churchops').scrollIntoView({behavior:'smooth'})">
    ChurchOps <span class="filter-count">6</span>
  </button>
  @if($allPublished->whereNotIn('linked_product_code', ['washops','churchops'])->where('linked_product_code', '!=', null)->count() > 0 || $allPublished->whereNull('linked_product_code')->count() > 0)
  <button class="filter-tab" :class="tab === 'shop' ? 'active' : ''" @click="tab='shop'; document.getElementById('shop-section').scrollIntoView({behavior:'smooth'})">
    All Products <span class="filter-count">{{ $allPublished->count() }}</span>
  </button>
  @endif
  <a class="filter-tab" href="{{ route('site.book-demo') }}" wire:navigate>Custom Build</a>
</div>

<div class="products-wrap">

  <!-- ══════════════ WASHOPS ══════════════ -->
  <div class="product-section" id="washops">
    <div class="product-hero-dark wash">
      <div class="product-hero-glow-wash"></div>
      <div class="product-hero-grid-wash"></div>
      <div style="position:relative;z-index:2;">
        <div class="product-badge badge-wash">WashOps</div>
        <h2>Complete Laundry Management for <span class="accent-wash">Modern Businesses</span></h2>
        <p>Enterprise-grade desktop application with powerful POS, real-time analytics, automated workflows, and cloud synchronization. Everything you need to run and scale your laundry business.</p>
        <div class="btn-row">
          <a class="btn-cyan" href="{{ route('site.book-demo') }}" wire:navigate>Start Free Trial</a>
          <a class="btn-outline-wash" href="{{ route('site.white-papers') }}" wire:navigate>Read White Paper</a>
        </div>
      </div>
      <!-- WashOps UI Mock -->
      <div class="product-ui-card" style="position:relative;z-index:2;">
        <div class="ui-titlebar">
          <span class="ui-dot r"></span><span class="ui-dot y"></span><span class="ui-dot g"></span>
          <span class="ui-titlebar-label">WashOps — Analytics Dashboard</span>
        </div>
        <div class="ui-body">
          <div class="ui-stat-row">
            <div class="ui-stat"><div class="ui-stat-num" style="color:#00b8db;">₵ 4,280</div><div class="ui-stat-label">Today's revenue</div></div>
            <div class="ui-stat"><div class="ui-stat-num" style="color:#7acfe8;">83</div><div class="ui-stat-label">Orders active</div></div>
            <div class="ui-stat"><div class="ui-stat-num" style="color:#28ca41;">97%</div><div class="ui-stat-label">On-time rate</div></div>
          </div>
          <div class="ui-kanban">
            <div class="kanban-col">
              <div class="kanban-col-title" style="color:#7acfe8;">Intake</div>
              <div class="kanban-card">Bulk wash · 8kg<span class="k-tag" style="background:rgba(0,184,219,0.15);color:#7acfe8;">New</span></div>
              <div class="kanban-card">Dry clean · 3 items</div>
            </div>
            <div class="kanban-col">
              <div class="kanban-col-title" style="color:#ffbd2e;">Processing</div>
              <div class="kanban-card">Express · 5kg<span class="k-tag" style="background:rgba(255,189,46,0.15);color:#ffbd2e;">Urgent</span></div>
            </div>
            <div class="kanban-col">
              <div class="kanban-col-title" style="color:#28ca41;">Ready</div>
              <div class="kanban-card">Order #1047<span class="k-tag" style="background:rgba(40,202,65,0.15);color:#28ca41;">Done</span></div>
            </div>
          </div>
          <div class="ui-bar-row">
            <div class="ui-bar-item"><span class="ui-bar-label">Mon</span><div class="ui-bar-track"><div class="ui-bar-fill" style="width:82%;background:#00b8db;"></div></div><span class="ui-bar-val">₵3.2k</span></div>
            <div class="ui-bar-item"><span class="ui-bar-label">Tue</span><div class="ui-bar-track"><div class="ui-bar-fill" style="width:65%;background:#00b8db;"></div></div><span class="ui-bar-val">₵2.5k</span></div>
            <div class="ui-bar-item"><span class="ui-bar-label">Wed</span><div class="ui-bar-track"><div class="ui-bar-fill" style="width:100%;background:#00b8db;"></div></div><span class="ui-bar-val">₵4.3k</span></div>
          </div>
        </div>
      </div>
    </div>
    <!-- WashOps Features -->
    <div class="features-section bg-ice">
      <div class="features-header">
        <h3>Powerful Features Built for Laundry Businesses</h3>
        <p>Everything you need to manage orders, delight customers, and grow your business — all in one place.</p>
      </div>
      <div class="features-grid">
        @php
          $washFeatures = [
            ['📊','Analytics Dashboard',['Revenue tracking and forecasting','Order volume analytics','Daily bottleneck identification','Staff performance metrics','Customer behavior insights','Custom date range reporting']],
            ['🖥️','Advanced Point of Sale',['Quick order booking interface','Multiple payment methods support','Partial and full payment processing','Customer creation and retrieval','Thermal printer integration','Receipt customization']],
            ['📋','Kanban Orders Board',['Drag-and-drop status updates','Color-coded priority system','Real-time order tracking','Team collaboration features','Automated status notifications','Custom workflow stages']],
            ['☁️','Enterprise Database Management',['Automatic cloud backup','Manual backup and restore','Push to cloud / Pull from cloud','Conflict resolution strategies','Disaster recovery protocols','Data encryption and security']],
          ];
        @endphp
        @foreach($washFeatures as [$icon, $title, $items])
        <div class="feature-card">
          <div class="feature-icon-wrap">{{ $icon }}</div>
          <h4>{{ $title }}</h4>
          <ul>@foreach($items as $item)<li>{{ $item }}</li>@endforeach</ul>
        </div>
        @endforeach
      </div>
    </div>
    <!-- Dynamic WashOps products from DB -->
    @if($allPublished->where('linked_product_code', 'washops')->count() > 0)
    <div class="all-products-section" style="background:var(--white);">
      <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:var(--cyan);text-transform:uppercase;margin-bottom:0.5rem;">Available Now</p>
      <h3 style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--navy);margin-bottom:2rem;">WashOps Editions</h3>
      <div class="all-products-grid">
        @foreach($allPublished->where('linked_product_code', 'washops') as $product)
        <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate class="prod-card">
          <div class="prod-card-img">
            @if($product->cover_image)<img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}">
            @else<div class="prod-card-placeholder">{{ strtoupper(substr($product->name,0,2)) }}</div>@endif
          </div>
          <div class="prod-card-body">
            <div class="prod-card-cat">{{ $product->category }}</div>
            <div class="prod-card-name">{{ $product->name }}</div>
            @if($product->tagline)<div class="prod-card-tagline">{{ $product->tagline }}</div>@endif
          </div>
          <div class="prod-card-footer">
            <div>
              @if($product->is_on_sale)
                <div style="font-size:0.72rem;color:var(--text-muted);text-decoration:line-through;">GHS {{ number_format($product->price,2) }}</div>
                <div class="prod-price sale">GHS {{ number_format($product->sale_price,2) }}</div>
              @else
                <div class="prod-price">GHS {{ number_format($product->price,2) }}</div>
              @endif
            </div>
            <span style="font-size:0.78rem;font-weight:600;color:var(--cyan);font-family:var(--font-display);">View →</span>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <!-- DIVIDER -->
  <div class="product-divider"></div>

  <!-- ══════════════ CHURCHOPS ══════════════ -->
  <div class="product-section" id="churchops">
    <div class="product-hero-dark church">
      <div class="product-hero-glow-church"></div>
      <div class="product-hero-grid-church"></div>
      <div style="position:relative;z-index:2;">
        <div class="product-badge badge-church">ChurchOps</div>
        <h2>Church Management That Works — <span class="accent-church">Even When the Internet Doesn't</span></h2>
        <p>The first offline-first church management system built specifically for Ghanaian churches. Manage members, finances, and ministries with 100% uptime.</p>
        <div class="btn-row">
          <a class="btn-green" href="{{ route('site.book-demo') }}" wire:navigate>Book Free Demo</a>
          <a class="btn-outline-green" href="{{ route('site.white-papers') }}" wire:navigate>Download White Paper</a>
        </div>
      </div>
      <!-- ChurchOps UI Mock -->
      <div class="product-ui-card church-card" style="position:relative;z-index:2;">
        <div class="ui-titlebar church">
          <span class="ui-dot r"></span><span class="ui-dot y"></span><span class="ui-dot g"></span>
          <div class="ui-offline-badge"><span class="pulse-dot"></span> Offline — synced 2m ago</div>
          <span class="ui-titlebar-label">ChurchOps</span>
        </div>
        <div class="ui-body">
          <div class="church-stat-grid">
            <div class="ui-stat church-stat"><div class="ui-stat-num" style="color:#4caf82;">1,240</div><div class="ui-stat-label">Active members</div></div>
            <div class="ui-stat church-stat"><div class="ui-stat-num" style="color:#7acfe8;">₵ 18,400</div><div class="ui-stat-label">This month tithes</div></div>
            <div class="ui-stat church-stat"><div class="ui-stat-num" style="color:#ffbd2e;">94%</div><div class="ui-stat-label">Attendance rate</div></div>
            <div class="ui-stat church-stat"><div class="ui-stat-num" style="color:#7acfe8;">3</div><div class="ui-stat-label">Branches synced</div></div>
          </div>
          <div class="church-activity">
            <div class="church-activity-item"><div class="activity-icon" style="background:rgba(76,175,130,0.2);">🎂</div><span>Birthday SMS sent to 4 members — MTN</span></div>
            <div class="church-activity-item"><div class="activity-icon" style="background:rgba(0,184,219,0.15);">💳</div><span>MoMo offering recorded — ₵240</span></div>
            <div class="church-activity-item"><div class="activity-icon" style="background:rgba(255,189,46,0.15);">👤</div><span>New visitor registered — Accra Central</span></div>
            <div class="church-activity-item"><div class="activity-icon" style="background:rgba(76,175,130,0.15);">☁️</div><span>Cloud sync complete — 3 branches · 0 conflicts</span></div>
          </div>
        </div>
      </div>
    </div>
    <!-- ChurchOps Features -->
    <div class="features-section bg-church">
      <div class="features-header">
        <h3>Why Churches Choose ChurchOps</h3>
        <p>Designed for Ghana's reality: unreliable internet, frequent power cuts, and the need for local payments.</p>
      </div>
      <div class="features-grid">
        @php
          $churchFeatures = [
            ['📡','100% Offline Operation',['Full functionality during power cuts','Automatic cloud sync when online','Zero data loss guarantee','Local network (LAN) collaboration']],
            ['💰','Complete Financial Management',['MTN, Vodafone, AirtelTigo Mobile Money','Tithe and offering tracking','Member contribution statements','Full financial reporting and audit trail']],
            ['👥','Member Management',['Complete member profiles','Attendance tracking and trends','Visitor follow-up workflows','Ministry assignments and family links']],
            ['📣','Automated Communication',['Bulk SMS via local gateways','Automated birthday greetings','Service reminders and event notifications','Email campaigns to members']],
            ['🏛️','Multi-Branch Ready',['Manage all locations from HQ','Consolidated cross-branch reporting','Branch autonomy with HQ visibility','Easy member transfers between branches']],
            ['📈','Powerful Analytics',['Financial dashboards and trends','Attendance pattern analysis','Member engagement scoring','Custom report builder']],
          ];
        @endphp
        @foreach($churchFeatures as [$icon, $title, $items])
        <div class="feature-card church-feat">
          <div class="feature-icon-wrap green">{{ $icon }}</div>
          <h4>{{ $title }}</h4>
          <ul>@foreach($items as $item)<li>{{ $item }}</li>@endforeach</ul>
        </div>
        @endforeach
      </div>
    </div>
    <!-- ROI callout -->
    <div class="roi-callout">
      <div class="roi-num">640–1,940%</div>
      <div class="roi-text">
        <h4>Documented return on investment</h4>
        <p>Churches using ChurchOps report payback in less than 5 days. Download the white paper to see the full ROI analysis across different church sizes.</p>
      </div>
      <a class="btn-green" href="{{ route('site.white-papers') }}" wire:navigate style="flex-shrink:0;">Get the White Paper</a>
    </div>
    <!-- Dynamic ChurchOps products from DB -->
    @if($allPublished->where('linked_product_code', 'churchops')->count() > 0)
    <div class="all-products-section" style="background:var(--white);">
      <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:#1a6b4a;text-transform:uppercase;margin-bottom:0.5rem;">Available Now</p>
      <h3 style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--navy);margin-bottom:2rem;">ChurchOps Editions</h3>
      <div class="all-products-grid">
        @foreach($allPublished->where('linked_product_code', 'churchops') as $product)
        <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate class="prod-card">
          <div class="prod-card-img" style="background:linear-gradient(135deg,#0f2d1f,#1a3d2a);">
            @if($product->cover_image)<img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}">
            @else<div class="prod-card-placeholder" style="color:rgba(76,175,130,0.4);">{{ strtoupper(substr($product->name,0,2)) }}</div>@endif
          </div>
          <div class="prod-card-body">
            <div class="prod-card-cat">{{ $product->category }}</div>
            <div class="prod-card-name">{{ $product->name }}</div>
            @if($product->tagline)<div class="prod-card-tagline">{{ $product->tagline }}</div>@endif
          </div>
          <div class="prod-card-footer">
            <div>
              @if($product->is_on_sale)
                <div style="font-size:0.72rem;color:var(--text-muted);text-decoration:line-through;">GHS {{ number_format($product->price,2) }}</div>
                <div class="prod-price sale">GHS {{ number_format($product->sale_price,2) }}</div>
              @else
                <div class="prod-price">GHS {{ number_format($product->price,2) }}</div>
              @endif
            </div>
            <span style="font-size:0.78rem;font-weight:600;color:#1a6b4a;font-family:var(--font-display);">View →</span>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <!-- ALL PRODUCTS (if any not tied to product codes, or with search) -->
  @if($allPublished->whereNull('linked_product_code')->count() > 0)
  <div class="product-divider" style="background:linear-gradient(90deg,var(--cyan),var(--cyan));"></div>
  <div class="all-products-section" id="shop-section">
    <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:var(--cyan);text-transform:uppercase;margin-bottom:0.5rem;">Digital Store</p>
    <h3 style="font-family:var(--font-display);font-size:1.6rem;font-weight:800;color:var(--navy);margin-bottom:0.5rem;">All Products</h3>
    <p style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:2rem;">Software, tools, templates, and more — instantly available.</p>

    <!-- Search/filter -->
    <div class="search-bar">
      <div class="search-input-wrap">
        <svg style="width:16px;height:16px;color:var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products...">
      </div>
      <select wire:model.live="filterCategory" class="search-select">
        <option value="">All Categories</option>
        <option value="software">Software</option>
        <option value="template">Template</option>
        <option value="course">Course</option>
        <option value="service">Service</option>
      </select>
    </div>

    @if($products->isEmpty())
    <div style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
      <p style="font-size:1rem;font-weight:600;">No products found</p>
      <p style="font-size:0.875rem;margin-top:0.5rem;">Try a different search or category.</p>
    </div>
    @else
    <div class="all-products-grid">
      @foreach($products as $product)
      <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate class="prod-card">
        <div class="prod-card-img">
          @if($product->cover_image)<img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}">
          @else<div class="prod-card-placeholder">{{ strtoupper(substr($product->name,0,2)) }}</div>@endif
          @if($product->is_on_sale)<span style="position:absolute;top:0.75rem;right:0.75rem;background:#ef4444;color:white;padding:0.2rem 0.6rem;border-radius:100px;font-size:0.65rem;font-weight:700;">SALE</span>@endif
        </div>
        <div class="prod-card-body">
          <div class="prod-card-cat">{{ $product->category }}</div>
          <div class="prod-card-name">{{ $product->name }}</div>
          @if($product->tagline)<div class="prod-card-tagline">{{ $product->tagline }}</div>@endif
        </div>
        <div class="prod-card-footer">
          <div>
            @if($product->is_on_sale)
              <div style="font-size:0.72rem;color:var(--text-muted);text-decoration:line-through;">GHS {{ number_format($product->price,2) }}</div>
              <div class="prod-price sale">GHS {{ number_format($product->sale_price,2) }}</div>
            @else
              <div class="prod-price">GHS {{ number_format($product->price,2) }}</div>
            @endif
          </div>
          <span style="font-size:0.78rem;font-weight:600;color:var(--cyan);font-family:var(--font-display);">View →</span>
        </div>
      </a>
      @endforeach
    </div>
    @if($products->hasPages())<div style="margin-top:2rem;">{{ $products->links() }}</div>@endif
    @endif
  </div>
  @endif

</div>

<!-- CTA STRIP -->
<div class="products-cta-strip" id="cta">
  <div>
    <h2>Need something built for your industry?</h2>
    <p>WashOps and ChurchOps are two of our products. If your sector isn't covered, we build custom — from the ground up, for your exact operations.</p>
  </div>
  <a class="btn-cyan" href="{{ route('site.book-demo') }}" wire:navigate style="padding:0.9rem 2.2rem;font-size:0.95rem;">Start a Conversation</a>
</div>

</div>
