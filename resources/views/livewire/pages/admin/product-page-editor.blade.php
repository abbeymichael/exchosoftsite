<?php

use App\Models\ProductPageSection;
use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Page Banner Editor — ExchoSoft')] class extends Component
{
    public string $activeProduct = 'washops';

    // Banner fields
    public string $banner_tag      = '';
    public string $banner_title    = '';
    public string $banner_subtitle = '';
    public string $banner_badge    = '';
    public string $banner_theme    = 'cyan';
    public string $banner_btn_primary   = '';
    public string $banner_btn_secondary = '';
    public string $banner_stats    = ''; // JSON array [{value, label}]

    // Products page banner (stored in site_settings)
    public string $products_banner_tag      = '';
    public string $products_banner_title    = '';
    public string $products_banner_subtitle = '';

    public function mount(): void
    {
        $this->loadBanner();
        $this->loadProductsPageBanner();
    }

    public function updatedActiveProduct(): void
    {
        $this->loadBanner();
    }

    protected function loadBanner(): void
    {
        $sections = ProductPageSection::getForProduct($this->activeProduct);
        $hero = $sections->get('hero');

        if ($hero) {
            $this->banner_tag      = $hero->data['badge']               ?? '';
            $this->banner_title    = $hero->data['page_banner_title']   ?? '';
            $this->banner_subtitle = $hero->data['page_banner_subtitle'] ?? '';
            $this->banner_theme    = $hero->data['banner_theme']        ?? ($this->activeProduct === 'churchops' ? 'green' : 'cyan');
            $this->banner_btn_primary   = $hero->data['btn_primary_label']   ?? '';
            $this->banner_btn_secondary = $hero->data['btn_secondary_label'] ?? '';
            $this->banner_stats    = isset($hero->data['page_banner_stats'])
                ? json_encode($hero->data['page_banner_stats'], JSON_PRETTY_PRINT)
                : '';
        } else {
            // Defaults
            $this->banner_tag      = $this->activeProduct === 'washops' ? 'WashOps' : 'ChurchOps';
            $this->banner_title    = $this->activeProduct === 'washops'
                ? 'Complete Laundry Management for **Modern Businesses**'
                : 'Church Management That Works — **Even When the Internet Doesn\'t**';
            $this->banner_subtitle = '';
            $this->banner_theme    = $this->activeProduct === 'churchops' ? 'green' : 'cyan';
            $this->banner_btn_primary   = '';
            $this->banner_btn_secondary = '';
            $this->banner_stats    = '';
        }
    }

    protected function loadProductsPageBanner(): void
    {
        $settings = SiteSetting::whereIn('key', [
            'products_banner_tag', 'products_banner_title', 'products_banner_subtitle',
        ])->pluck('value', 'key');

        $this->products_banner_tag      = $settings['products_banner_tag']      ?? 'Software built for Africa';
        $this->products_banner_title    = $settings['products_banner_title']    ?? 'Real Software for **Real Conditions**';
        $this->products_banner_subtitle = $settings['products_banner_subtitle'] ?? 'Industry-specific platforms built offline-first, designed for the realities of doing business in Ghana and across our markets.';
    }

    public function saveBanner(): void
    {
        // Validate stats JSON if provided
        if ($this->banner_stats) {
            json_decode($this->banner_stats);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('banner_stats', 'Invalid JSON. Use format: [{"value":"5+","label":"Years"},...]');
                return;
            }
        }

        $sections = ProductPageSection::getForProduct($this->activeProduct);
        $existing = $sections->get('hero');

        // Build merged data — preserve existing hero content data
        $existingData = $existing?->data ?? [];

        $newData = array_merge($existingData, [
            'badge'               => $this->banner_tag,
            'page_banner_title'   => $this->banner_title,
            'page_banner_subtitle'=> $this->banner_subtitle,
            'banner_theme'        => $this->banner_theme,
            'btn_primary_label'   => $this->banner_btn_primary,
            'btn_secondary_label' => $this->banner_btn_secondary,
            'page_banner_stats'   => $this->banner_stats ? json_decode($this->banner_stats, true) : [],
            'badge_class'         => match($this->banner_theme) {
                'green'  => 'badge-church',
                'violet' => 'badge-custom',
                default  => 'badge-wash',
            },
        ]);

        ProductPageSection::upsertSection($this->activeProduct, 'hero', [
            'product_code' => $this->activeProduct,
            'section_key'  => 'hero',
            'label'        => ucfirst($this->activeProduct) . ' Page Banner',
            'content'      => $existing?->content ?? '',
            'type'         => 'markdown',
            'is_active'    => true,
            'sort_order'   => 1,
            'data'         => $newData,
        ]);

        session()->flash('success', ucfirst($this->activeProduct) . ' banner saved!');
    }

    public function saveProductsPageBanner(): void
    {
        $items = [
            ['key' => 'products_banner_tag',      'value' => $this->products_banner_tag,      'type' => 'text', 'group' => 'products_page', 'label' => 'Products Page Banner Tag'],
            ['key' => 'products_banner_title',    'value' => $this->products_banner_title,    'type' => 'text', 'group' => 'products_page', 'label' => 'Products Page Banner Title'],
            ['key' => 'products_banner_subtitle', 'value' => $this->products_banner_subtitle, 'type' => 'text', 'group' => 'products_page', 'label' => 'Products Page Banner Subtitle'],
        ];
        foreach ($items as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }
        session()->flash('success', 'Products page banner saved!');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.admin.product-page-editor');
    }
}; ?>

<div>
<x-slot:heading>
    <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100">
            <svg class="h-4 w-4 text-violet-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
        </div>
        Page Banner Editor
    </div>
</x-slot:heading>

<style>
/* ── Shared card styles ── */
.ppe-card {
    background:#fff; border-radius:14px; border:1px solid #e2e8f0;
    overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.04);
}
.ppe-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;
    background:linear-gradient(135deg,#f8fafc 0%,#fff 100%);
}
.ppe-card-title { font-size:0.9rem; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
.ppe-card-body { padding:1.25rem; display:flex; flex-direction:column; gap:1rem; }

/* ── Product switcher ── */
.ppe-prod-btn {
    display:inline-flex; align-items:center; gap:8px;
    padding:9px 18px; border-radius:10px; font-size:0.85rem; font-weight:700;
    cursor:pointer; border:2px solid transparent; transition:all 0.15s;
}
.ppe-prod-btn-wash  { background:#ecfeff; color:#0e7490; border-color:#a5f3fc; }
.ppe-prod-btn-wash.active, .ppe-prod-btn-wash:hover  { background:#0e7490; color:#fff; border-color:#0e7490; box-shadow:0 2px 10px rgba(14,116,144,0.3); }
.ppe-prod-btn-church { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
.ppe-prod-btn-church.active, .ppe-prod-btn-church:hover { background:#166534; color:#fff; border-color:#166534; box-shadow:0 2px 10px rgba(22,101,52,0.3); }

/* ── Fields ── */
.ppe-field { display:flex; flex-direction:column; gap:5px; }
.ppe-field-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.ppe-label {
    font-size:0.7rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.07em; color:#94a3b8; display:flex; align-items:center; gap:6px;
}
.ppe-badge { font-size:0.6rem; font-weight:700; padding:1px 6px; border-radius:100px; text-transform:none; letter-spacing:0; }
.badge-text  { background:#e0f2fe; color:#0369a1; }
.badge-json  { background:#ede9fe; color:#5b21b6; }

.ppe-input, .ppe-textarea, .ppe-select {
    width:100%; border:1.5px solid #e2e8f0; border-radius:9px;
    padding:0.55rem 0.85rem; font-size:0.875rem; color:#1e293b;
    font-family:inherit; background:#fff;
    transition:border-color 0.15s, box-shadow 0.15s;
}
.ppe-input:focus, .ppe-textarea:focus, .ppe-select:focus {
    outline:none; border-color:#0891b2; box-shadow:0 0 0 3px rgba(8,145,178,0.1);
}
.ppe-textarea { resize:vertical; }
.ppe-mono { font-family:'SFMono-Regular',Menlo,monospace; font-size:0.78rem; }
.ppe-hint { font-size:0.72rem; color:#94a3b8; }
.ppe-hint code { background:#f1f5f9; padding:1px 5px; border-radius:4px; font-size:0.7rem; color:#475569; }
.ppe-error { font-size:0.75rem; color:#dc2626; display:flex; align-items:center; gap:4px; }

/* ── Save/action buttons ── */
.ppe-save-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 20px; border-radius:9px; font-size:0.85rem; font-weight:700;
    border:none; cursor:pointer; transition:all 0.15s;
}
.ppe-save-wash   { background:#0e7490; color:#fff; box-shadow:0 2px 8px rgba(14,116,144,0.25); }
.ppe-save-wash:hover   { background:#0c6179; }
.ppe-save-church { background:#166534; color:#fff; box-shadow:0 2px 8px rgba(22,101,52,0.25); }
.ppe-save-church:hover { background:#14532d; }
.ppe-save-violet { background:#6d28d9; color:#fff; box-shadow:0 2px 8px rgba(109,40,217,0.2); }
.ppe-save-violet:hover { background:#5b21b6; }

/* ── Live preview ── */
.ppe-banner-preview {
    border-radius:12px; overflow:hidden; border:1px solid rgba(0,0,0,0.08);
    box-shadow:0 4px 20px rgba(0,0,0,0.1);
}
.ppe-banner-inner {
    background:#0d2136; position:relative; padding:2.5rem 2.5rem 2rem;
    min-height:180px;
}
.ppe-preview-dots {
    position:absolute; inset:0;
    background-image:radial-gradient(circle,rgba(0,184,219,0.14) 1px,transparent 1px);
    background-size:28px 28px; pointer-events:none;
}
.ppe-preview-glow-cyan   { position:absolute; inset:0; background:radial-gradient(circle at 80% 50%,rgba(0,184,219,0.12) 0%,transparent 60%); pointer-events:none; }
.ppe-preview-glow-green  { position:absolute; inset:0; background:radial-gradient(circle at 80% 50%,rgba(76,175,130,0.14) 0%,transparent 60%); pointer-events:none; }
.ppe-preview-glow-violet { position:absolute; inset:0; background:radial-gradient(circle at 80% 50%,rgba(139,92,246,0.14) 0%,transparent 60%); pointer-events:none; }
.ppe-preview-content { position:relative; z-index:2; }
.ppe-preview-crumb { font-size:0.7rem; color:rgba(255,255,255,0.4); margin-bottom:0.75rem; display:flex; gap:5px; }
.ppe-preview-crumb .sep { color:rgba(255,255,255,0.2); }
.ppe-preview-crumb .cur { color:#00b8db; }
.ppe-preview-tag {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(0,184,219,0.12); border:1px solid rgba(0,184,219,0.25);
    color:#7acfe8; padding:3px 10px; border-radius:100px;
    font-size:0.65rem; font-weight:700; letter-spacing:0.07em;
    text-transform:uppercase; margin-bottom:0.65rem;
}
.ppe-preview-tag.green { background:rgba(76,175,130,0.12); border-color:rgba(76,175,130,0.25); color:#4caf82; }
.ppe-preview-tag.violet{ background:rgba(139,92,246,0.12); border-color:rgba(139,92,246,0.25); color:#a78bfa; }
.ppe-preview-h1 {
    font-size:clamp(1rem,2vw,1.5rem); font-weight:800; color:#fff;
    line-height:1.15; letter-spacing:-0.02em; margin-bottom:0.5rem;
}
.ppe-preview-h1 em { font-style:normal; color:#00b8db; }
.ppe-preview-h1 em.green { color:#4caf82; }
.ppe-preview-h1 em.violet { color:#a78bfa; }
.ppe-preview-sub { font-size:0.78rem; color:rgba(255,255,255,0.5); line-height:1.6; font-weight:300; max-width:460px; }

/* ── Info box ── */
.ppe-info {
    background:#fafaf9; border:1px solid #e7e5e4; border-radius:10px;
    padding:0.85rem 1rem; font-size:0.8rem; color:#57534e;
    display:flex; gap:0.6rem; align-items:flex-start;
}
.ppe-info svg { width:16px; height:16px; flex-shrink:0; color:#a8a29e; margin-top:1px; }

/* ── Toast ── */
.ppe-toast {
    display:flex; align-items:center; gap:10px;
    background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px;
    padding:12px 16px; font-size:0.875rem; color:#166534;
}
.ppe-toast svg { width:18px; height:18px; color:#16a34a; flex-shrink:0; }

/* ── Section divider ── */
.ppe-divider {
    display:flex; align-items:center; gap:1rem; color:#94a3b8; font-size:0.75rem; font-weight:600;
}
.ppe-divider::before, .ppe-divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }

@media(max-width:768px) { .ppe-field-row { grid-template-columns:1fr; } }
</style>

<div class="space-y-5">

    {{-- Toast --}}
    @if(session('success'))
    <div class="ppe-toast">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Edit page banner content for each product and the main products listing page.</p>
        <a href="{{ route('site.products') }}" target="_blank"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-cyan-300 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Preview Products Page
        </a>
    </div>

    {{-- ══════════ PRODUCTS PAGE BANNER ══════════ --}}
    <div class="ppe-divider">Products Listing Page Banner</div>
    <div class="ppe-card">
        <div class="ppe-card-header">
            <div class="ppe-card-title">
                <svg style="width:18px;height:18px;color:#6d28d9;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                /products — Page Banner
            </div>
            <button wire:click="saveProductsPageBanner" class="ppe-save-btn ppe-save-violet">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Products Banner
            </button>
        </div>
        <div class="ppe-card-body">
            <div class="ppe-info">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                This controls the top banner on the <strong>/products</strong> page (the listing of all products). Use <code>**word**</code> for cyan highlight.
            </div>
            <div class="ppe-field">
                <label class="ppe-label">Tag Badge <span class="ppe-badge badge-text">text</span></label>
                <input wire:model="products_banner_tag" type="text" class="ppe-input" placeholder="Software built for Africa">
            </div>
            <div class="ppe-field">
                <label class="ppe-label">Banner Title <span class="ppe-badge badge-text">text + **highlights**</span></label>
                <input wire:model="products_banner_title" type="text" class="ppe-input" placeholder="Real Software for **Real Conditions**">
                <p class="ppe-hint">Use <code>**word**</code> for cyan accent colour.</p>
            </div>
            <div class="ppe-field">
                <label class="ppe-label">Subtitle / Description</label>
                <textarea wire:model="products_banner_subtitle" rows="2" class="ppe-textarea" placeholder="Industry-specific platforms built offline-first..."></textarea>
            </div>
            {{-- Live preview --}}
            <div>
                <p class="ppe-hint" style="margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;">Live Preview</p>
                <div class="ppe-banner-preview">
                    <div class="ppe-banner-inner">
                        <div class="ppe-preview-dots"></div>
                        <div class="ppe-preview-glow-cyan"></div>
                        <div class="ppe-preview-content">
                            <div class="ppe-preview-crumb">
                                <span>Home</span><span class="sep">/</span><span class="cur">Our Products</span>
                            </div>
                            @if($products_banner_tag)
                            <div class="ppe-preview-tag">{{ $products_banner_tag }}</div>
                            @endif
                            <div class="ppe-preview-h1">
                                {!! preg_replace('/\*\*(.+?)\*\*/', '<em>$1</em>', e($products_banner_title ?: 'Real Software for **Real Conditions**')) !!}
                            </div>
                            @if($products_banner_subtitle)
                            <div class="ppe-preview-sub">{{ $products_banner_subtitle }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ PRODUCT-SPECIFIC BANNERS ══════════ --}}
    <div class="ppe-divider">Product Page Banners (WashOps / ChurchOps)</div>

    {{-- Product selector --}}
    <div class="flex items-center gap-3">
        <span class="text-sm font-semibold text-slate-600">Editing banner for:</span>
        <div class="flex gap-2">
            <button wire:click="$set('activeProduct','washops')"
                    class="ppe-prod-btn ppe-prod-btn-wash {{ $activeProduct === 'washops' ? 'active' : '' }}">
                🌊 WashOps
            </button>
            <button wire:click="$set('activeProduct','churchops')"
                    class="ppe-prod-btn ppe-prod-btn-church {{ $activeProduct === 'churchops' ? 'active' : '' }}">
                ⛪ ChurchOps
            </button>
        </div>
    </div>

    <div class="ppe-card">
        <div class="ppe-card-header">
            <div class="ppe-card-title">
                @if($activeProduct === 'washops')
                    <span style="color:#0e7490;">🌊</span> WashOps — Product Page Banner
                @else
                    <span style="color:#166534;">⛪</span> ChurchOps — Product Page Banner
                @endif
            </div>
            <button wire:click="saveBanner"
                    class="ppe-save-btn {{ $activeProduct === 'washops' ? 'ppe-save-wash' : 'ppe-save-church' }}">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save {{ $activeProduct === 'washops' ? 'WashOps' : 'ChurchOps' }} Banner
            </button>
        </div>
        <div class="ppe-card-body">
            <div class="ppe-info">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                This controls the page top banner when a visitor opens the dedicated product page. Use <code>**word**</code> for coloured highlights.
            </div>

            <div class="ppe-field-row">
                <div class="ppe-field">
                    <label class="ppe-label">Tag / Badge Text <span class="ppe-badge badge-text">text</span></label>
                    <input wire:model="banner_tag" type="text" class="ppe-input"
                           placeholder="{{ $activeProduct === 'washops' ? 'WashOps' : 'ChurchOps' }}">
                </div>
                <div class="ppe-field">
                    <label class="ppe-label">Banner Theme</label>
                    <select wire:model="banner_theme" class="ppe-select">
                        <option value="cyan">Cyan (WashOps default)</option>
                        <option value="green">Green (ChurchOps default)</option>
                        <option value="violet">Violet</option>
                    </select>
                </div>
            </div>

            <div class="ppe-field">
                <label class="ppe-label">Banner Title <span class="ppe-badge badge-text">text + **highlights**</span></label>
                <input wire:model="banner_title" type="text" class="ppe-input"
                       placeholder="{{ $activeProduct === 'washops' ? 'Complete Laundry Management for **Modern Businesses**' : 'Church Management That Works — **Even When the Internet Doesn\'t**' }}">
                <p class="ppe-hint">Use <code>**word**</code> for accent-coloured highlights matching the theme.</p>
            </div>

            <div class="ppe-field">
                <label class="ppe-label">Banner Subtitle</label>
                <textarea wire:model="banner_subtitle" rows="2" class="ppe-textarea"
                          placeholder="{{ $activeProduct === 'washops' ? 'Enterprise-grade desktop application for modern laundry businesses.' : 'The first offline-first church management system built for Ghanaian churches.' }}"></textarea>
            </div>

            <div class="ppe-field-row">
                <div class="ppe-field">
                    <label class="ppe-label">Primary Button Label</label>
                    <input wire:model="banner_btn_primary" type="text" class="ppe-input"
                           placeholder="{{ $activeProduct === 'washops' ? 'Start Free Trial' : 'Book Free Demo' }}">
                </div>
                <div class="ppe-field">
                    <label class="ppe-label">Secondary Button Label</label>
                    <input wire:model="banner_btn_secondary" type="text" class="ppe-input"
                           placeholder="{{ $activeProduct === 'washops' ? 'Read White Paper' : 'Download White Paper' }}">
                </div>
            </div>

            <div class="ppe-field">
                <label class="ppe-label">Banner Stats <span class="ppe-badge badge-json">JSON</span> <span style="font-size:0.65rem;color:#94a3b8;font-weight:400;text-transform:none;">(optional)</span></label>
                <textarea wire:model="banner_stats" rows="4" class="ppe-textarea ppe-mono"
                          placeholder='[{"value":"500+","label":"Businesses served"},{"value":"₵2M+","label":"Revenue tracked"}]'></textarea>
                <p class="ppe-hint">Optional stats shown at the bottom of the banner. Format: <code>[{"value":"500+","label":"Description"},...]</code></p>
                @error('banner_stats') <p class="ppe-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>

            {{-- Live preview --}}
            <div>
                <p class="ppe-hint" style="margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;">Live Preview</p>
                <div class="ppe-banner-preview">
                    <div class="ppe-banner-inner" style="{{ $banner_theme === 'church' || ($banner_theme === 'green' && $activeProduct === 'churchops') ? 'background:#0f2d1f;' : '' }}">
                        <div class="ppe-preview-dots" style="{{ $banner_theme === 'green' ? 'background-image:radial-gradient(circle,rgba(76,175,130,0.14) 1px,transparent 1px);background-size:28px 28px;' : '' }}"></div>
                        <div class="ppe-preview-glow-{{ $banner_theme === 'green' ? 'green' : ($banner_theme === 'violet' ? 'violet' : 'cyan') }}"></div>
                        <div class="ppe-preview-content">
                            <div class="ppe-preview-crumb">
                                <span>Home</span><span class="sep">/</span>
                                <span>Products</span><span class="sep">/</span>
                                <span class="cur" style="{{ $banner_theme === 'green' ? 'color:#4caf82;' : ($banner_theme === 'violet' ? 'color:#a78bfa;' : '') }}">{{ $banner_tag ?: ($activeProduct === 'washops' ? 'WashOps' : 'ChurchOps') }}</span>
                            </div>
                            @if($banner_tag)
                            <div class="ppe-preview-tag {{ $banner_theme === 'green' ? 'green' : ($banner_theme === 'violet' ? 'violet' : '') }}">
                                {{ $banner_tag }}
                            </div>
                            @endif
                            @php
                                $accentStyle = $banner_theme === 'green' ? 'color:#4caf82;' : ($banner_theme === 'violet' ? 'color:#a78bfa;' : '');
                                $previewTitle = $banner_title ?: ($activeProduct === 'washops' ? 'Complete Laundry Management for **Modern Businesses**' : 'Church Management That Works — **Even When the Internet Doesn\'t**');
                                $parsedPreview = preg_replace('/\*\*(.+?)\*\*/', '<em style="font-style:normal;'.$accentStyle.'">$1</em>', e($previewTitle));
                            @endphp
                            <div class="ppe-preview-h1">{!! $parsedPreview !!}</div>
                            @if($banner_subtitle)
                            <div class="ppe-preview-sub">{{ $banner_subtitle }}</div>
                            @endif
                            @if($banner_btn_primary || $banner_btn_secondary)
                            <div style="display:flex;gap:8px;margin-top:1rem;flex-wrap:wrap;">
                                @if($banner_btn_primary)
                                <div style="background:{{ $banner_theme === 'green' ? '#1a6b4a' : ($banner_theme === 'violet' ? '#6d28d9' : '#00b8db') }};color:#fff;padding:6px 14px;border-radius:6px;font-size:0.7rem;font-weight:700;">{{ $banner_btn_primary }}</div>
                                @endif
                                @if($banner_btn_secondary)
                                <div style="background:transparent;color:{{ $banner_theme === 'green' ? '#4caf82' : ($banner_theme === 'violet' ? '#a78bfa' : '#7acfe8') }};padding:6px 14px;border-radius:6px;border:1px solid;font-size:0.7rem;font-weight:700;">{{ $banner_btn_secondary }}</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
