<?php

use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public ShopProduct $product;

    public bool $showPurchaseModal = false;
    public bool $purchaseSuccess = false;

    public string $buyerName  = '';
    public string $buyerEmail = '';
    public string $buyerPhone = '';
    public string $buyerNote  = '';

    public function mount(string $slug): void
    {
        $this->product = ShopProduct::published()->where('slug', $slug)->firstOrFail();
    }

    public function openPurchase(): void
    {
        if (auth()->check()) {
            $this->buyerName  = auth()->user()->name;
            $this->buyerEmail = auth()->user()->email;
        }
        $this->showPurchaseModal = true;
    }

    public function purchase(): void
    {
        $this->validate([
            'buyerName'  => 'required|string|max:200',
            'buyerEmail' => 'required|email',
        ]);

        $price = $this->product->effective_price;

        $order = \App\Models\Order::create([
            'customer_user_id' => auth()->id(),
            'guest_name'       => auth()->check() ? null : $this->buyerName,
            'guest_email'      => auth()->check() ? null : $this->buyerEmail,
            'guest_phone'      => $this->buyerPhone,
            'subtotal'         => $price,
            'total'            => $price,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'customer_note'    => $this->buyerNote,
        ]);

        $order->items()->create([
            'shop_product_id' => $this->product->id,
            'product_name'    => $this->product->name,
            'product_version' => $this->product->version,
            'unit_price'      => $price,
            'quantity'        => 1,
            'total'           => $price,
        ]);

        $this->showPurchaseModal = false;
        $this->purchaseSuccess   = true;
    }

    public function render(): \Illuminate\View\View
    {
        $relatedProducts = ShopProduct::published()
            ->where('id', '!=', $this->product->id)
            ->when($this->product->linked_product_code, fn($q) => $q->where('linked_product_code', $this->product->linked_product_code))
            ->orWhere('category', $this->product->category)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();

        return view('livewire.pages.site.product-detail', compact('relatedProducts'))
            ->title($this->product->name . ' — Exchosoft Consult');
    }
}; ?>

<div>
<style>
  /* Product Detail */
  .detail-banner {
    background: var(--navy); position: relative; overflow: hidden;
    padding: 3.5rem 6rem 3rem;
  }
  .detail-banner-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(0,184,219,0.12) 1px, transparent 1px);
    background-size: 32px 32px; pointer-events: none;
  }
  .detail-banner-glow {
    position: absolute; inset: 0;
    background: radial-gradient(circle at 80% 50%, rgba(0,184,219,0.1) 0%, transparent 60%);
    pointer-events: none;
  }
  .detail-breadcrumb {
    position: relative; z-index: 2;
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.78rem;
  }
  .detail-breadcrumb a { color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.2s; }
  .detail-breadcrumb a:hover { color: var(--cyan); }
  .detail-breadcrumb .sep { color: rgba(255,255,255,0.2); }
  .detail-breadcrumb .current { color: var(--cyan); font-weight: 500; }

  /* Product Body */
  .product-detail-body { padding: 4rem 6rem; }
  .product-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start; }
  .product-image-area {
    border-radius: 16px; overflow: hidden;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    position: sticky; top: 80px; border: 1px solid rgba(0,184,219,0.12);
  }
  .product-image-area img { width: 100%; height: 100%; object-fit: cover; }
  .product-image-placeholder {
    font-family: var(--font-display); font-size: 6rem; font-weight: 800;
    color: rgba(0,184,219,0.2);
  }

  /* Gallery */
  .product-gallery { display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
  .gallery-thumb {
    width: 64px; height: 64px; border-radius: 8px; overflow: hidden;
    border: 2px solid var(--border); cursor: pointer; transition: border-color 0.2s;
  }
  .gallery-thumb:hover { border-color: var(--cyan); }
  .gallery-thumb img { width: 100%; height: 100%; object-fit: cover; }

  /* Info side */
  .product-info {}
  .product-tags { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; }
  .product-tag {
    font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
    padding: 0.25rem 0.7rem; border-radius: 100px;
    background: var(--sky-light); color: var(--cyan-deep);
    display: inline-block;
  }
  .product-tag.green { background: #e8f5ee; color: #1a6b4a; }
  .product-tag.grey { background: var(--ice); color: var(--text-muted); }
  .product-name {
    font-family: var(--font-display); font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 800; color: var(--navy); letter-spacing: -0.03em; line-height: 1.15;
    margin-bottom: 0.75rem;
  }
  .product-tagline-text { font-size: 1.05rem; color: var(--text-secondary); margin-bottom: 1.5rem; font-weight: 300; }
  .product-desc { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 2rem; }
  .product-price-block { display: flex; align-items: flex-end; gap: 1rem; margin-bottom: 1.75rem; }
  .product-price-main {
    font-family: var(--font-display); font-size: 2.2rem; font-weight: 800;
    color: var(--navy); letter-spacing: -0.03em;
  }
  .product-price-main.sale { color: #16a34a; }
  .product-price-original { font-size: 1.1rem; color: var(--text-muted); text-decoration: line-through; margin-bottom: 0.35rem; }
  .product-sale-badge {
    background: #fef2f2; color: #dc2626; font-size: 0.75rem; font-weight: 700;
    padding: 0.2rem 0.6rem; border-radius: 100px;
  }

  .product-actions { display: flex; gap: 0.85rem; flex-wrap: wrap; margin-bottom: 2rem; }
  .btn-buy {
    background: var(--cyan); color: var(--white);
    padding: 0.9rem 2.2rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 0.95rem; font-weight: 700;
    border: none; cursor: pointer; transition: background 0.2s, transform 0.15s;
    display: inline-flex; align-items: center; gap: 0.5rem;
  }
  .btn-buy:hover { background: var(--cyan-dark); transform: translateY(-1px); }
  .btn-demo-link {
    background: transparent; color: var(--text-secondary);
    padding: 0.9rem 1.75rem; border-radius: 8px; border: 1px solid var(--border);
    font-family: var(--font-display); font-size: 0.875rem; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
    transition: border-color 0.2s, color 0.2s;
  }
  .btn-demo-link:hover { border-color: var(--cyan); color: var(--navy); }

  /* Features list */
  .features-list-block {
    background: var(--ice); border-radius: 12px; padding: 1.5rem;
    margin-bottom: 1.5rem;
  }
  .features-list-block h4 {
    font-family: var(--font-display); font-size: 0.8rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted);
    margin-bottom: 1rem;
  }
  .features-list-block ul { list-style: none; padding: 0; margin: 0; }
  .features-list-block ul li {
    font-size: 0.875rem; color: var(--text-secondary);
    display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.3rem 0;
  }
  .features-list-block ul li svg { flex-shrink: 0; margin-top: 1px; color: var(--cyan); }

  /* Tech stack */
  .tech-stack-block { margin-bottom: 1.5rem; }
  .tech-stack-block h4 {
    font-family: var(--font-display); font-size: 0.8rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted);
    margin-bottom: 0.75rem;
  }
  .tech-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; }
  .tech-tag {
    background: var(--navy); color: rgba(255,255,255,0.65);
    padding: 0.3rem 0.75rem; border-radius: 6px; font-size: 0.78rem; font-weight: 500;
    border: 1px solid rgba(0,184,219,0.15);
  }

  /* Links */
  .product-links { display: flex; gap: 1rem; flex-wrap: wrap; }
  .product-link {
    font-size: 0.83rem; font-weight: 600; color: var(--cyan); text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.35rem;
    transition: color 0.2s;
  }
  .product-link:hover { color: var(--cyan-dark); }

  /* Full description */
  .product-full-desc { margin-top: 4rem; padding-top: 3rem; border-top: 1px solid var(--border); }
  .product-full-desc h2 {
    font-family: var(--font-display); font-size: 1.5rem; font-weight: 800;
    color: var(--navy); letter-spacing: -0.02em; margin-bottom: 1.5rem;
  }
  .product-full-desc .prose { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.85; }
  .product-full-desc .prose h3 { font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: var(--navy); margin: 1.5rem 0 0.5rem; }
  .product-full-desc .prose p { margin-bottom: 1rem; }
  .product-full-desc .prose ul { padding-left: 1.25rem; margin-bottom: 1rem; }
  .product-full-desc .prose ul li { margin-bottom: 0.35rem; }

  /* Related products */
  .related-products { padding: 4rem 6rem; background: var(--ice); }
  .related-products h3 {
    font-family: var(--font-display); font-size: 1.3rem; font-weight: 800;
    color: var(--navy); letter-spacing: -0.02em; margin-bottom: 2rem;
  }
  .related-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; }
  .related-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 12px;
    overflow: hidden; text-decoration: none; display: block;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .related-card:hover { border-color: var(--cyan); box-shadow: 0 8px 24px rgba(0,184,219,0.1); }
  .related-img {
    height: 120px; background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    display: flex; align-items: center; justify-content: center;
  }
  .related-img img { width: 100%; height: 100%; object-fit: cover; }
  .related-body { padding: 1rem 1.25rem; }
  .related-name { font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; color: var(--navy); }
  .related-price { font-size: 0.85rem; font-weight: 700; color: var(--cyan); margin-top: 0.35rem; font-family: var(--font-display); }

  /* Purchase Modal */
  .modal-overlay {
    position: fixed; inset: 0; z-index: 1000;
    display: flex; align-items: center; justify-content: center; padding: 1rem;
  }
  .modal-backdrop { position: fixed; inset: 0; background: rgba(13,33,55,0.7); }
  .modal-box {
    position: relative; background: var(--white); border-radius: 16px;
    padding: 2rem; width: 100%; max-width: 460px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.2);
    z-index: 1;
  }
  .modal-title { font-family: var(--font-display); font-size: 1.1rem; font-weight: 800; color: var(--navy); margin-bottom: 0.25rem; }
  .modal-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; }
  .form-group { margin-bottom: 1rem; }
  .form-label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem; font-family: var(--font-display); text-transform: uppercase; letter-spacing: 0.05em; }
  .form-input {
    width: 100%; padding: 0.7rem 0.9rem; border: 1px solid var(--border); border-radius: 8px;
    font-size: 0.875rem; font-family: var(--font-body); color: var(--text-primary);
    transition: border-color 0.2s;
  }
  .form-input:focus { outline: none; border-color: var(--cyan); }
  .form-note { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.75rem; }
  .modal-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; }
  .btn-modal-submit {
    flex: 1; background: var(--cyan); color: var(--white);
    padding: 0.8rem; border-radius: 8px; border: none; cursor: pointer;
    font-family: var(--font-display); font-size: 0.9rem; font-weight: 700;
    transition: background 0.2s;
  }
  .btn-modal-submit:hover { background: var(--cyan-dark); }
  .btn-modal-cancel {
    flex: 1; background: var(--ice); color: var(--text-secondary);
    padding: 0.8rem; border-radius: 8px; border: none; cursor: pointer;
    font-family: var(--font-display); font-size: 0.9rem; font-weight: 600;
    transition: background 0.2s;
  }
  .btn-modal-cancel:hover { background: var(--border); }

  /* Success */
  .purchase-success {
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px;
    padding: 2.5rem; text-align: center; margin: 2rem 6rem;
  }
  .success-icon {
    width: 56px; height: 56px; border-radius: 50%; background: #dcfce7;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;
  }
  .success-icon svg { width: 28px; height: 28px; color: #16a34a; }
  .success-title { font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: #14532d; margin-bottom: 0.5rem; }
  .success-msg { font-size: 0.9rem; color: #166534; margin-bottom: 1.25rem; }

  @media (max-width: 1024px) {
    .detail-banner { padding: 2.5rem 2rem; }
    .product-detail-body { padding: 2.5rem 2rem; }
    .product-detail-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    .product-image-area { position: static; aspect-ratio: 16/9; }
    .related-products { padding: 3rem 2rem; }
    .related-grid { grid-template-columns: repeat(2,1fr); }
    .purchase-success { margin: 2rem; }
  }
  @media (max-width: 640px) {
    .related-grid { grid-template-columns: 1fr; }
    .product-price-main { font-size: 1.8rem; }
  }
</style>

<!-- BANNER -->
<div class="detail-banner">
  <div class="detail-banner-dots"></div>
  <div class="detail-banner-glow"></div>
  <div class="detail-breadcrumb">
    <a href="{{ route('home') }}" wire:navigate>Home</a>
    <span class="sep">/</span>
    <a href="{{ route('site.products') }}" wire:navigate>Products</a>
    <span class="sep">/</span>
    <span class="current">{{ $product->name }}</span>
  </div>
</div>

@if($purchaseSuccess)
<div class="purchase-success">
  <div class="success-icon">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
  </div>
  <div class="success-title">Order Placed Successfully!</div>
  <p class="success-msg">Your order for <strong>{{ $product->name }}</strong> has been placed. We'll contact you with payment details and your license key shortly.</p>
  <a href="{{ route('home') }}" wire:navigate class="btn-buy" style="display:inline-flex;margin:0 auto;">Back to Home</a>
</div>
@endif

<!-- PRODUCT DETAIL -->
<section class="product-detail-body">
  <div class="product-detail-grid">

    {{-- Image --}}
    <div>
      <div class="product-image-area">
        @if($product->cover_image)
          <img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}">
        @else
          <div class="product-image-placeholder">{{ strtoupper(substr($product->name,0,2)) }}</div>
        @endif
      </div>
      {{-- Gallery --}}
      @if($product->gallery && count($product->gallery) > 0)
      <div class="product-gallery">
        @foreach($product->gallery as $img)
        <div class="gallery-thumb">
          <img src="{{ asset('storage/'.$img) }}" alt="Gallery image">
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Info --}}
    <div class="product-info">
      {{-- Tags --}}
      <div class="product-tags">
        @if($product->category)<span class="product-tag">{{ $product->category }}</span>@endif
        @if($product->platform)<span class="product-tag grey">{{ $product->platform }}</span>@endif
        @if($product->version)<span class="product-tag grey">v{{ $product->version }}</span>@endif
        @if($product->linked_product_code === 'churchops')<span class="product-tag green">ChurchOps</span>@endif
        @if($product->linked_product_code === 'washops')<span class="product-tag">WashOps</span>@endif
      </div>

      <h1 class="product-name">{{ $product->name }}</h1>
      @if($product->tagline)<p class="product-tagline-text">{{ $product->tagline }}</p>@endif
      @if($product->description)<p class="product-desc">{{ $product->description }}</p>@endif

      {{-- Pricing --}}
      <div class="product-price-block">
        @if($product->is_on_sale)
          <div>
            <div class="product-price-original">GHS {{ number_format($product->price, 2) }}</div>
            <div class="product-price-main sale">GHS {{ number_format($product->sale_price, 2) }}</div>
          </div>
          <span class="product-sale-badge">SALE</span>
        @else
          <div class="product-price-main">GHS {{ number_format($product->price, 2) }}</div>
        @endif
      </div>

      {{-- Actions --}}
      <div class="product-actions">
        <button wire:click="openPurchase" class="btn-buy">
          <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          Buy Now
        </button>
        @if($product->demo_url)
        <a href="{{ $product->demo_url }}" target="_blank" class="btn-demo-link">
          <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Live Demo
        </a>
        @endif
        <a href="{{ route('site.book-demo') }}" wire:navigate class="btn-demo-link">
          <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Book Demo
        </a>
      </div>

      {{-- Features --}}
      @if($product->features && count($product->features) > 0)
      <div class="features-list-block">
        <h4>Key Features</h4>
        <ul>
          @foreach($product->features as $feature)
          <li>
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ $feature }}
          </li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Tech Stack --}}
      @if($product->tech_stack && count($product->tech_stack) > 0)
      <div class="tech-stack-block">
        <h4>Tech Stack</h4>
        <div class="tech-tags">
          @foreach($product->tech_stack as $tech)
          <span class="tech-tag">{{ $tech }}</span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Extra Links --}}
      <div class="product-links">
        @if($product->documentation_url)
        <a href="{{ $product->documentation_url }}" target="_blank" class="product-link">
          <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          Documentation
        </a>
        @endif
        @if($product->download_url)
        <a href="{{ $product->download_url }}" target="_blank" class="product-link">
          <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          Download
        </a>
        @endif
      </div>
    </div>
  </div>

  {{-- Full Description (Markdown rendered) --}}
  @if($product->full_description)
  @php
    // Simple markdown-to-HTML conversion for safe rendering
    $md = $product->full_description;
    // Use Str helper for basic markdown rendering or just nl2br
    // We'll use a simple conversion here since no markdown package is required
    $mdHtml = $md;
    // Headings
    $mdHtml = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $mdHtml);
    $mdHtml = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $mdHtml);
    $mdHtml = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $mdHtml);
    // Bold / italic
    $mdHtml = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $mdHtml);
    $mdHtml = preg_replace('/_(.+?)_/', '<em>$1</em>', $mdHtml);
    // Unordered lists
    $mdHtml = preg_replace('/^[-*] (.+)$/m', '<li>$1</li>', $mdHtml);
    $mdHtml = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $mdHtml);
    // Paragraphs: wrap double-newline blocks
    $paras = array_filter(preg_split('/\n{2,}/', $mdHtml));
    $mdHtml = implode("\n", array_map(function($p) {
        $p = trim($p);
        if (preg_match('/^<(h[1-6]|ul|ol|li)/', $p)) return $p;
        return "<p>{$p}</p>";
    }, $paras));
    $mdHtml = nl2br($mdHtml);
  @endphp
  <div class="product-full-desc">
    <h2>Product Details</h2>
    <div class="prose">{!! $mdHtml !!}</div>
  </div>
  @endif
</section>

{{-- Related Products --}}
@if($relatedProducts->isNotEmpty())
<section class="related-products">
  <h3>Related Products</h3>
  <div class="related-grid">
    @foreach($relatedProducts as $related)
    <a href="{{ route('site.products.show', $related->slug) }}" wire:navigate class="related-card">
      <div class="related-img">
        @if($related->cover_image)<img src="{{ asset('storage/'.$related->cover_image) }}" alt="{{ $related->name }}">
        @else<span style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:rgba(0,184,219,0.3);">{{ strtoupper(substr($related->name,0,2)) }}</span>@endif
      </div>
      <div class="related-body">
        <div class="related-name">{{ $related->name }}</div>
        <div class="related-price">GHS {{ number_format($related->effective_price, 2) }}</div>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif

{{-- Purchase Modal --}}
@if($showPurchaseModal)
<div class="modal-overlay">
  <div class="modal-backdrop" wire:click="$set('showPurchaseModal', false)"></div>
  <div class="modal-box">
    <div class="modal-title">Complete Your Order</div>
    <div class="modal-subtitle">{{ $product->name }} &mdash; GHS {{ number_format($product->effective_price, 2) }}</div>
    <div class="form-group">
      <label class="form-label">Full Name *</label>
      <input wire:model="buyerName" type="text" class="form-input" placeholder="Your full name">
      @error('buyerName') <p style="font-size:0.78rem;color:#dc2626;margin-top:0.35rem;">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
      <label class="form-label">Email Address *</label>
      <input wire:model="buyerEmail" type="email" class="form-input" placeholder="your@email.com">
      @error('buyerEmail') <p style="font-size:0.78rem;color:#dc2626;margin-top:0.35rem;">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
      <label class="form-label">Phone</label>
      <input wire:model="buyerPhone" type="tel" class="form-input" placeholder="Optional">
    </div>
    <div class="form-group">
      <label class="form-label">Notes</label>
      <textarea wire:model="buyerNote" rows="2" class="form-input" placeholder="Any specific requirements..." style="resize:none;"></textarea>
    </div>
    <p class="form-note">We'll send you payment instructions and your license key once the order is confirmed.</p>
    <div class="modal-actions">
      <button wire:click="purchase" class="btn-modal-submit">Place Order</button>
      <button wire:click="$set('showPurchaseModal', false)" class="btn-modal-cancel">Cancel</button>
    </div>
  </div>
</div>
@endif

</div>
