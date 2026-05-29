<?php

use App\Models\ProductPageSection;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Products — Exchosoft Consult')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $products = ShopProduct::published()->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('tagline', 'like', '%' . $this->search . '%'))->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))->orderBy('sort_order')->latest()->paginate(12);

        // Group published products by linked_product_code for the hero sections
        $featuredGroups = ShopProduct::published()->whereNotNull('linked_product_code')->orderBy('sort_order')->get()->groupBy('linked_product_code');

        $allPublished = ShopProduct::published()->orderBy('sort_order')->get();

        // Load dynamic page sections for known product codes
        $washSections = ProductPageSection::getForProduct('washops');
        $churchSections = ProductPageSection::getForProduct('churchops');

        // Get unique linked product codes that have sections
        $linkedCodes = $featuredGroups->keys()->toArray();

        return view('pages.site.products', compact('products', 'featuredGroups', 'allPublished', 'washSections', 'churchSections', 'linkedCodes'));
    }
}; ?>

<div>
    <style>
        /* ── PRODUCTS PAGE STYLES ── */

        /* HERO */
        .products-hero {
            min-height: 80vh;
            background: #08121d;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 8rem 6rem 5rem;
        }

        .products-hero-bg {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 70% 50%, rgba(0, 184, 219, 0.07) 0%, transparent 60%),
                radial-gradient(circle at 20% 80%, rgba(122, 207, 232, 0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        .products-hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0, 184, 219, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 184, 219, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        #products-particle-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.4;
        }

        #products-radar-canvas {
            position: absolute;
            top: 50%;
            right: 8%;
            transform: translateY(-50%);
            width: 480px;
            height: 480px;
            pointer-events: none;
            z-index: 1;
        }

        .products-hero-content {
            position: relative;
            z-index: 10;
            max-width: 680px;
        }

        .products-hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 184, 219, 0.12);
            border: 1px solid rgba(0, 184, 219, 0.25);
            color: var(--cyan);
            padding: 0.35rem 0.9rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }

        .products-hero-tag span {
            width: 6px;
            height: 6px;
            background: var(--cyan);
            border-radius: 50%;
            display: block;
        }

        .products-hero h1 {
            font-family: var(--font-display);
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.05;
            letter-spacing: -0.04em;
            margin-bottom: 1.5rem;
        }

        .products-hero h1 em {
            color: var(--cyan);
            font-style: normal;
        }

        .products-hero-sub {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.6);
            max-width: 540px;
            font-weight: 300;
            line-height: 1.8;
        }

        /* CATALOG SECTION */
        .products-catalog {
            padding: 6rem;
            background: var(--white);
            position: relative;
        }

        .catalog-header {
            max-width: 1200px;
            margin: 0 auto 4rem;
        }

        .cat-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--cyan);
            text-transform: uppercase;
            margin-bottom: 1rem;
            display: block;
        }

        .cat-h2 {
            font-family: var(--font-display);
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            font-weight: 800;
            color: var(--navy);
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .cat-sub {
            font-size: 1rem;
            color: var(--text-secondary);
            max-width: 560px;
            font-weight: 300;
        }

        /* PRODUCT CARDS GRID */
        .pcard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .pcard {
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 500px;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--white);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 20px rgba(0, 184, 219, 0.03);
        }

        .pcard:not(.coming):hover {
            transform: translateY(-10px);
            border-color: var(--cyan);
            box-shadow: 0 20px 40px rgba(0, 184, 219, 0.12);
        }

        .pcard.coming {
            cursor: default;
        }

        .pcard-img-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
            height: 100%;
        }

        .pcard-img {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: transform 0.8s ease;
        }

        .pcard:not(.coming):hover .pcard-img {
            transform: scale(1.05);
        }

        .pcard-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(13, 33, 55, 0.18) 0%, rgba(13, 33, 55, 0.88) 100%);
            z-index: 1;
        }

        .pcard-content {
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 2.5rem;
            color: var(--white);
        }

        .pcard-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: auto;
        }

        .pcard-sector {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .pcard-coming-badge {
            background: var(--cyan);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .pcard-body {
            margin-top: 2rem;
        }

        .pcard-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .pcard-text {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.65;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .pcard-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pcard-btn-cyan {
            background: var(--cyan);
            color: white;
        }

        .pcard-btn-green {
            background: #1db954;
            color: white;
        }

        .pcard-btn-disabled {
            background: rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.5);
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pcard-btn:not(.pcard-btn-disabled):hover {
            gap: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* FILTER TABS (sticky beneath nav) */
        .products-filter-bar {
            background: var(--ice);
            border-bottom: 1px solid var(--border);
            padding: 0 6rem;
            display: flex;
            align-items: center;
            gap: 0;
            position: sticky;
            top: 58px;
            z-index: 50;
            overflow-x: auto;
        }

        .filter-tab {
            padding: 1.1rem 1.75rem;
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
            text-decoration: none;
            white-space: nowrap;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }

        .filter-tab:hover {
            color: var(--text-primary);
        }

        .filter-tab.active {
            color: var(--cyan);
            border-bottom-color: var(--cyan);
        }

        .filter-count {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            background: var(--sky-light);
            color: var(--cyan-deep);
            padding: 1px 6px;
            border-radius: 100px;
            margin-left: 6px;
        }

        /* PRODUCTS WRAP */
        .products-wrap {
            padding-bottom: 4rem;
        }

        /* ── PRODUCT HERO SECTIONS ── */
        .product-section {
            position: relative;
        }

        .product-hero-dark {
            padding: 5rem 6rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .product-hero-dark.wash {
            background: var(--navy);
        }

        .product-hero-dark.church {
            background: #0f2d1f;
        }

        .product-hero-glow-wash {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 80% 50%, rgba(0, 184, 219, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .product-hero-glow-church {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 80% 40%, rgba(76, 175, 130, 0.12) 0%, transparent 60%);
            pointer-events: none;
        }

        .product-hero-grid-wash {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(0, 184, 219, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 184, 219, 0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .product-hero-grid-church {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(76, 175, 130, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(76, 175, 130, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .product-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.9rem;
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
            text-transform: uppercase;
            width: fit-content;
        }

        .badge-wash {
            background: rgba(0, 184, 219, 0.15);
            border: 1px solid rgba(0, 184, 219, 0.25);
            color: var(--cyan);
        }

        .badge-church {
            background: rgba(76, 175, 130, 0.15);
            border: 1px solid rgba(76, 175, 130, 0.25);
            color: #4caf82;
        }

        .badge-custom {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.7);
        }

        .product-hero-dark h2 {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 3vw, 2.8rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .product-hero-dark h2 .accent-wash {
            color: var(--cyan);
        }

        .product-hero-dark h2 .accent-church {
            color: #4caf82;
        }

        .product-hero-dark p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.8;
            font-weight: 300;
            margin-bottom: 2rem;
        }

        .btn-row {
            display: flex;
            gap: 0.85rem;
            flex-wrap: wrap;
        }

        .btn-cyan {
            background: var(--cyan);
            color: var(--white);
            padding: 0.75rem 1.6rem;
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            display: inline-block;
        }

        .btn-cyan:hover {
            background: var(--cyan-dark);
            transform: translateY(-1px);
        }

        .btn-outline-wash {
            background: transparent;
            color: var(--cyan);
            padding: 0.75rem 1.6rem;
            border-radius: 8px;
            border: 1px solid rgba(0, 184, 219, 0.35);
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
            display: inline-block;
        }

        .btn-outline-wash:hover {
            border-color: var(--cyan);
            background: rgba(0, 184, 219, 0.08);
        }

        .btn-green {
            background: #1a6b4a;
            color: var(--white);
            padding: 0.75rem 1.6rem;
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            display: inline-block;
        }

        .btn-green:hover {
            background: #155a3e;
            transform: translateY(-1px);
        }

        .btn-outline-green {
            background: transparent;
            color: #4caf82;
            padding: 0.75rem 1.6rem;
            border-radius: 8px;
            border: 1px solid rgba(76, 175, 130, 0.35);
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
            display: inline-block;
        }

        .btn-outline-green:hover {
            border-color: #4caf82;
            background: rgba(76, 175, 130, 0.08);
        }

        /* Mock UI card */
        .product-ui-card {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            position: relative;
            z-index: 1;
        }

        .product-ui-card.church-card {
            border-color: rgba(76, 175, 130, 0.15);
        }

        .ui-titlebar {
            padding: 0.7rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .ui-titlebar.church {
            background: rgba(76, 175, 130, 0.08);
            border-color: rgba(76, 175, 130, 0.12);
        }

        .ui-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .ui-dot.r {
            background: #ff5f57;
        }

        .ui-dot.y {
            background: #ffbd2e;
        }

        .ui-dot.g {
            background: #28ca41;
        }

        .ui-titlebar-label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.3);
            margin-left: auto;
            font-family: var(--font-display);
        }

        .ui-offline-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(76, 175, 130, 0.2);
            border: 1px solid rgba(76, 175, 130, 0.3);
            color: #4caf82;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .pulse-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #4caf82;
            animation: blink 1.2s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        .ui-body {
            padding: 1.25rem;
        }

        .ui-stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }

        .ui-stat {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .church-stat {
            background: rgba(76, 175, 130, 0.08);
            border-color: rgba(76, 175, 130, 0.12);
        }

        .ui-stat-num {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 800;
        }

        .ui-stat-label {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.35);
            margin-top: 2px;
        }

        .ui-bar-row {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .ui-bar-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .ui-bar-label {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            width: 60px;
            flex-shrink: 0;
        }

        .ui-bar-track {
            flex: 1;
            height: 5px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
            overflow: hidden;
        }

        .ui-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .ui-bar-val {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            width: 36px;
            text-align: right;
        }

        .ui-kanban {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .kanban-col {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 6px;
            padding: 0.5rem;
        }

        .kanban-col-title {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            opacity: 0.5;
        }

        .kanban-card {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 4px;
            padding: 0.4rem 0.5rem;
            margin-bottom: 0.35rem;
            font-size: 0.6rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .k-tag {
            display: inline-block;
            font-size: 0.55rem;
            padding: 1px 5px;
            border-radius: 3px;
            margin-top: 3px;
        }

        .church-activity {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .church-activity-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.04);
            font-size: 0.62rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .activity-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
        }

        .church-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        /* Features section */
        .features-section {
            padding: 4rem 6rem;
        }

        .features-section.bg-ice {
            background: var(--ice);
        }

        .features-section.bg-church {
            background: #e8f5ee;
        }

        .features-header {
            margin-bottom: 3rem;
        }

        .features-header h3 {
            font-family: var(--font-display);
            font-size: clamp(1.3rem, 2vw, 1.8rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--navy);
            margin-bottom: 0.5rem;
        }

        .features-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .feature-card:hover {
            border-color: var(--cyan);
            box-shadow: 0 6px 24px rgba(0, 184, 219, 0.08);
        }

        .feature-card.church-feat:hover {
            border-color: #4caf82;
            box-shadow: 0 6px 24px rgba(26, 107, 74, 0.08);
        }

        .feature-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 18px;
            background: var(--sky-light);
        }

        .feature-icon-wrap.green {
            background: #e8f5ee;
        }

        .feature-card h4 {
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.6rem;
        }

        .feature-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-card ul li {
            font-size: 0.82rem;
            color: var(--text-secondary);
            padding: 0.2rem 0;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .feature-card ul li::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 0.45rem;
            background: var(--cyan);
        }

        .feature-card.church-feat ul li::before {
            background: #4caf82;
        }

        /* ROI callout */
        .roi-callout {
            background: var(--navy);
            padding: 3rem 6rem;
            display: flex;
            align-items: center;
            gap: 4rem;
            border-top: 1px solid rgba(76, 175, 130, 0.1);
            flex-wrap: wrap;
        }

        .roi-num {
            font-family: var(--font-display);
            font-size: 3.5rem;
            font-weight: 800;
            color: #4caf82;
            letter-spacing: -0.04em;
            line-height: 1;
            flex-shrink: 0;
        }

        .roi-text h4 {
            font-family: var(--font-display);
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.4rem;
            font-size: 1rem;
        }

        .roi-text p {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 300;
        }

        /* Divider */
        .product-divider {
            height: 4px;
            background: linear-gradient(90deg, var(--cyan) 0%, var(--cyan) 50%, #1a6b4a 50%, #1a6b4a 100%);
        }

        /* All products grid (search/filter mode) */
        .all-products-section {
            padding: 4rem 6rem;
        }

        .search-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .search-input-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 360px;
        }

        .search-input-wrap svg {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .search-input-wrap input {
            width: 100%;
            padding: 0.65rem 0.85rem 0.65rem 2.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            font-family: var(--font-body);
            background: var(--white);
            color: var(--text-primary);
            transition: border-color 0.2s;
        }

        .search-input-wrap input:focus {
            outline: none;
            border-color: var(--cyan);
        }

        .search-select {
            padding: 0.65rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            font-family: var(--font-body);
            background: var(--white);
            color: var(--text-secondary);
        }

        .search-select:focus {
            outline: none;
            border-color: var(--cyan);
        }

        .all-products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .prod-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            display: block;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .prod-card:hover {
            border-color: var(--cyan);
            box-shadow: 0 8px 24px rgba(0, 184, 219, 0.1);
        }

        .prod-card-img {
            height: 150px;
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .prod-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .prod-card-placeholder {
            font-family: var(--font-display);
            font-size: 2.5rem;
            font-weight: 800;
            color: rgba(0, 184, 219, 0.3);
        }

        .prod-card-body {
            padding: 1.1rem 1.25rem;
        }

        .prod-card-cat {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-muted);
        }

        .prod-card-name {
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0.3rem 0 0.4rem;
        }

        .prod-card-tagline {
            font-size: 0.8rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .prod-card-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .prod-price {
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--navy);
        }

        .prod-price.sale {
            color: #16a34a;
        }

        /* CTA strip */
        .products-cta-strip {
            background: var(--navy);
            padding: 4rem 6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            flex-wrap: wrap;
            border-top: 1px solid rgba(0, 184, 219, 0.1);
        }

        .products-cta-strip h2 {
            font-family: var(--font-display);
            font-weight: 800;
            color: var(--white);
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .products-cta-strip p {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.9rem;
            max-width: 420px;
            font-weight: 300;
        }

        @media (max-width: 1024px) {
            .products-hero {
                padding: 7rem 2rem 4rem;
            }

            #products-radar-canvas {
                display: none;
            }

            .products-filter-bar {
                padding: 0 2rem;
            }

            .product-hero-dark {
                grid-template-columns: 1fr;
                padding: 3.5rem 2rem;
                gap: 2.5rem;
            }

            .features-section {
                padding: 3rem 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .roi-callout {
                padding: 2.5rem 2rem;
                gap: 1.5rem;
            }

            .all-products-section {
                padding: 3rem 2rem;
            }

            .all-products-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .products-cta-strip {
                padding: 3rem 2rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .products-catalog {
                padding: 4rem 2rem;
            }

            .pcard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .all-products-grid {
                grid-template-columns: 1fr;
            }

            .roi-callout {
                flex-direction: column;
            }

            .pcard {
                min-height: 420px;
            }

            .pcard-title {
                font-size: 1.7rem;
            }
        }
    </style>

    @php
        // Helper: render markdown headings/paragraphs simply
        function renderProductHero(string $md): string
        {
            $lines = explode("\n", $md);
            $html = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if (str_starts_with($line, '## ')) {
                    $text = substr($line, 3);
                    $text = preg_replace('/\*\*(.+?)\*\*/', '<span class="dyn-accent">$1</span>', e($text));
                    $html .= "<h2>{$text}</h2>";
                } elseif ($line) {
                    $html .= '<p>' . e($line) . '</p>';
                }
            }
            return $html;
        }

        $washHero = $washSections->get('hero');
        $washFeatures = $washSections->get('features');
        $washRoi = $washSections->get('roi');
        $churchHero = $churchSections->get('hero');
        $churchFeatures = $churchSections->get('features');
        $churchRoi = $churchSections->get('roi');

        $defaultWashFeatures = [
            [
                'icon' => '📊',
                'title' => 'Analytics Dashboard',
                'items' => [
                    'Revenue tracking and forecasting',
                    'Order volume analytics',
                    'Daily bottleneck identification',
                    'Staff performance metrics',
                    'Customer behavior insights',
                    'Custom date range reporting',
                ],
            ],
            [
                'icon' => '🖥️',
                'title' => 'Advanced Point of Sale',
                'items' => [
                    'Quick order booking interface',
                    'Multiple payment methods support',
                    'Partial and full payment processing',
                    'Customer creation and retrieval',
                    'Thermal printer integration',
                    'Receipt customization',
                ],
            ],
            [
                'icon' => '📋',
                'title' => 'Kanban Orders Board',
                'items' => [
                    'Drag-and-drop status updates',
                    'Color-coded priority system',
                    'Real-time order tracking',
                    'Team collaboration features',
                    'Automated status notifications',
                    'Custom workflow stages',
                ],
            ],
            [
                'icon' => '☁️',
                'title' => 'Enterprise Database Management',
                'items' => [
                    'Automatic cloud backup',
                    'Manual backup and restore',
                    'Push to cloud / Pull from cloud',
                    'Conflict resolution strategies',
                    'Disaster recovery protocols',
                    'Data encryption and security',
                ],
            ],
        ];
        $defaultChurchFeatures = [
            [
                'icon' => '📡',
                'title' => '100% Offline Operation',
                'items' => [
                    'Full functionality during power cuts',
                    'Automatic cloud sync when online',
                    'Zero data loss guarantee',
                    'Local network (LAN) collaboration',
                ],
            ],
            [
                'icon' => '💰',
                'title' => 'Complete Financial Management',
                'items' => [
                    'MTN, Vodafone, AirtelTigo Mobile Money',
                    'Tithe and offering tracking',
                    'Member contribution statements',
                    'Full financial reporting and audit trail',
                ],
            ],
            [
                'icon' => '👥',
                'title' => 'Member Management',
                'items' => [
                    'Complete member profiles',
                    'Attendance tracking and trends',
                    'Visitor follow-up workflows',
                    'Ministry assignments and family links',
                ],
            ],
            [
                'icon' => '📣',
                'title' => 'Automated Communication',
                'items' => [
                    'Bulk SMS via local gateways',
                    'Automated birthday greetings',
                    'Service reminders and event notifications',
                    'Email campaigns to members',
                ],
            ],
            [
                'icon' => '🏛️',
                'title' => 'Multi-Branch Ready',
                'items' => [
                    'Manage all locations from HQ',
                    'Consolidated cross-branch reporting',
                    'Branch autonomy with HQ visibility',
                    'Easy member transfers between branches',
                ],
            ],
            [
                'icon' => '📈',
                'title' => 'Powerful Analytics',
                'items' => [
                    'Financial dashboards and trends',
                    'Attendance pattern analysis',
                    'Member engagement scoring',
                    'Custom report builder',
                ],
            ],
        ];
    @endphp

    <style>
        .dyn-accent {
            color: var(--cyan);
        }

        .church .dyn-accent {
            color: #4caf82;
        }
    </style>

    {{-- ── HERO ── --}}
    <section class="products-hero">
        <div class="products-hero-bg"></div>
        <div class="products-hero-grid"></div>
        <canvas id="products-particle-canvas"></canvas>
        <canvas id="products-radar-canvas"></canvas>
        <div class="products-hero-content">
            <div class="products-hero-tag"><span></span> Industry Solutions</div>
            <h1>Software Built for <br /><em>Real Conditions</em></h1>
            <p class="products-hero-sub">Sector-specific platforms designed with an offline-first philosophy, engineered
                for the infrastructure and business realities of emerging markets.</p>
        </div>
    </section>

    {{-- ── CATALOG CARDS ── --}}
    <section class="products-catalog">
        <div class="catalog-header">
            <span class="cat-label">Our Catalog</span>
            <h2 class="cat-h2">Platforms That Perform</h2>
            <p class="cat-sub">Deeply researched systems built from the ground up to solve the unique operational
                challenges of your industry.</p>
        </div>
        <div class="pcard-grid">
            {{-- WashOps --}}
            <a class="pcard" href="#washops"
                onclick="document.getElementById('washops').scrollIntoView({behavior:'smooth'}); return false;">
                <div class="pcard-img-wrap">
                    <div class="pcard-img"
                        style="background-image: url('https://lh3.googleusercontent.com/aida/ADBb0ugtz39yv0t16Pl6Vya-RxfPdyUjRqkm53GqVVxKtcF3risQzAlZ7ErADLIGl1zyDVwjL1HyjhCBHYGk7u49RDIW5nmSD0eDqMUeuaht5ZbTDrF8SGVoA_FDNraLCFr5A7Rr1sU0U4WPC7yrhMRe71lIEthpa410OkVv2EAvY_7eER0WSNV-8Ei1FxfWFqZdgvfuEwDUe-RYCWYOLhUEfZpArVG0GoJMQjisY4nGdWmBbnIlUp6wfRL2bg');">
                    </div>
                </div>
                <div class="pcard-overlay"></div>
                <div class="pcard-content">
                    <div class="pcard-top">
                        <div class="pcard-sector">🫧 Laundry</div>
                    </div>
                    <div class="pcard-body">
                        <h3 class="pcard-title">WashOps</h3>
                        <p class="pcard-text">Enterprise-grade desktop application with POS, real-time analytics, Kanban
                            order board, and cloud sync — everything to run and scale a laundry business.</p>
                        <span class="pcard-btn pcard-btn-cyan">Explore WashOps →</span>
                    </div>
                </div>
            </a>
            {{-- ChurchOps --}}
            <a class="pcard" href="#churchops"
                onclick="document.getElementById('churchops').scrollIntoView({behavior:'smooth'}); return false;">
                <div class="pcard-img-wrap">
                    <div class="pcard-img"
                        style="background-image: url('https://lh3.googleusercontent.com/aida/ADBb0ujWVD-GHTi2ed6MYa1LIML9wljjzhfs5eam1YUNmftFYazmsBX7bXDlEKa04mcQZnHroewUMqJYH4rQchmQWJ3hTcT0WyvhIhXrcEAmvbOzjvQvZ6Bbm8-lN_1M3tYHbpLsC6MR8vweDJznQfhdcLbHMWNKERPsobPiVMPj5eJwDlC-KCGvUzVdoE1VHMh_r-PPIkK9bRUMdQW9hgP5x0GQnXpypkA2N5yWsZSH-tsBMLGmvm0TEtd7kHc');">
                    </div>
                </div>
                <div class="pcard-overlay"></div>
                <div class="pcard-content">
                    <div class="pcard-top">
                        <div class="pcard-sector">⛪ Faith &amp; Community</div>
                    </div>
                    <div class="pcard-body">
                        <h3 class="pcard-title">ChurchOps</h3>
                        <p class="pcard-text">The first offline-first church management system built for Ghanaian
                            churches — members, finances, Mobile Money, multi-branch, and 100% uptime.</p>
                        <span class="pcard-btn pcard-btn-green">Explore ChurchOps →</span>
                    </div>
                </div>
            </a>
            {{-- ClinicOps --}}
            <div class="pcard coming">
                <div class="pcard-img-wrap">
                    <div class="pcard-img"
                        style="background-image: url('https://lh3.googleusercontent.com/aida/ADBb0ujOJI2svVFC8xxpkwg9aBk-puH0KPPlCth9XghjhCzQpi9SFL1-xtltLxyjF9F2g6FEBK4eSBpwe4P4EUDwlGDD7UmeqJY2LEyrVgUA820IrxIfqRD89qk2j_A66KIYgwnJCkjjhan9nBGRqGj5igzxT3lOJVzExBnmwjbf0hTW1MCjU7Qzu12Xw645TeCpNhjLjcriZCjMxnnbCOvjRRzVtqP-7XBjsUTm17id1n-eh96XKW4-14HglOw');">
                    </div>
                </div>
                <div class="pcard-overlay"></div>
                <div class="pcard-content">
                    <div class="pcard-top">
                        <div class="pcard-sector">🏥 Healthcare</div>
                        <span class="pcard-coming-badge">Coming Soon</span>
                    </div>
                    <div class="pcard-body">
                        <h3 class="pcard-title">ClinicOps</h3>
                        <p class="pcard-text">Hospital and pharmacy management — patient records, prescriptions,
                            inventory, and billing. Offline-first, built for clinical environments.</p>
                        <span class="pcard-btn pcard-btn-disabled">In Development</span>
                    </div>
                </div>
            </div>
            {{-- LabOps --}}
            <div class="pcard coming">
                <div class="pcard-img-wrap">
                    <div class="pcard-img"
                        style="background-image: url('https://lh3.googleusercontent.com/aida/ADBb0ug8jVmvIaOPGwcXVKC2W11VjxCK88veulcLTIBM14Wd2Qhtc_cQn8hjIS-kE476dXseUR-MKFTbYKxnhAZCQUuDexOLno2G-g6iep4kEZi3eOUF2U-Bk-qZQaO3x_Hj2K0Gxf0YysUjcNMcyQurxyRv-pVg5n2bB2GDVfwL8B2Q2bso0yHzyTPedHECoTHRLVQgUjCbDgny98116lIRhKRYaoE33L0h41V1azekYhD66J_6wBBL05dUDCw');">
                    </div>
                </div>
                <div class="pcard-overlay"></div>
                <div class="pcard-content">
                    <div class="pcard-top">
                        <div class="pcard-sector">🔬 Laboratory</div>
                        <span class="pcard-coming-badge">Coming Soon</span>
                    </div>
                    <div class="pcard-body">
                        <h3 class="pcard-title">LabOps</h3>
                        <p class="pcard-text">Laboratory information management — sample tracking, test workflows,
                            results reporting, compliance, and full audit trails.</p>
                        <span class="pcard-btn pcard-btn-disabled">In Development</span>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- CTA STRIP -->
    <div class="products-cta-strip" id="cta">
        <div>
            <h2>Need something built for your industry?</h2>
            <p>WashOps and ChurchOps are two of our products. If your sector isn't covered, we build custom — from the
                ground up, for your exact operations.</p>
        </div>
        <a class="btn-cyan" href="{{ route('site.book-demo') }}" wire:navigate
            style="padding:0.9rem 2.2rem;font-size:0.95rem;">Start a Conversation</a>
    </div>



    <script>
        (function() {
            // Particles
            const pc = document.getElementById('products-particle-canvas');
            if (!pc) return;
            const pctx = pc.getContext('2d');
            let pars = [];

            function initP() {
                pc.width = window.innerWidth;
                pc.height = pc.parentElement.offsetHeight;
                pars = [];
                const cnt = Math.floor(window.innerWidth / 16);
                for (let i = 0; i < cnt; i++) {
                    pars.push({
                        x: Math.random() * pc.width,
                        y: Math.random() * pc.height,
                        vx: (Math.random() - 0.5) * 0.3,
                        vy: (Math.random() - 0.5) * 0.3,
                        s: Math.random() * 1.4 + 0.8
                    });
                }
            }

            function animP() {
                pctx.clearRect(0, 0, pc.width, pc.height);
                pars.forEach((p, i) => {
                    p.x += p.vx;
                    p.y += p.vy;
                    if (p.x < 0 || p.x > pc.width) p.vx *= -1;
                    if (p.y < 0 || p.y > pc.height) p.vy *= -1;
                    pctx.fillStyle = 'rgba(0,184,219,0.4)';
                    pctx.beginPath();
                    pctx.arc(p.x, p.y, p.s, 0, Math.PI * 2);
                    pctx.fill();
                    for (let j = i + 1; j < pars.length; j++) {
                        const q = pars[j];
                        const dx = p.x - q.x,
                            dy = p.y - q.y,
                            d = Math.sqrt(dx * dx + dy * dy);
                        if (d < 110) {
                            pctx.strokeStyle = `rgba(0,184,219,${0.14*(1-d/110)})`;
                            pctx.lineWidth = 0.5;
                            pctx.beginPath();
                            pctx.moveTo(p.x, p.y);
                            pctx.lineTo(q.x, q.y);
                            pctx.stroke();
                        }
                    }
                });
                requestAnimationFrame(animP);
            }
            window.addEventListener('resize', initP);
            initP();
            animP();

            // Radar
            const rc = document.getElementById('products-radar-canvas');
            if (!rc) return;
            const rctx = rc.getContext('2d');
            const RS = 480;
            rc.width = RS;
            rc.height = RS;
            const CX = RS / 2,
                CY = RS / 2;
            let ang = 0;
            const pings = [];
            const prodAngles = [45, 135, 225, 315];

            function drawR() {
                rctx.clearRect(0, 0, RS, RS);
                rctx.strokeStyle = 'rgba(0,184,219,0.08)';
                rctx.lineWidth = 1;
                for (let r = 50; r <= 220; r += 45) {
                    rctx.beginPath();
                    rctx.arc(CX, CY, r, 0, Math.PI * 2);
                    rctx.stroke();
                }
                ang += 0.015;
                const norm = (ang % (Math.PI * 2));
                const g = rctx.createConicGradient(ang, CX, CY);
                g.addColorStop(0, 'rgba(0,184,219,0.35)');
                g.addColorStop(0.15, 'rgba(0,184,219,0)');
                rctx.fillStyle = g;
                rctx.beginPath();
                rctx.moveTo(CX, CY);
                rctx.arc(CX, CY, 230, ang, ang - 1);
                rctx.fill();
                rctx.beginPath();
                rctx.moveTo(CX, CY);
                rctx.lineTo(CX + Math.cos(ang) * 230, CY + Math.sin(ang) * 230);
                rctx.strokeStyle = 'rgba(0,230,255,0.8)';
                rctx.lineWidth = 2;
                rctx.stroke();
                prodAngles.forEach(deg => {
                    const rad = deg * (Math.PI / 180);
                    const diff = Math.abs(norm - rad);
                    if (diff < 0.02) pings.push({
                        x: CX + Math.cos(rad) * 155,
                        y: CY + Math.sin(rad) * 155,
                        r: 0,
                        a: 0.8
                    });
                });
                for (let i = pings.length - 1; i >= 0; i--) {
                    const p = pings[i];
                    p.r += 1.5;
                    p.a -= 0.015;
                    if (p.a <= 0) {
                        pings.splice(i, 1);
                        continue;
                    }
                    rctx.strokeStyle = `rgba(0,230,255,${p.a})`;
                    rctx.lineWidth = 1.2;
                    for (let j = 0; j < 2; j++) {
                        rctx.beginPath();
                        rctx.arc(p.x, p.y, p.r + (j * 10), 0, Math.PI * 2);
                        rctx.stroke();
                    }
                    rctx.fillStyle = `rgba(0,230,255,${p.a})`;
                    rctx.beginPath();
                    rctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
                    rctx.fill();
                }
                requestAnimationFrame(drawR);
            }
            drawR();
        })();
    </script>



</div>
