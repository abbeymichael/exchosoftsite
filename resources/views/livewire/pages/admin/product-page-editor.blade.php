<?php

use App\Models\ProductPageSection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Product Page Editor — ExchoSoft')] class extends Component
{
    public string $activeProduct = 'washops';
    public string $activeSection = 'hero';

    // Hero
    public string $hero_content = '';
    public string $hero_badge   = '';
    public string $hero_btn_primary   = '';
    public string $hero_btn_secondary = '';
    public string $hero_theme   = 'wash';

    // Features
    public string $features_title    = '';
    public string $features_subtitle = '';
    public string $features_data     = '';

    // ROI/Impact
    public string $roi_number   = '';
    public string $roi_title    = '';
    public string $roi_subtitle = '';

    public function mount(): void
    {
        $this->loadSections();
    }

    public function updatedActiveProduct(): void
    {
        $this->activeSection = 'hero';
        $this->loadSections();
    }

    public function updatedActiveSection(): void
    {
        $this->loadSections();
    }

    protected function loadSections(): void
    {
        $sections = ProductPageSection::getForProduct($this->activeProduct);

        // Hero
        $hero = $sections->get('hero');
        if ($hero) {
            $this->hero_content       = $hero->content ?? '';
            $this->hero_badge         = $hero->data['badge'] ?? '';
            $this->hero_btn_primary   = $hero->data['btn_primary_label'] ?? '';
            $this->hero_btn_secondary = $hero->data['btn_secondary_label'] ?? '';
            $this->hero_theme         = $hero->data['theme'] ?? 'wash';
        }

        // Features
        $features = $sections->get('features');
        if ($features) {
            $this->features_title    = $features->content ?? '';
            $this->features_subtitle = $features->data['subtitle'] ?? '';
            $this->features_data     = json_encode($features->data['features'] ?? [], JSON_PRETTY_PRINT);
        }

        // ROI
        $roi = $sections->get('roi');
        if ($roi) {
            $this->roi_number   = $roi->data['number'] ?? '';
            $this->roi_title    = $roi->data['title'] ?? '';
            $this->roi_subtitle = $roi->data['subtitle'] ?? '';
        }
    }

    public function saveHero(): void
    {
        // Determine badge_class and accent_class from theme
        $themeMap = [
            'wash'   => ['badge_class' => 'badge-wash',   'accent_class' => 'accent-wash'],
            'church' => ['badge_class' => 'badge-church', 'accent_class' => 'accent-church'],
            'custom' => ['badge_class' => 'badge-custom', 'accent_class' => 'accent-custom'],
        ];
        $theme = $themeMap[$this->hero_theme] ?? $themeMap['wash'];

        ProductPageSection::upsertSection($this->activeProduct, 'hero', [
            'product_code' => $this->activeProduct,
            'section_key'  => 'hero',
            'label'        => ucfirst($this->activeProduct) . ' Hero Section',
            'content'      => $this->hero_content,
            'type'         => 'markdown',
            'is_active'    => true,
            'sort_order'   => 1,
            'data'         => [
                'badge'               => $this->hero_badge,
                'btn_primary_label'   => $this->hero_btn_primary,
                'btn_secondary_label' => $this->hero_btn_secondary,
                'theme'               => $this->hero_theme,
                'badge_class'         => $theme['badge_class'],
                'accent_class'        => $theme['accent_class'],
            ],
        ]);

        session()->flash('success', 'Hero section saved!');
    }

    public function saveFeatures(): void
    {
        // Validate JSON
        $decoded = json_decode($this->features_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError('features_data', 'Invalid JSON format.');
            return;
        }

        $bgMap = ['wash' => 'bg-ice', 'church' => 'bg-church', 'custom' => 'bg-ice'];

        ProductPageSection::upsertSection($this->activeProduct, 'features', [
            'product_code' => $this->activeProduct,
            'section_key'  => 'features',
            'label'        => ucfirst($this->activeProduct) . ' Features',
            'content'      => $this->features_title,
            'type'         => 'json',
            'is_active'    => true,
            'sort_order'   => 2,
            'data'         => [
                'subtitle' => $this->features_subtitle,
                'bg_class' => $bgMap[$this->hero_theme] ?? 'bg-ice',
                'features' => $decoded,
            ],
        ]);

        session()->flash('success', 'Features section saved!');
    }

    public function saveRoi(): void
    {
        ProductPageSection::upsertSection($this->activeProduct, 'roi', [
            'product_code' => $this->activeProduct,
            'section_key'  => 'roi',
            'label'        => ucfirst($this->activeProduct) . ' ROI Callout',
            'content'      => null,
            'type'         => 'json',
            'is_active'    => true,
            'sort_order'   => 3,
            'data'         => [
                'number'   => $this->roi_number,
                'title'    => $this->roi_title,
                'subtitle' => $this->roi_subtitle,
            ],
        ]);

        session()->flash('success', 'ROI/Impact callout saved!');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.admin.product-page-editor');
    }
}; ?>

<div>
    <x-slot:heading>Product Page Editor</x-slot:heading>

    {{-- EasyMDE --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

    <style>
        .EasyMDEContainer .CodeMirror { min-height: 180px; font-size: 13px; }
        .prod-tab { padding: 0.6rem 1.2rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; }
        .prod-tab.active-wash   { background: #0891b2; color: white; }
        .prod-tab.active-church { background: #1a6b4a; color: white; }
        .prod-tab.active        { background: #334155; color: white; }
        .prod-tab:not(.active):not(.active-wash):not(.active-church) { background: #f1f5f9; color: #64748b; }
        .prod-tab:not(.active):not(.active-wash):not(.active-church):hover { background: #e2e8f0; }
        .section-tab { padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; }
        .section-tab.active { background: #e0f2fe; color: #0891b2; }
        .section-tab:not(.active) { background: transparent; color: #64748b; }
        .section-tab:not(.active):hover { background: #f8fafc; }
        .section-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .section-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; margin-bottom: 0.35rem; display: block; }
        .form-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.55rem 0.8rem; font-size: 0.875rem; color: #1e293b; transition: border-color 0.15s; }
        .form-input:focus { outline: none; border-color: #0891b2; }
        .form-textarea { resize: vertical; }
        .json-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem; }
        .feature-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; }
    </style>

    <div class="space-y-5">

        @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Product Selector --}}
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-slate-600">Product:</span>
            <div class="flex gap-2">
                <button wire:click="$set('activeProduct', 'washops')"
                        class="prod-tab {{ $activeProduct === 'washops' ? 'active-wash' : '' }}">
                    🌊 WashOps
                </button>
                <button wire:click="$set('activeProduct', 'churchops')"
                        class="prod-tab {{ $activeProduct === 'churchops' ? 'active-church' : '' }}">
                    ⛪ ChurchOps
                </button>
            </div>
            <div class="ml-auto">
                <a href="{{ route('site.products') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Preview Products Page
                </a>
            </div>
        </div>

        {{-- Section Tabs --}}
        <div class="flex gap-1 bg-slate-50 p-1.5 rounded-xl border border-slate-100 w-fit">
            @foreach([['hero','🎯 Hero'],['features','⚡ Features'],['roi','📈 ROI/Impact']] as [$s,$l])
            <button wire:click="$set('activeSection', '{{ $s }}')"
                    class="section-tab {{ $activeSection === $s ? 'active' : '' }}">
                {{ $l }}
            </button>
            @endforeach
        </div>

        {{-- ── HERO EDITOR ─────────────────────────────────────────────────────── --}}
        @if($activeSection === 'hero')
        <div class="section-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-semibold text-slate-900 text-base">🎯 Hero Section — {{ $activeProduct === 'washops' ? 'WashOps' : 'ChurchOps' }}</h3>
                <button wire:click="saveHero"
                        class="inline-flex items-center gap-1.5 rounded-xl {{ $activeProduct === 'washops' ? 'bg-cyan-600 hover:bg-cyan-700' : 'bg-emerald-700 hover:bg-emerald-800' }} px-4 py-2 text-sm font-semibold text-white transition-colors">
                    Save Hero
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Product Badge Text</label>
                    <input wire:model="hero_badge" type="text" class="form-input" placeholder="WashOps">
                </div>
                <div>
                    <label class="section-label">Color Theme</label>
                    <select wire:model="hero_theme" class="form-input">
                        <option value="wash">Cyan (WashOps)</option>
                        <option value="church">Green (ChurchOps)</option>
                        <option value="custom">Dark (Custom)</option>
                    </select>
                </div>
                <div>
                    <label class="section-label">Primary Button Label</label>
                    <input wire:model="hero_btn_primary" type="text" class="form-input" placeholder="Start Free Trial">
                </div>
                <div>
                    <label class="section-label">Secondary Button Label</label>
                    <input wire:model="hero_btn_secondary" type="text" class="form-input" placeholder="Read White Paper">
                </div>
            </div>

            <div>
                <label class="section-label">Hero Content (Markdown)</label>
                <p class="text-xs text-slate-400 mb-2">Use ## for the heading. Use **text** for colored accent words. Write the description paragraph below the heading.</p>
                <textarea id="hero-md-editor" wire:model="hero_content" rows="8" class="form-input form-textarea font-mono text-sm"></textarea>
                <p class="json-hint">Markdown: ## Heading, **bold/accent**, paragraphs, - lists</p>
            </div>
        </div>
        @endif

        {{-- ── FEATURES EDITOR ──────────────────────────────────────────────────── --}}
        @if($activeSection === 'features')
        <div class="section-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-semibold text-slate-900 text-base">⚡ Features Section</h3>
                <button wire:click="saveFeatures"
                        class="inline-flex items-center gap-1.5 rounded-xl {{ $activeProduct === 'washops' ? 'bg-cyan-600 hover:bg-cyan-700' : 'bg-emerald-700 hover:bg-emerald-800' }} px-4 py-2 text-sm font-semibold text-white transition-colors">
                    Save Features
                </button>
            </div>

            <div>
                <label class="section-label">Features Section Title</label>
                <input wire:model="features_title" type="text" class="form-input" placeholder="Powerful Features Built for Laundry Businesses">
            </div>
            <div>
                <label class="section-label">Features Section Subtitle</label>
                <textarea wire:model="features_subtitle" rows="2" class="form-input form-textarea" placeholder="Everything you need to manage..."></textarea>
            </div>
            <div>
                <label class="section-label">Feature Cards (JSON Array)</label>
                <textarea wire:model="features_data" rows="20" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">
                    JSON array of feature groups:<br>
                    <code>[{"icon":"📊","title":"Analytics Dashboard","items":["Feature 1","Feature 2",...]}]</code>
                </p>
                @error('features_data') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            @if($features_data)
            @php $featuresDecoded = json_decode($features_data, true); @endphp
            @if(is_array($featuresDecoded))
            <div>
                <label class="section-label">Preview ({{ count($featuresDecoded) }} feature groups)</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(array_slice($featuresDecoded, 0, 4) as $feat)
                    <div class="feature-preview">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">{{ $feat['icon'] ?? '📋' }}</span>
                            <span class="font-semibold text-slate-900 text-sm">{{ $feat['title'] ?? '' }}</span>
                        </div>
                        <ul class="text-xs text-slate-500 space-y-0.5">
                            @foreach(array_slice($feat['items'] ?? [], 0, 3) as $item)
                            <li>• {{ $item }}</li>
                            @endforeach
                            @if(count($feat['items'] ?? []) > 3)<li class="text-slate-400">+{{ count($feat['items']) - 3 }} more...</li>@endif
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif
        </div>
        @endif

        {{-- ── ROI EDITOR ───────────────────────────────────────────────────────── --}}
        @if($activeSection === 'roi')
        <div class="section-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-semibold text-slate-900 text-base">📈 ROI / Impact Callout</h3>
                <button wire:click="saveRoi"
                        class="inline-flex items-center gap-1.5 rounded-xl {{ $activeProduct === 'washops' ? 'bg-cyan-600 hover:bg-cyan-700' : 'bg-emerald-700 hover:bg-emerald-800' }} px-4 py-2 text-sm font-semibold text-white transition-colors">
                    Save ROI
                </button>
            </div>
            <div>
                <label class="section-label">Big Number / Metric</label>
                <input wire:model="roi_number" type="text" class="form-input" placeholder="40%">
            </div>
            <div>
                <label class="section-label">Metric Title</label>
                <input wire:model="roi_title" type="text" class="form-input" placeholder="Average Revenue Increase">
            </div>
            <div>
                <label class="section-label">Description</label>
                <textarea wire:model="roi_subtitle" rows="4" class="form-input form-textarea" placeholder="Businesses that implement WashOps typically see..."></textarea>
            </div>

            {{-- Preview --}}
            <div class="bg-slate-900 rounded-xl p-6 flex items-center gap-8">
                <div class="text-5xl font-black {{ $activeProduct === 'churchops' ? 'text-green-400' : 'text-cyan-400' }}">{{ $roi_number ?: '40%' }}</div>
                <div>
                    <div class="font-bold text-white text-base mb-1">{{ $roi_title ?: 'Metric Title' }}</div>
                    <div class="text-slate-400 text-sm max-w-md">{{ $roi_subtitle ?: 'Description of the impact...' }}</div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
