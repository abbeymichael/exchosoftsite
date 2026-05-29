<?php

use App\Models\ProductPageSection;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Computed;

new #[Layout('layouts.admin')] #[Title('Product Page Editor — ExchoSoft')] class extends Component
{
    // ── State ─────────────────────────────────────────────────────────────────
    public ?int    $selectedProductId = null;
    public string  $activeSection     = 'hero';

    // ── Hero fields ───────────────────────────────────────────────────────────
    public string $hero_badge         = '';
    public string $hero_headline      = '';
    public string $hero_subheadline   = '';
    public string $hero_description   = '';
    public string $hero_btn_primary   = '';
    public string $hero_btn_secondary = '';
    public string $hero_theme         = 'wash';   // wash | church | dark

    // ── Features fields ───────────────────────────────────────────────────────
    public string $features_title    = '';
    public string $features_subtitle = '';
    public array  $features_cards    = [];         // [{icon, title, items:[]}]

    // ── ROI/Impact fields ─────────────────────────────────────────────────────
    public string $roi_number   = '';
    public string $roi_title    = '';
    public string $roi_subtitle = '';

    // ── Specs / Tech fields ───────────────────────────────────────────────────
    public array  $specs_items  = [];              // [{label, value}]
    public string $specs_title  = '';

    // ── Available themes ──────────────────────────────────────────────────────
    public array $themes = [
        'wash'   => ['label' => 'Cyan / Blue (WashOps)',    'color' => '#0891b2'],
        'church' => ['label' => 'Green (ChurchOps)',         'color' => '#1a6b4a'],
        'dark'   => ['label' => 'Dark / Navy (Custom)',      'color' => '#1e293b'],
        'purple' => ['label' => 'Purple (SaaS)',             'color' => '#7c3aed'],
        'amber'  => ['label' => 'Amber / Gold',              'color' => '#d97706'],
    ];

    // ── Local cache of products ────────────────────────────────────────────────
    protected array $cachedProducts = [];

    public function mount(): void
    {
        // Default to first published product if any
        $first = ShopProduct::published()->orderBy('sort_order')->first();
        if ($first) {
            $this->selectedProductId = $first->id;
            $this->loadSections();
        }
    }

    // ─── When user picks a different product ──────────────────────────────────
    public function updatedSelectedProductId(): void
    {
        $this->activeSection = 'hero';
        $this->resetFields();
        $this->loadSections();
    }

    public function updatedActiveSection(): void
    {
        // nothing extra needed; fields reload on section change through the blade
    }

    protected function resetFields(): void
    {
        $this->hero_badge = $this->hero_headline = $this->hero_subheadline =
        $this->hero_description = $this->hero_btn_primary = $this->hero_btn_secondary = '';
        $this->hero_theme = 'wash';
        $this->features_title = $this->features_subtitle = '';
        $this->features_cards = [];
        $this->roi_number = $this->roi_title = $this->roi_subtitle = '';
        $this->specs_title = '';
        $this->specs_items = [];
    }

    protected function productCode(): string
    {
        if (! $this->selectedProductId) return '';
        $p = ShopProduct::find($this->selectedProductId);
        return $p?->linked_product_code ?? 'product_' . $this->selectedProductId;
    }

    protected function loadSections(): void
    {
        $code = $this->productCode();
        if (! $code) return;

        $sections = ProductPageSection::getForProduct($code);

        // ── Hero ──
        $hero = $sections->get('hero');
        if ($hero) {
            $this->hero_badge         = $hero->data['badge']               ?? '';
            $this->hero_headline      = $hero->data['headline']            ?? ($hero->content ?? '');
            $this->hero_subheadline   = $hero->data['subheadline']         ?? '';
            $this->hero_description   = $hero->data['description']         ?? '';
            $this->hero_btn_primary   = $hero->data['btn_primary_label']   ?? '';
            $this->hero_btn_secondary = $hero->data['btn_secondary_label'] ?? '';
            $this->hero_theme         = $hero->data['theme']               ?? 'wash';
        }

        // ── Features ──
        $features = $sections->get('features');
        if ($features) {
            $this->features_title    = $features->content ?? '';
            $this->features_subtitle = $features->data['subtitle']  ?? '';
            $this->features_cards    = $features->data['features']  ?? [];
        }

        // ── ROI ──
        $roi = $sections->get('roi');
        if ($roi) {
            $this->roi_number   = $roi->data['number']   ?? '';
            $this->roi_title    = $roi->data['title']    ?? '';
            $this->roi_subtitle = $roi->data['subtitle'] ?? '';
        }

        // ── Specs ──
        $specs = $sections->get('specs');
        if ($specs) {
            $this->specs_title = $specs->content         ?? '';
            $this->specs_items = $specs->data['items']   ?? [];
        }
    }

    // ── Feature card management ────────────────────────────────────────────────
    public function addFeatureCard(): void
    {
        $this->features_cards[] = ['icon' => '📋', 'title' => '', 'items' => []];
    }
    public function removeFeatureCard(int $i): void
    {
        array_splice($this->features_cards, $i, 1);
        $this->features_cards = array_values($this->features_cards);
    }
    public function addFeatureItem(int $cardIndex): void
    {
        $this->features_cards[$cardIndex]['items'][] = '';
    }
    public function removeFeatureItem(int $cardIndex, int $itemIndex): void
    {
        array_splice($this->features_cards[$cardIndex]['items'], $itemIndex, 1);
        $this->features_cards[$cardIndex]['items'] = array_values($this->features_cards[$cardIndex]['items']);
    }

    // ── Spec item management ───────────────────────────────────────────────────
    public function addSpecItem(): void
    {
        $this->specs_items[] = ['label' => '', 'value' => ''];
    }
    public function removeSpecItem(int $i): void
    {
        array_splice($this->specs_items, $i, 1);
        $this->specs_items = array_values($this->specs_items);
    }

    // ── Save Hero ──────────────────────────────────────────────────────────────
    public function saveHero(): void
    {
        $code = $this->productCode();
        if (! $code) return;

        $themeColors = [
            'wash'   => ['badge_class' => 'badge-wash',   'accent_class' => 'accent-wash'],
            'church' => ['badge_class' => 'badge-church', 'accent_class' => 'accent-church'],
            'dark'   => ['badge_class' => 'badge-custom', 'accent_class' => 'accent-custom'],
            'purple' => ['badge_class' => 'badge-purple', 'accent_class' => 'accent-purple'],
            'amber'  => ['badge_class' => 'badge-amber',  'accent_class' => 'accent-amber'],
        ];
        $themeData = $themeColors[$this->hero_theme] ?? $themeColors['wash'];

        ProductPageSection::upsertSection($code, 'hero', [
            'product_code' => $code,
            'section_key'  => 'hero',
            'label'        => 'Hero Section',
            'content'      => $this->hero_headline,
            'type'         => 'structured',
            'is_active'    => true,
            'sort_order'   => 1,
            'data'         => [
                'badge'               => $this->hero_badge,
                'headline'            => $this->hero_headline,
                'subheadline'         => $this->hero_subheadline,
                'description'         => $this->hero_description,
                'btn_primary_label'   => $this->hero_btn_primary,
                'btn_secondary_label' => $this->hero_btn_secondary,
                'theme'               => $this->hero_theme,
                'badge_class'         => $themeData['badge_class'],
                'accent_class'        => $themeData['accent_class'],
            ],
        ]);

        session()->flash('success', 'Hero section saved!');
    }

    // ── Save Features ──────────────────────────────────────────────────────────
    public function saveFeatures(): void
    {
        $code = $this->productCode();
        if (! $code) return;

        // Clean up empty items within each card
        $cards = array_map(function ($card) {
            $card['items'] = array_values(array_filter($card['items'] ?? [], fn($v) => trim($v) !== ''));
            return $card;
        }, $this->features_cards);

        ProductPageSection::upsertSection($code, 'features', [
            'product_code' => $code,
            'section_key'  => 'features',
            'label'        => 'Features Section',
            'content'      => $this->features_title,
            'type'         => 'structured',
            'is_active'    => true,
            'sort_order'   => 2,
            'data'         => [
                'subtitle' => $this->features_subtitle,
                'features' => array_values($cards),
            ],
        ]);

        session()->flash('success', 'Features section saved!');
    }

    // ── Save ROI ───────────────────────────────────────────────────────────────
    public function saveRoi(): void
    {
        $code = $this->productCode();
        if (! $code) return;

        ProductPageSection::upsertSection($code, 'roi', [
            'product_code' => $code,
            'section_key'  => 'roi',
            'label'        => 'ROI / Impact Callout',
            'content'      => null,
            'type'         => 'structured',
            'is_active'    => true,
            'sort_order'   => 3,
            'data'         => [
                'number'   => $this->roi_number,
                'title'    => $this->roi_title,
                'subtitle' => $this->roi_subtitle,
            ],
        ]);

        session()->flash('success', 'ROI section saved!');
    }

    // ── Save Specs ─────────────────────────────────────────────────────────────
    public function saveSpecs(): void
    {
        $code = $this->productCode();
        if (! $code) return;

        $items = array_values(array_filter($this->specs_items, fn($s) => trim($s['label'] ?? '') !== ''));

        ProductPageSection::upsertSection($code, 'specs', [
            'product_code' => $code,
            'section_key'  => 'specs',
            'label'        => 'Technical Specs',
            'content'      => $this->specs_title,
            'type'         => 'structured',
            'is_active'    => true,
            'sort_order'   => 4,
            'data'         => ['items' => $items],
        ]);

        session()->flash('success', 'Specs saved!');
    }

    #[Computed]
    public function products(): \Illuminate\Database\Eloquent\Collection
    {
        return ShopProduct::published()->orderBy('sort_order')->get();
    }

    #[Computed]
    public function selectedProduct(): ?ShopProduct
    {
        return $this->selectedProductId ? ShopProduct::find($this->selectedProductId) : null;
    }

}; ?>

<div>
    <x-slot:heading>Product Page Editor</x-slot:heading>

    <style>
        /* ── Layout & Base ── */
        .ppe-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .ppe-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; margin-bottom: 0.3rem; display: block; }
        .ppe-hint { font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem; }
        .ppe-section-head { font-size: 0.8rem; font-weight: 700; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1.25rem; }

        /* ── Form controls ── */
        .ppe-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: #1e293b; transition: border-color 0.15s; background: white; }
        .ppe-input:focus { outline: none; border-color: #0891b2; box-shadow: 0 0 0 3px rgba(8,145,178,0.08); }
        .ppe-textarea { resize: vertical; min-height: 64px; }

        /* ── Product selector pills ── */
        .prod-pill { padding: 0.5rem 1.1rem; border-radius: 99px; font-size: 0.82rem; font-weight: 600; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; white-space: nowrap; }
        .prod-pill.active { color: white; border-color: transparent; }
        .prod-pill:not(.active) { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .prod-pill:not(.active):hover { background: #e2e8f0; color: #334155; }

        /* ── Section tabs ── */
        .sec-tab { padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; }
        .sec-tab.active { background: #e0f2fe; color: #0891b2; }
        .sec-tab:not(.active) { background: transparent; color: #64748b; }
        .sec-tab:not(.active):hover { background: #f8fafc; color: #334155; }

        /* ── Row items ── */
        .ppe-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; }
        .ppe-row:hover { border-color: #cbd5e1; }
        .ppe-sub-row { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; }

        /* ── Buttons ── */
        .ppe-btn-add { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; border: 1.5px dashed #cbd5e1; color: #64748b; background: transparent; cursor: pointer; transition: all 0.15s; }
        .ppe-btn-add:hover { border-color: #0891b2; color: #0891b2; background: #f0f9ff; }
        .ppe-btn-add.sm { padding: 0.3rem 0.65rem; font-size: 0.72rem; }
        .ppe-btn-rm { width: 22px; height: 22px; border-radius: 6px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .ppe-btn-rm:hover { background: #fca5a5; }
        .ppe-btn-save { display: inline-flex; align-items: center; gap: 1.5rem; padding: 0.55rem 1.25rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; border: none; color: white; cursor: pointer; transition: all 0.15s; }

        /* ── Theme swatches ── */
        .theme-swatch { width: 18px; height: 18px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 1.5px #cbd5e1; display: inline-block; flex-shrink: 0; }
        .theme-chip { display: flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.75rem; border-radius: 8px; border: 1.5px solid #e2e8f0; cursor: pointer; font-size: 0.78rem; font-weight: 600; transition: all 0.12s; color: #64748b; }
        .theme-chip.selected { border-color: #0891b2; background: #e0f2fe; color: #0369a1; }
        .theme-chip:not(.selected):hover { border-color: #cbd5e1; background: #f8fafc; }

        /* ── No-product state ── */
        .ppe-empty { text-align: center; padding: 4rem 2rem; color: #94a3b8; }
        .ppe-empty h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem; }

        /* ── Preview card ── */
        .preview-hero { border-radius: 12px; overflow: hidden; border: 1px solid #1e293b; }
    </style>

    <div class="space-y-5">

        @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- ══ Top bar ══ --}}
        <div class="flex items-center justify-between">
            <p class="text-slate-500 text-sm">Edit the public-facing content for each product's page section.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('site.products') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Preview Products Page
                </a>
                <a href="{{ route('admin.shop-products') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Manage Shop Products
                </a>
            </div>
        </div>

        {{-- ══ Product selector ══ --}}
        <div class="ppe-card">
            <div class="ppe-section-head">Select a product to edit its page content</div>
            @if($this->products->isEmpty())
            <div class="ppe-empty">
                <h3>No published products yet</h3>
                <p class="text-sm mb-4">Publish a product from the Shop Products manager to start editing its page.</p>
                <a href="{{ route('admin.shop-products') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                    Go to Shop Products
                </a>
            </div>
            @else
            <div class="flex flex-wrap gap-2">
                @foreach($this->products as $product)
                @php
                    $isActive = $this->selectedProductId === $product->id;
                    $themeColor = match($product->linked_product_code) {
                        'washops'   => '#0891b2',
                        'churchops' => '#1a6b4a',
                        default     => '#475569',
                    };
                @endphp
                <button wire:click="$set('selectedProductId', {{ $product->id }})"
                        class="prod-pill {{ $isActive ? 'active' : '' }}"
                        style="{{ $isActive ? 'background-color:' . $themeColor . ';' : '' }}">
                    {{ $product->name }}
                    @if($product->is_featured)
                        <span class="ml-1 text-{{ $isActive ? 'white/70' : 'amber-500' }} text-xs">★</span>
                    @endif
                </button>
                @endforeach
            </div>

            @if($this->selectedProduct)
            <div class="mt-4 flex items-center gap-4 text-xs text-slate-500 border-t border-slate-100 pt-3">
                <span class="font-semibold text-slate-700">{{ $this->selectedProduct->name }}</span>
                @if($this->selectedProduct->tagline)<span class="text-slate-400">{{ $this->selectedProduct->tagline }}</span>@endif
                @if($this->selectedProduct->linked_product_code)
                    <span class="bg-slate-100 text-slate-500 rounded-full px-2 py-0.5 font-mono">{{ $this->selectedProduct->linked_product_code }}</span>
                @endif
                <span class="ml-auto">
                    @if($this->selectedProduct->is_published)<span class="text-green-600 font-semibold">● Published</span>@else<span class="text-slate-400">● Draft</span>@endif
                </span>
            </div>
            @endif
            @endif
        </div>

        @if($this->selectedProduct)
        {{-- ══ Section tabs ══ --}}
        <div class="flex gap-1 bg-slate-50 p-1.5 rounded-xl border border-slate-100 w-fit">
            @foreach([
                ['hero',     '🎯 Hero'],
                ['features', '⚡ Features'],
                ['roi',      '📈 ROI / Impact'],
                ['specs',    '🔩 Tech Specs'],
            ] as [$s, $l])
            <button wire:click="$set('activeSection', '{{ $s }}')"
                    class="sec-tab {{ $activeSection === $s ? 'active' : '' }}">
                {{ $l }}
            </button>
            @endforeach
        </div>

        {{-- ══════════════════ HERO ══════════════════ --}}
        @if($activeSection === 'hero')
        <div class="ppe-card space-y-5">
            <div class="flex items-center justify-between">
                <div class="ppe-section-head mb-0">🎯 Hero Section — {{ $this->selectedProduct->name }}</div>
                <button wire:click="saveHero" class="ppe-btn-save"
                        style="background: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Hero
                </button>
            </div>

            {{-- Theme picker --}}
            <div>
                <label class="ppe-label">Colour Theme</label>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($themes as $themeKey => $themeData)
                    <button type="button" wire:click="$set('hero_theme', '{{ $themeKey }}')"
                            class="theme-chip {{ $hero_theme === $themeKey ? 'selected' : '' }}">
                        <span class="theme-swatch" style="background: {{ $themeData['color'] }}"></span>
                        {{ $themeData['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="ppe-label">Product Badge Text</label>
                    <input wire:model="hero_badge" type="text" class="ppe-input" placeholder="WashOps Pro">
                    <p class="ppe-hint">Small label shown above the headline (e.g. "WashOps" or "ChurchOps Enterprise")</p>
                </div>
                <div>
                    <label class="ppe-label">Main Headline</label>
                    <input wire:model="hero_headline" type="text" class="ppe-input" placeholder="Complete Laundry Management for **Modern Businesses**">
                    <p class="ppe-hint">Use **word** for a coloured accent word</p>
                </div>
                <div>
                    <label class="ppe-label">Sub-headline <span class="normal-case font-normal text-slate-300">(optional)</span></label>
                    <input wire:model="hero_subheadline" type="text" class="ppe-input" placeholder="Enterprise-grade · Offline-first · Built for Africa">
                </div>
                <div>
                    <label class="ppe-label">Primary Button Label</label>
                    <input wire:model="hero_btn_primary" type="text" class="ppe-input" placeholder="Start Free Trial">
                </div>
                <div class="col-span-2">
                    <label class="ppe-label">Description Paragraph</label>
                    <textarea wire:model="hero_description" rows="3" class="ppe-input ppe-textarea" placeholder="Enterprise-grade desktop application with powerful POS, real-time analytics..."></textarea>
                </div>
                <div>
                    <label class="ppe-label">Secondary Button Label</label>
                    <input wire:model="hero_btn_secondary" type="text" class="ppe-input" placeholder="Read White Paper">
                </div>
            </div>

            {{-- Live preview --}}
            @if($hero_headline || $hero_description)
            <div class="preview-hero">
                <div class="p-5 text-white" style="background: {{ $hero_theme === 'wash' ? '#0c1a2e' : ($hero_theme === 'church' ? '#0f2d1f' : ($hero_theme === 'purple' ? '#1e1038' : ($hero_theme === 'amber' ? '#1c1205' : '#1e293b'))) }}">
                    <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}90">Live Preview</p>
                    @if($hero_badge)
                        <div class="inline-block mb-2 text-xs font-bold uppercase tracking-wider rounded-full px-3 py-0.5 border mb-3"
                             style="background: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}20; border-color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}40; color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}">
                            {{ $hero_badge }}
                        </div>
                    @endif
                    <h2 class="text-lg font-bold leading-tight mb-1">
                        {!! preg_replace('/\*\*(.+?)\*\*/', '<span style="color:' . ($themes[$hero_theme]['color'] ?? '#0891b2') . '">$1</span>', e($hero_headline)) !!}
                    </h2>
                    @if($hero_subheadline)
                        <p class="text-xs mb-2" style="color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}aa">{{ $hero_subheadline }}</p>
                    @endif
                    @if($hero_description)
                        <p class="text-slate-400 text-sm leading-relaxed max-w-lg mb-3">{{ $hero_description }}</p>
                    @endif
                    <div class="flex gap-2">
                        @if($hero_btn_primary)
                            <span class="text-xs font-semibold rounded-lg px-3 py-1.5 text-white"
                                  style="background: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}">
                                {{ $hero_btn_primary }}
                            </span>
                        @endif
                        @if($hero_btn_secondary)
                            <span class="text-xs font-semibold rounded-lg px-3 py-1.5 border"
                                  style="border-color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}50; color: {{ $themes[$hero_theme]['color'] ?? '#0891b2' }}">
                                {{ $hero_btn_secondary }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════ FEATURES ══════════════════ --}}
        @if($activeSection === 'features')
        <div class="ppe-card space-y-5">
            <div class="flex items-center justify-between">
                <div class="ppe-section-head mb-0">⚡ Features Section</div>
                <button wire:click="saveFeatures" class="ppe-btn-save bg-cyan-600 hover:bg-cyan-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Features
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="ppe-label">Section Title</label>
                    <input wire:model="features_title" type="text" class="ppe-input" placeholder="Powerful Features Built for Laundry Businesses">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="ppe-label">Section Subtitle</label>
                    <textarea wire:model="features_subtitle" rows="2" class="ppe-input ppe-textarea" placeholder="Everything you need to manage operations, orders, and analytics from one place."></textarea>
                </div>
            </div>

            {{-- Feature cards --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="ppe-label mb-0">Feature Groups <span class="normal-case font-normal text-slate-400">(each group = one card on the page)</span></label>
                    <button wire:click="addFeatureCard" class="ppe-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Feature Group
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($features_cards as $ci => $card)
                    <div class="ppe-row">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-500 bg-slate-200 rounded-full px-2 py-0.5">Group {{ $ci + 1 }}</span>
                            <button wire:click="removeFeatureCard({{ $ci }})" class="ppe-btn-rm">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-5 gap-3 mb-3">
                            <div class="col-span-1">
                                <label class="ppe-label">Icon (emoji)</label>
                                <input wire:model="features_cards.{{ $ci }}.icon" type="text" class="ppe-input text-lg text-center" placeholder="📊" maxlength="4">
                            </div>
                            <div class="col-span-4">
                                <label class="ppe-label">Group Title</label>
                                <input wire:model="features_cards.{{ $ci }}.title" type="text" class="ppe-input" placeholder="Analytics Dashboard">
                            </div>
                        </div>

                        {{-- Feature bullet items --}}
                        <div>
                            <label class="ppe-label mb-2">Feature Bullet Points</label>
                            <div class="space-y-1.5">
                                @if(!empty($card['items']))
                                @foreach($card['items'] as $ii => $item)
                                <div class="ppe-sub-row">
                                    <span class="text-slate-300 flex-shrink-0">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                    <input wire:model="features_cards.{{ $ci }}.items.{{ $ii }}"
                                           type="text" class="flex-1 text-sm border-none outline-none bg-transparent text-slate-700"
                                           placeholder="Feature description...">
                                    <button wire:click="removeFeatureItem({{ $ci }}, {{ $ii }})" class="ppe-btn-rm flex-shrink-0">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                @endforeach
                                @endif
                                <button wire:click="addFeatureItem({{ $ci }})" class="ppe-btn-add sm mt-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add bullet point
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400">
                        <svg class="h-8 w-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <p class="text-sm">No feature groups yet. Click "Add Feature Group" to start.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Preview --}}
            @if(count($features_cards) > 0)
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Preview ({{ count($features_cards) }} groups)</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(array_slice($features_cards, 0, 4) as $card)
                    @if(!empty($card['title']))
                    <div class="bg-white border border-slate-200 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-base">{{ $card['icon'] ?? '📋' }}</span>
                            <span class="font-semibold text-slate-800 text-sm">{{ $card['title'] }}</span>
                        </div>
                        <ul class="text-xs text-slate-500 space-y-0.5">
                            @foreach(array_slice($card['items'] ?? [], 0, 3) as $item)
                            @if($item)<li class="flex items-start gap-1"><span class="text-cyan-400 mt-0.5">•</span>{{ $item }}</li>@endif
                            @endforeach
                            @if(count($card['items'] ?? []) > 3)<li class="text-slate-300">+{{ count($card['items']) - 3 }} more</li>@endif
                        </ul>
                    </div>
                    @endif
                    @endforeach
                </div>
                @if(count($features_cards) > 4)
                <p class="text-xs text-slate-400 mt-2 text-center">…and {{ count($features_cards) - 4 }} more groups</p>
                @endif
            </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════ ROI / IMPACT ══════════════════ --}}
        @if($activeSection === 'roi')
        <div class="ppe-card space-y-5">
            <div class="flex items-center justify-between">
                <div class="ppe-section-head mb-0">📈 ROI / Impact Callout</div>
                <button wire:click="saveRoi" class="ppe-btn-save bg-cyan-600 hover:bg-cyan-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save ROI
                </button>
            </div>
            <p class="text-sm text-slate-500 -mt-2">A dark banner showing a key business metric. Keep it punchy — one number, one message.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="ppe-label">Big Number / Metric</label>
                    <input wire:model="roi_number" type="text" class="ppe-input text-2xl font-black text-center" placeholder="40%">
                    <p class="ppe-hint">e.g. "40%", "2×", "₵12k", "3 days"</p>
                </div>
                <div>
                    <label class="ppe-label">Metric Title</label>
                    <input wire:model="roi_title" type="text" class="ppe-input" placeholder="Average Revenue Increase">
                </div>
                <div>
                    <label class="ppe-label">Description</label>
                    <textarea wire:model="roi_subtitle" rows="3" class="ppe-input ppe-textarea" placeholder="Businesses that implement WashOps typically see..."></textarea>
                </div>
            </div>

            {{-- Live preview --}}
            <div class="bg-slate-900 rounded-xl p-6 flex items-center gap-8 flex-wrap">
                <div class="text-5xl font-black text-cyan-400 leading-none min-w-[80px] text-center">
                    {{ $roi_number ?: '—' }}
                </div>
                <div>
                    <div class="font-bold text-white text-base mb-1">{{ $roi_title ?: 'Metric Title' }}</div>
                    <div class="text-slate-400 text-sm max-w-md leading-relaxed">{{ $roi_subtitle ?: 'Description of the impact your product delivers…' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════════════════ TECH SPECS ══════════════════ --}}
        @if($activeSection === 'specs')
        <div class="ppe-card space-y-5">
            <div class="flex items-center justify-between">
                <div class="ppe-section-head mb-0">🔩 Technical Specifications</div>
                <button wire:click="saveSpecs" class="ppe-btn-save bg-cyan-600 hover:bg-cyan-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Specs
                </button>
            </div>
            <p class="text-sm text-slate-500 -mt-2">Optional table of technical specs shown on the product detail page.</p>

            <div>
                <label class="ppe-label">Section Title</label>
                <input wire:model="specs_title" type="text" class="ppe-input" placeholder="Technical Specifications">
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="ppe-label mb-0">Spec Rows</label>
                    <button wire:click="addSpecItem" class="ppe-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Row
                    </button>
                </div>
                <div class="space-y-2">
                    @forelse($specs_items as $i => $spec)
                    <div class="ppe-sub-row">
                        <input wire:model="specs_items.{{ $i }}.label" type="text"
                               class="w-40 text-sm border-none outline-none bg-transparent text-slate-600 font-semibold"
                               placeholder="Platform">
                        <span class="text-slate-200">·</span>
                        <input wire:model="specs_items.{{ $i }}.value" type="text"
                               class="flex-1 text-sm border-none outline-none bg-transparent text-slate-700"
                               placeholder="Windows 10/11, macOS 12+">
                        <button wire:click="removeSpecItem({{ $i }})" class="ppe-btn-rm">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2">No spec rows yet. Add rows for things like Platform, Requirements, Languages, etc.</p>
                    @endforelse
                </div>
            </div>

            @if(count($specs_items) > 0)
            <div class="bg-slate-50 rounded-xl overflow-hidden border border-slate-200">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2 border-b border-slate-200">Preview</p>
                <table class="w-full text-sm">
                    @foreach($specs_items as $spec)
                    @if(!empty($spec['label']))
                    <tr class="border-b border-slate-100 last:border-0">
                        <td class="px-4 py-2.5 font-semibold text-slate-600 w-40">{{ $spec['label'] }}</td>
                        <td class="px-4 py-2.5 text-slate-700">{{ $spec['value'] ?? '—' }}</td>
                    </tr>
                    @endif
                    @endforeach
                </table>
            </div>
            @endif
        </div>
        @endif

        @else
        {{-- No product selected state --}}
        <div class="ppe-card ppe-empty">
            <svg class="h-12 w-12 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <h3>Select a product above to edit its page</h3>
            <p class="text-sm">Publish products in Shop Products first, then come here to write their page content.</p>
        </div>
        @endif

    </div>
</div>
