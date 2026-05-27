<?php

use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Homepage Editor — ExchoSoft')] class extends Component
{
    // ── Hero ────────────────────────────────────────────────────────────────
    public string $home_hero_tag       = '';
    public string $home_hero_title     = '';
    public string $home_hero_subtitle  = '';
    public string $home_hero_btn_primary_label   = '';
    public string $home_hero_btn_secondary_label = '';

    // ── Stats ────────────────────────────────────────────────────────────────
    public string $home_stats = '';

    // ── About ────────────────────────────────────────────────────────────────
    public string $home_about_tag     = '';
    public string $home_about_title   = '';
    public string $home_about_content = '';
    public string $home_about_cards   = '';

    // ── Products Section ─────────────────────────────────────────────────────
    public string $home_products_tag   = '';
    public string $home_products_title = '';

    // ── Approach ─────────────────────────────────────────────────────────────
    public string $home_approach_tag   = '';
    public string $home_approach_title = '';
    public string $home_approach_cards = '';

    // ── Industries ───────────────────────────────────────────────────────────
    public string $home_industries_tag   = '';
    public string $home_industries_title = '';
    public string $home_industries_cards = '';

    // ── Why Us ───────────────────────────────────────────────────────────────
    public string $home_why_tag   = '';
    public string $home_why_title = '';
    public string $home_why_items = '';

    // ── Trust ────────────────────────────────────────────────────────────────
    public string $home_trust_tag     = '';
    public string $home_trust_title   = '';
    public string $home_trust_subtitle= '';
    public string $home_trust_clients = '';

    // ── CTA ──────────────────────────────────────────────────────────────────
    public string $home_cta_title     = '';
    public string $home_cta_subtitle  = '';
    public string $home_cta_btn       = '';
    public string $home_cta_email_note= '';

    // ── Demo CTA ─────────────────────────────────────────────────────────────
    public string $home_demo_cta_title    = '';
    public string $home_demo_cta_subtitle = '';

    public string $activeTab = 'hero';

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $keys = [
            'home_hero_tag','home_hero_title','home_hero_subtitle',
            'home_hero_btn_primary_label','home_hero_btn_secondary_label',
            'home_stats',
            'home_about_tag','home_about_title','home_about_content','home_about_cards',
            'home_products_tag','home_products_title',
            'home_approach_tag','home_approach_title','home_approach_cards',
            'home_industries_tag','home_industries_title','home_industries_cards',
            'home_why_tag','home_why_title','home_why_items',
            'home_trust_tag','home_trust_title','home_trust_subtitle','home_trust_clients',
            'home_cta_title','home_cta_subtitle','home_cta_btn','home_cta_email_note',
            'home_demo_cta_title','home_demo_cta_subtitle',
        ];

        $settings = SiteSetting::whereIn('key', $keys)->pluck('value', 'key');

        foreach ($keys as $key) {
            if (isset($settings[$key])) {
                $this->$key = $settings[$key];
            }
        }
    }

    public function save(): void
    {
        // Validate JSON fields
        $jsonFields = [
            'home_stats','home_about_cards','home_approach_cards',
            'home_industries_cards','home_why_items','home_trust_clients',
        ];

        foreach ($jsonFields as $field) {
            if ($this->$field) {
                json_decode($this->$field);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->addError($field, 'Invalid JSON format.');
                    return;
                }
            }
        }

        $settings = [
            ['key' => 'home_hero_tag',     'value' => $this->home_hero_tag,     'type' => 'text',     'group' => 'homepage', 'label' => 'Hero Tag'],
            ['key' => 'home_hero_title',   'value' => $this->home_hero_title,   'type' => 'text',     'group' => 'homepage', 'label' => 'Hero Title'],
            ['key' => 'home_hero_subtitle','value' => $this->home_hero_subtitle,'type' => 'text',     'group' => 'homepage', 'label' => 'Hero Subtitle'],
            ['key' => 'home_hero_btn_primary_label',   'value' => $this->home_hero_btn_primary_label,   'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Primary Button'],
            ['key' => 'home_hero_btn_secondary_label', 'value' => $this->home_hero_btn_secondary_label, 'type' => 'text', 'group' => 'homepage', 'label' => 'Hero Secondary Button'],
            ['key' => 'home_stats',        'value' => $this->home_stats,        'type' => 'json',     'group' => 'homepage', 'label' => 'Stats'],
            ['key' => 'home_about_tag',    'value' => $this->home_about_tag,    'type' => 'text',     'group' => 'homepage', 'label' => 'About Tag'],
            ['key' => 'home_about_title',  'value' => $this->home_about_title,  'type' => 'text',     'group' => 'homepage', 'label' => 'About Title'],
            ['key' => 'home_about_content','value' => $this->home_about_content,'type' => 'markdown', 'group' => 'homepage', 'label' => 'About Content'],
            ['key' => 'home_about_cards',  'value' => $this->home_about_cards,  'type' => 'json',     'group' => 'homepage', 'label' => 'About Cards'],
            ['key' => 'home_products_tag',  'value' => $this->home_products_tag,  'type' => 'text', 'group' => 'homepage', 'label' => 'Products Tag'],
            ['key' => 'home_products_title','value' => $this->home_products_title,'type' => 'text', 'group' => 'homepage', 'label' => 'Products Title'],
            ['key' => 'home_approach_tag',   'value' => $this->home_approach_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Tag'],
            ['key' => 'home_approach_title', 'value' => $this->home_approach_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Title'],
            ['key' => 'home_approach_cards', 'value' => $this->home_approach_cards, 'type' => 'json', 'group' => 'homepage', 'label' => 'Approach Cards'],
            ['key' => 'home_industries_tag',   'value' => $this->home_industries_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Tag'],
            ['key' => 'home_industries_title', 'value' => $this->home_industries_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Title'],
            ['key' => 'home_industries_cards', 'value' => $this->home_industries_cards, 'type' => 'json', 'group' => 'homepage', 'label' => 'Industries Cards'],
            ['key' => 'home_why_tag',   'value' => $this->home_why_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Tag'],
            ['key' => 'home_why_title', 'value' => $this->home_why_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Title'],
            ['key' => 'home_why_items', 'value' => $this->home_why_items, 'type' => 'json', 'group' => 'homepage', 'label' => 'Why Us Items'],
            ['key' => 'home_trust_tag',     'value' => $this->home_trust_tag,     'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Tag'],
            ['key' => 'home_trust_title',   'value' => $this->home_trust_title,   'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Title'],
            ['key' => 'home_trust_subtitle','value' => $this->home_trust_subtitle,'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Subtitle'],
            ['key' => 'home_trust_clients', 'value' => $this->home_trust_clients, 'type' => 'json', 'group' => 'homepage', 'label' => 'Trust Clients'],
            ['key' => 'home_cta_title',     'value' => $this->home_cta_title,     'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Title'],
            ['key' => 'home_cta_subtitle',  'value' => $this->home_cta_subtitle,  'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Subtitle'],
            ['key' => 'home_cta_btn',       'value' => $this->home_cta_btn,       'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Button'],
            ['key' => 'home_cta_email_note','value' => $this->home_cta_email_note,'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Email Note'],
            ['key' => 'home_demo_cta_title',    'value' => $this->home_demo_cta_title,    'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Title'],
            ['key' => 'home_demo_cta_subtitle', 'value' => $this->home_demo_cta_subtitle, 'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Subtitle'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        session()->flash('success', 'Homepage content saved successfully!');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.admin.homepage-editor');
    }
}; ?>

<div>
    <x-slot:heading>Homepage Editor</x-slot:heading>

    {{-- EasyMDE Markdown Editor --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        .EasyMDEContainer .CodeMirror { min-height: 150px; font-size: 13px; }
        .admin-tab { padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; }
        .admin-tab.active { background: #0891b2; color: white; }
        .admin-tab:not(.active) { background: #f1f5f9; color: #64748b; }
        .admin-tab:not(.active):hover { background: #e2e8f0; color: #334155; }
        .section-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .section-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; margin-bottom: 0.35rem; display: block; }
        .json-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem; }
        .form-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.55rem 0.8rem; font-size: 0.875rem; color: #1e293b; transition: border-color 0.15s; }
        .form-input:focus { outline: none; border-color: #0891b2; box-shadow: 0 0 0 3px rgba(8,145,178,0.08); }
        .form-textarea { resize: vertical; min-height: 80px; }
        .preview-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; font-size: 0.85rem; color: #475569; min-height: 60px; }
        .preview-box h1, .preview-box h2, .preview-box h3 { color: #0f172a; font-weight: 700; }
        .preview-box p { margin-bottom: 0.5rem; }
        .preview-box strong { color: #0891b2; }
    </style>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-sm">Edit all sections of the homepage. Changes are saved to the database and appear live.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Preview Homepage
                </a>
                <button wire:click="save" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </div>

        {{-- Tab Nav --}}
        <div class="flex flex-wrap gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
            @foreach([
                ['hero', 'Hero'],
                ['stats', 'Stats Bar'],
                ['about', 'About'],
                ['products', 'Products'],
                ['approach', 'Approach'],
                ['industries', 'Industries'],
                ['why', 'Why Us'],
                ['trust', 'Trust'],
                ['cta', 'CTA'],
            ] as [$tab, $label])
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    class="admin-tab {{ $activeTab === $tab ? 'active' : '' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── HERO TAB ─────────────────────────────────────────────────────── --}}
        @if($activeTab === 'hero')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🎯 Hero Section</h3>
            <div>
                <label class="section-label">Tag Line (small badge above title)</label>
                <input wire:model="home_hero_tag" type="text" class="form-input" placeholder="Ghana-Based · Africa · Caribbean · Diaspora">
            </div>
            <div>
                <label class="section-label">Main Title <span class="text-cyan-600 normal-case font-normal ml-1">(wrap in **text** for cyan highlight)</span></label>
                <input wire:model="home_hero_title" type="text" class="form-input" placeholder="Technology Consultancy Built on **Real-World** Experience">
                <p class="json-hint">Use **word** to make a word appear in cyan on the page.</p>
            </div>
            <div>
                <label class="section-label">Sub-headline / Description</label>
                <textarea wire:model="home_hero_subtitle" rows="3" class="form-input form-textarea" placeholder="We're a software development..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Primary Button Label</label>
                    <input wire:model="home_hero_btn_primary_label" type="text" class="form-input" placeholder="Talk to Us">
                </div>
                <div>
                    <label class="section-label">Secondary Button Label</label>
                    <input wire:model="home_hero_btn_secondary_label" type="text" class="form-input" placeholder="Our Products">
                </div>
            </div>
        </div>
        @endif

        {{-- ── STATS TAB ────────────────────────────────────────────────────── --}}
        @if($activeTab === 'stats')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">📊 Stats Bar</h3>
            <div>
                <label class="section-label">Stats (JSON Array)</label>
                <textarea wire:model="home_stats" rows="10" class="form-input form-textarea font-mono text-xs" placeholder='[{"num":"10+","label":"Industries served"},...]'></textarea>
                <p class="json-hint">JSON array of objects with "num" and "label" keys. Example: [{"num":"10+","label":"Industries served"},{"num":"3","label":"Continents"}]</p>
                @error('home_stats') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            @if($home_stats)
            <div>
                <label class="section-label">Preview</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(json_decode($home_stats, true) ?? [] as $stat)
                    <div class="bg-cyan-50 border border-cyan-200 rounded-xl px-4 py-3 text-center">
                        <div class="text-xl font-bold text-cyan-600">{{ $stat['num'] ?? '' }}</div>
                        <div class="text-xs text-slate-500">{{ $stat['label'] ?? '' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── ABOUT TAB ────────────────────────────────────────────────────── --}}
        @if($activeTab === 'about')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🏢 Who We Are / About Section</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_about_tag" type="text" class="form-input" placeholder="Who We Are">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_about_title" type="text" class="form-input" placeholder="Built for the Conditions...">
                </div>
            </div>
            <div>
                <label class="section-label">Main Content (Markdown)</label>
                <textarea id="about-md-editor" wire:model="home_about_content" rows="6" class="form-input form-textarea" placeholder="Write with **bold**, _italic_, headings, lists..."></textarea>
                <p class="json-hint">Markdown supported: **bold**, _italic_, # headings, - lists</p>
            </div>
            <div>
                <label class="section-label">Reality Cards (JSON) — right side cards</label>
                <textarea wire:model="home_about_cards" rows="10" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">JSON array: [{"title":"Card Title","body":"Card description text"},...]</p>
                @error('home_about_cards') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- ── PRODUCTS TAB ─────────────────────────────────────────────────── --}}
        @if($activeTab === 'products')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🛍️ Featured Products Section</h3>
            <p class="text-sm text-slate-500">Products are pulled dynamically from <a href="{{ route('admin.shop-products') }}" wire:navigate class="text-cyan-600 underline">Shop Products</a>. Only featured & published products appear here. Customize the section heading below.</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_products_tag" type="text" class="form-input" placeholder="Our Software">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_products_title" type="text" class="form-input" placeholder="Products Built for African Businesses">
                </div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-700 mb-1">💡 To manage which products appear:</p>
                <p>Go to <a href="{{ route('admin.shop-products') }}" wire:navigate class="text-cyan-600 underline">Shop Products</a> → Edit a product → Check <strong>Featured</strong> and <strong>Published</strong></p>
            </div>
        </div>
        @endif

        {{-- ── APPROACH TAB ─────────────────────────────────────────────────── --}}
        @if($activeTab === 'approach')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🔧 Our Approach Section</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_approach_tag" type="text" class="form-input" placeholder="Our Approach">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_approach_title" type="text" class="form-input" placeholder="What We've Learned...">
                </div>
            </div>
            <div>
                <label class="section-label">Approach Cards (JSON)</label>
                <textarea wire:model="home_approach_cards" rows="16" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">JSON array: [{"icon":"grid","title":"Card Title","body":"Description"},...]<br>Icons: grid, offline, data, lan, shield, partner</p>
                @error('home_approach_cards') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- ── INDUSTRIES TAB ───────────────────────────────────────────────── --}}
        @if($activeTab === 'industries')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🏭 Industries Section</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_industries_tag" type="text" class="form-input" placeholder="Experience">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_industries_title" type="text" class="form-input" placeholder="Industries We've Served">
                </div>
            </div>
            <div>
                <label class="section-label">Industry Cards (JSON)</label>
                <textarea wire:model="home_industries_cards" rows="14" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">JSON array: [{"title":"Healthcare & Medical","body":"Description text"},...]</p>
                @error('home_industries_cards') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- ── WHY US TAB ───────────────────────────────────────────────────── --}}
        @if($activeTab === 'why')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">⭐ Why Us Section</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_why_tag" type="text" class="form-input" placeholder="Why Exchosoft">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_why_title" type="text" class="form-input" placeholder="The Exchosoft Difference">
                </div>
            </div>
            <div>
                <label class="section-label">Why Us Items (JSON)</label>
                <textarea wire:model="home_why_items" rows="12" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">JSON array: [{"title":"Point Title","body":"Description"},...]</p>
                @error('home_why_items') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- ── TRUST TAB ────────────────────────────────────────────────────── --}}
        @if($activeTab === 'trust')
        <div class="section-card space-y-4">
            <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🤝 Trust / Clients Section</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="section-label">Section Tag</label>
                    <input wire:model="home_trust_tag" type="text" class="form-input" placeholder="Trusted By">
                </div>
                <div>
                    <label class="section-label">Section Title</label>
                    <input wire:model="home_trust_title" type="text" class="form-input" placeholder="Organisations That Trust Exchosoft">
                </div>
            </div>
            <div>
                <label class="section-label">Section Subtitle</label>
                <input wire:model="home_trust_subtitle" type="text" class="form-input" placeholder="We've delivered solutions across...">
            </div>
            <div>
                <label class="section-label">Client Pills (JSON string array)</label>
                <textarea wire:model="home_trust_clients" rows="6" class="form-input form-textarea font-mono text-xs"></textarea>
                <p class="json-hint">JSON string array: ["Healthcare Facilities","Church Networks","Laundry Businesses",...]</p>
                @error('home_trust_clients') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            @if($home_trust_clients)
            <div>
                <label class="section-label">Preview</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(json_decode($home_trust_clients, true) ?? [] as $client)
                    <span class="bg-white border border-slate-200 rounded-full px-3 py-1 text-sm text-slate-600">{{ $client }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── CTA TAB ──────────────────────────────────────────────────────── --}}
        @if($activeTab === 'cta')
        <div class="space-y-4">
            <div class="section-card space-y-4">
                <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">🔔 Main CTA Section (cyan background)</h3>
                <div>
                    <label class="section-label">Title</label>
                    <input wire:model="home_cta_title" type="text" class="form-input" placeholder="Ready to Build Something...">
                </div>
                <div>
                    <label class="section-label">Subtitle</label>
                    <textarea wire:model="home_cta_subtitle" rows="2" class="form-input form-textarea" placeholder="Tell us what you need..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="section-label">Button Label</label>
                        <input wire:model="home_cta_btn" type="text" class="form-input" placeholder="Start a Conversation">
                    </div>
                    <div>
                        <label class="section-label">Email Note (below button)</label>
                        <input wire:model="home_cta_email_note" type="text" class="form-input" placeholder="Or email us at hello@exchosoft.com">
                    </div>
                </div>
            </div>
            <div class="section-card space-y-4">
                <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">📅 Demo CTA Section (dark navy background)</h3>
                <div>
                    <label class="section-label">Title</label>
                    <input wire:model="home_demo_cta_title" type="text" class="form-input" placeholder="See Our Software in Action">
                </div>
                <div>
                    <label class="section-label">Subtitle</label>
                    <textarea wire:model="home_demo_cta_subtitle" rows="2" class="form-input form-textarea" placeholder="Book a live demonstration..."></textarea>
                </div>
            </div>
        </div>
        @endif

        {{-- Save button bottom --}}
        <div class="flex justify-end pt-2">
            <button wire:click="save" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save All Homepage Changes
            </button>
        </div>

    </div>
</div>
