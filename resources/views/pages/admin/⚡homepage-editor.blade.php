<?php

use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Homepage Editor — ExchoSoft')] class extends Component
{
    // ── Active Tab ───────────────────────────────────────────────────────────
    public string $activeTab = 'hero';

    // ── Hero ─────────────────────────────────────────────────────────────────
    public string $home_hero_tag               = '';
    public string $home_hero_title             = '';
    public string $home_hero_subtitle          = '';
    public string $home_hero_btn_primary_label  = '';
    public string $home_hero_btn_secondary_label = '';

    // ── Stats (visual rows) ───────────────────────────────────────────────────
    public array $stats = [];

    // ── About ─────────────────────────────────────────────────────────────────
    public string $home_about_tag     = '';
    public string $home_about_title   = '';
    public string $home_about_content = '';
    public array  $about_cards        = [];

    // ── Products Section ──────────────────────────────────────────────────────
    public string $home_products_tag   = '';
    public string $home_products_title = '';

    // ── Approach ──────────────────────────────────────────────────────────────
    public string $home_approach_tag   = '';
    public string $home_approach_title = '';
    public array  $approach_cards      = [];

    // ── Industries ────────────────────────────────────────────────────────────
    public string $home_industries_tag   = '';
    public string $home_industries_title = '';
    public array  $industry_cards        = [];

    // ── Why Us ────────────────────────────────────────────────────────────────
    public string $home_why_tag   = '';
    public string $home_why_title = '';
    public array  $why_items      = [];

    // ── Trust ─────────────────────────────────────────────────────────────────
    public string $home_trust_tag      = '';
    public string $home_trust_title    = '';
    public string $home_trust_subtitle = '';
    public array  $trust_clients       = [];

    // ── CTA ───────────────────────────────────────────────────────────────────
    public string $home_cta_title     = '';
    public string $home_cta_subtitle  = '';
    public string $home_cta_btn       = '';
    public string $home_cta_email_note = '';

    // ── Demo CTA ──────────────────────────────────────────────────────────────
    public string $home_demo_cta_title    = '';
    public string $home_demo_cta_subtitle = '';

    // ── Available icons for approach cards ────────────────────────────────────
    public array $availableIcons = [
        'grid' => 'Grid / Modules',
        'offline' => 'Offline / Cloud',
        'data' => 'Data / Database',
        'lan' => 'LAN / Network',
        'shield' => 'Shield / Security',
        'partner' => 'Partner / Handshake',
        'chart' => 'Chart / Analytics',
        'code' => 'Code / Dev',
        'mobile' => 'Mobile',
        'settings' => 'Settings',
        'globe' => 'Globe / Web',
        'people' => 'People / Team',
    ];

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

        // Plain text fields
        foreach ([
            'home_hero_tag','home_hero_title','home_hero_subtitle',
            'home_hero_btn_primary_label','home_hero_btn_secondary_label',
            'home_about_tag','home_about_title','home_about_content',
            'home_products_tag','home_products_title',
            'home_approach_tag','home_approach_title',
            'home_industries_tag','home_industries_title',
            'home_why_tag','home_why_title',
            'home_trust_tag','home_trust_title','home_trust_subtitle',
            'home_cta_title','home_cta_subtitle','home_cta_btn','home_cta_email_note',
            'home_demo_cta_title','home_demo_cta_subtitle',
        ] as $key) {
            if (isset($settings[$key])) {
                $this->$key = $settings[$key];
            }
        }

        // JSON → array fields
        $this->stats         = $this->decodeJsonSetting($settings['home_stats'] ?? '');
        $this->about_cards   = $this->decodeJsonSetting($settings['home_about_cards'] ?? '');
        $this->approach_cards = $this->decodeJsonSetting($settings['home_approach_cards'] ?? '');
        $this->industry_cards = $this->decodeJsonSetting($settings['home_industries_cards'] ?? '');
        $this->why_items      = $this->decodeJsonSetting($settings['home_why_items'] ?? '');
        $this->trust_clients  = $this->decodeJsonSetting($settings['home_trust_clients'] ?? '');
    }

    protected function decodeJsonSetting(string $value): array
    {
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        return (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
    }

    // ── Stats actions ──────────────────────────────────────────────────────────
    public function addStat(): void
    {
        $this->stats[] = ['num' => '', 'label' => ''];
    }
    public function removeStat(int $i): void
    {
        array_splice($this->stats, $i, 1);
        $this->stats = array_values($this->stats);
    }
    public function moveStatUp(int $i): void
    {
        if ($i > 0) [$this->stats[$i - 1], $this->stats[$i]] = [$this->stats[$i], $this->stats[$i - 1]];
    }
    public function moveStatDown(int $i): void
    {
        if ($i < count($this->stats) - 1) [$this->stats[$i], $this->stats[$i + 1]] = [$this->stats[$i + 1], $this->stats[$i]];
    }

    // ── About cards actions ────────────────────────────────────────────────────
    public function addAboutCard(): void
    {
        $this->about_cards[] = ['title' => '', 'body' => ''];
    }
    public function removeAboutCard(int $i): void
    {
        array_splice($this->about_cards, $i, 1);
        $this->about_cards = array_values($this->about_cards);
    }

    // ── Approach cards actions ─────────────────────────────────────────────────
    public function addApproachCard(): void
    {
        $this->approach_cards[] = ['icon' => 'grid', 'title' => '', 'body' => ''];
    }
    public function removeApproachCard(int $i): void
    {
        array_splice($this->approach_cards, $i, 1);
        $this->approach_cards = array_values($this->approach_cards);
    }

    // ── Industry cards actions ─────────────────────────────────────────────────
    public function addIndustryCard(): void
    {
        $this->industry_cards[] = ['title' => '', 'body' => ''];
    }
    public function removeIndustryCard(int $i): void
    {
        array_splice($this->industry_cards, $i, 1);
        $this->industry_cards = array_values($this->industry_cards);
    }

    // ── Why Us items actions ───────────────────────────────────────────────────
    public function addWhyItem(): void
    {
        $this->why_items[] = ['title' => '', 'body' => ''];
    }
    public function removeWhyItem(int $i): void
    {
        array_splice($this->why_items, $i, 1);
        $this->why_items = array_values($this->why_items);
    }

    // ── Trust clients actions ──────────────────────────────────────────────────
    public function addTrustClient(): void
    {
        $this->trust_clients[] = ['name' => '', 'industry' => ''];
    }
    public function removeTrustClient(int $i): void
    {
        array_splice($this->trust_clients, $i, 1);
        $this->trust_clients = array_values($this->trust_clients);
    }

    // ── Save ─────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $settings = [
            // Hero
            ['key' => 'home_hero_tag',                  'value' => $this->home_hero_tag,                  'type' => 'text',  'group' => 'homepage', 'label' => 'Hero Tag'],
            ['key' => 'home_hero_title',                'value' => $this->home_hero_title,                'type' => 'text',  'group' => 'homepage', 'label' => 'Hero Title'],
            ['key' => 'home_hero_subtitle',             'value' => $this->home_hero_subtitle,             'type' => 'text',  'group' => 'homepage', 'label' => 'Hero Subtitle'],
            ['key' => 'home_hero_btn_primary_label',    'value' => $this->home_hero_btn_primary_label,    'type' => 'text',  'group' => 'homepage', 'label' => 'Hero Primary Button'],
            ['key' => 'home_hero_btn_secondary_label',  'value' => $this->home_hero_btn_secondary_label,  'type' => 'text',  'group' => 'homepage', 'label' => 'Hero Secondary Button'],
            // Stats
            ['key' => 'home_stats',         'value' => json_encode(array_values($this->stats)),          'type' => 'json', 'group' => 'homepage', 'label' => 'Stats'],
            // About
            ['key' => 'home_about_tag',     'value' => $this->home_about_tag,     'type' => 'text',     'group' => 'homepage', 'label' => 'About Tag'],
            ['key' => 'home_about_title',   'value' => $this->home_about_title,   'type' => 'text',     'group' => 'homepage', 'label' => 'About Title'],
            ['key' => 'home_about_content', 'value' => $this->home_about_content, 'type' => 'markdown', 'group' => 'homepage', 'label' => 'About Content'],
            ['key' => 'home_about_cards',   'value' => json_encode(array_values($this->about_cards)),   'type' => 'json', 'group' => 'homepage', 'label' => 'About Cards'],
            // Products
            ['key' => 'home_products_tag',   'value' => $this->home_products_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Products Tag'],
            ['key' => 'home_products_title', 'value' => $this->home_products_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Products Title'],
            // Approach
            ['key' => 'home_approach_tag',   'value' => $this->home_approach_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Tag'],
            ['key' => 'home_approach_title', 'value' => $this->home_approach_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Approach Title'],
            ['key' => 'home_approach_cards', 'value' => json_encode(array_values($this->approach_cards)), 'type' => 'json', 'group' => 'homepage', 'label' => 'Approach Cards'],
            // Industries
            ['key' => 'home_industries_tag',   'value' => $this->home_industries_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Tag'],
            ['key' => 'home_industries_title', 'value' => $this->home_industries_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Industries Title'],
            ['key' => 'home_industries_cards', 'value' => json_encode(array_values($this->industry_cards)), 'type' => 'json', 'group' => 'homepage', 'label' => 'Industries Cards'],
            // Why Us
            ['key' => 'home_why_tag',   'value' => $this->home_why_tag,   'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Tag'],
            ['key' => 'home_why_title', 'value' => $this->home_why_title, 'type' => 'text', 'group' => 'homepage', 'label' => 'Why Us Title'],
            ['key' => 'home_why_items', 'value' => json_encode(array_values($this->why_items)), 'type' => 'json', 'group' => 'homepage', 'label' => 'Why Us Items'],
            // Trust
            ['key' => 'home_trust_tag',      'value' => $this->home_trust_tag,      'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Tag'],
            ['key' => 'home_trust_title',    'value' => $this->home_trust_title,    'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Title'],
            ['key' => 'home_trust_subtitle', 'value' => $this->home_trust_subtitle, 'type' => 'text', 'group' => 'homepage', 'label' => 'Trust Subtitle'],
            ['key' => 'home_trust_clients',  'value' => json_encode(array_values($this->trust_clients)), 'type' => 'json', 'group' => 'homepage', 'label' => 'Trust Clients'],
            // CTA
            ['key' => 'home_cta_title',      'value' => $this->home_cta_title,      'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Title'],
            ['key' => 'home_cta_subtitle',   'value' => $this->home_cta_subtitle,   'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Subtitle'],
            ['key' => 'home_cta_btn',        'value' => $this->home_cta_btn,        'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Button'],
            ['key' => 'home_cta_email_note', 'value' => $this->home_cta_email_note, 'type' => 'text', 'group' => 'homepage', 'label' => 'CTA Email Note'],
            // Demo CTA
            ['key' => 'home_demo_cta_title',    'value' => $this->home_demo_cta_title,    'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Title'],
            ['key' => 'home_demo_cta_subtitle', 'value' => $this->home_demo_cta_subtitle, 'type' => 'text', 'group' => 'homepage', 'label' => 'Demo CTA Subtitle'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        session()->flash('success', 'Homepage content saved successfully!');
    }

   
}; ?>

<div>
    <x-slot:heading>Homepage Editor</x-slot:heading>

    <style>
        /* ── Tabs ── */
        .he-tab { padding: 0.55rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; }
        .he-tab.active { background: #0891b2; color: white; }
        .he-tab:not(.active) { background: #f1f5f9; color: #64748b; }
        .he-tab:not(.active):hover { background: #e2e8f0; color: #334155; }

        /* ── Cards / layout ── */
        .he-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .he-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; margin-bottom: 0.3rem; display: block; }
        .he-hint { font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem; }

        /* ── Form controls ── */
        .he-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: #1e293b; transition: border-color 0.15s; background: white; }
        .he-input:focus { outline: none; border-color: #0891b2; box-shadow: 0 0 0 3px rgba(8,145,178,0.08); }
        .he-textarea { resize: vertical; min-height: 72px; }
        .he-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.6rem center; background-size: 14px; padding-right: 2rem; }

        /* ── Row items (stats, cards) ── */
        .he-row-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1rem 0.75rem; position: relative; }
        .he-row-item:hover { border-color: #cbd5e1; }
        .he-row-handle { color: #cbd5e1; cursor: grab; }
        .he-row-handle:active { cursor: grabbing; }

        /* ── Buttons ── */
        .he-btn-add { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: 1.5px dashed #cbd5e1; color: #64748b; background: transparent; cursor: pointer; transition: all 0.15s; }
        .he-btn-add:hover { border-color: #0891b2; color: #0891b2; background: #f0f9ff; }
        .he-btn-remove { width: 24px; height: 24px; border-radius: 6px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.15s; }
        .he-btn-remove:hover { background: #fca5a5; }
        .he-btn-move { width: 22px; height: 22px; border-radius: 5px; border: 1px solid #e2e8f0; background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s; }
        .he-btn-move:hover { background: #f1f5f9; color: #334155; }

        /* ── Section header ── */
        .he-section-head { font-size: 0.8rem; font-weight: 700; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1.25rem; }

        /* ── Icon picker chips ── */
        .icon-chip { padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600; border: 1.5px solid transparent; cursor: pointer; transition: all 0.12s; white-space: nowrap; }
        .icon-chip.selected { border-color: #0891b2; background: #e0f2fe; color: #0369a1; }
        .icon-chip:not(.selected) { border-color: #e2e8f0; color: #64748b; background: white; }
        .icon-chip:not(.selected):hover { border-color: #cbd5e1; color: #334155; }

        /* ── Live preview badge ── */
        .stat-preview { display: inline-flex; flex-direction: column; align-items: center; background: #ecfeff; border: 1px solid #a5f3fc; border-radius: 10px; padding: 0.5rem 1rem; min-width: 80px; }
        .stat-preview-num { font-size: 1.1rem; font-weight: 800; color: #0891b2; line-height: 1; }
        .stat-preview-lbl { font-size: 0.65rem; color: #0e7490; margin-top: 2px; }
    </style>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Top bar --}}
        <div class="flex items-center justify-between">
            <p class="text-slate-500 text-sm">Edit all homepage sections. No JSON — just fill in the fields.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Preview
                </a>
                <button wire:click="save"
                        class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </div>

        {{-- Tab Nav --}}
        <div class="flex flex-wrap gap-1.5 bg-slate-50 p-1.5 rounded-xl border border-slate-100">
            @foreach([
                ['hero',       '🎯 Hero'],
                ['stats',      '📊 Stats Bar'],
                ['about',      '🏢 About'],
                ['products',   '🛍️ Products'],
                ['approach',   '🔧 Approach'],
                ['industries', '🏭 Industries'],
                ['why',        '⭐ Why Us'],
                ['trust',      '🤝 Trust'],
                ['cta',        '📣 CTA'],
            ] as [$tab, $label])
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    class="he-tab {{ $activeTab === $tab ? 'active' : '' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ════════════════════════ HERO ════════════════════════ --}}
        @if($activeTab === 'hero')
        <div class="he-card space-y-4">
            <div class="he-section-head">🎯 Hero Section — the very first thing visitors see</div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="he-label">Tag / Badge text</label>
                    <input wire:model="home_hero_tag" type="text" class="he-input" placeholder="Ghana-Based · Africa · Caribbean · Diaspora">
                </div>
                <div class="sm:col-span-2">
                    <label class="he-label">Main Headline</label>
                    <input wire:model="home_hero_title" type="text" class="he-input" placeholder="Technology Consultancy Built on **Real-World** Experience">
                    <p class="he-hint">Wrap words in **double asterisks** to highlight them in cyan on the page.</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="he-label">Subtitle / Description</label>
                    <textarea wire:model="home_hero_subtitle" rows="3" class="he-input he-textarea" placeholder="We're a software development and technology consultancy..."></textarea>
                </div>
                <div>
                    <label class="he-label">Primary Button Label</label>
                    <input wire:model="home_hero_btn_primary_label" type="text" class="he-input" placeholder="Talk to Us">
                </div>
                <div>
                    <label class="he-label">Secondary Button Label</label>
                    <input wire:model="home_hero_btn_secondary_label" type="text" class="he-input" placeholder="Our Products">
                </div>
            </div>

            {{-- Live preview strip --}}
            @if($home_hero_title || $home_hero_subtitle)
            <div class="mt-2 bg-slate-900 rounded-xl p-5 text-white">
                <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Live Preview</div>
                @if($home_hero_tag)
                    <div class="inline-block mb-3 text-xs font-bold uppercase tracking-widest bg-cyan-900/50 text-cyan-400 border border-cyan-800 rounded-full px-3 py-0.5">{{ $home_hero_tag }}</div>
                @endif
                <h2 class="text-xl font-bold leading-tight mb-2">
                    {!! preg_replace('/\*\*(.+?)\*\*/', '<span style="color:#22d3ee">$1</span>', e($home_hero_title)) !!}
                </h2>
                @if($home_hero_subtitle)
                    <p class="text-slate-400 text-sm leading-relaxed max-w-lg">{{ $home_hero_subtitle }}</p>
                @endif
                <div class="flex gap-3 mt-4">
                    @if($home_hero_btn_primary_label)
                        <span class="text-xs font-semibold bg-cyan-600 text-white rounded-lg px-4 py-1.5">{{ $home_hero_btn_primary_label }}</span>
                    @endif
                    @if($home_hero_btn_secondary_label)
                        <span class="text-xs font-semibold border border-cyan-700 text-cyan-400 rounded-lg px-4 py-1.5">{{ $home_hero_btn_secondary_label }}</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ════════════════════════ STATS ════════════════════════ --}}
        @if($activeTab === 'stats')
        <div class="he-card space-y-4">
            <div class="he-section-head">📊 Stats Bar — the numbers strip below the hero</div>

            <div class="space-y-2">
                @foreach($stats as $i => $stat)
                <div class="he-row-item">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold text-slate-400 w-5 text-center">{{ $i + 1 }}</span>
                        <div class="flex gap-1 ml-auto">
                            <button wire:click="moveStatUp({{ $i }})" class="he-btn-move" title="Move up">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button wire:click="moveStatDown({{ $i }})" class="he-btn-move" title="Move down">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button wire:click="removeStat({{ $i }})" class="he-btn-remove" title="Remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 items-start">
                        <div>
                            <label class="he-label">Number / Value</label>
                            <input wire:model="stats.{{ $i }}.num" type="text" class="he-input" placeholder="10+">
                        </div>
                        <div>
                            <label class="he-label">Label</label>
                            <input wire:model="stats.{{ $i }}.label" type="text" class="he-input" placeholder="Industries served">
                        </div>
                    </div>
                    {{-- Mini preview --}}
                    @if(!empty($stat['num']) || !empty($stat['label']))
                    <div class="mt-2 flex justify-end">
                        <div class="stat-preview">
                            <span class="stat-preview-num">{{ $stat['num'] ?? '–' }}</span>
                            <span class="stat-preview-lbl">{{ $stat['label'] ?? '' }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <button wire:click="addStat" class="he-btn-add">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Stat
            </button>

            @if(count($stats))
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Full Preview</p>
                <div class="flex flex-wrap gap-3">
                    @foreach($stats as $stat)
                    @if(!empty($stat['num']) || !empty($stat['label']))
                    <div class="stat-preview">
                        <span class="stat-preview-num">{{ $stat['num'] ?? '' }}</span>
                        <span class="stat-preview-lbl">{{ $stat['label'] ?? '' }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ════════════════════════ ABOUT ════════════════════════ --}}
        @if($activeTab === 'about')
        <div class="he-card space-y-4">
            <div class="he-section-head">🏢 About / "Who We Are" Section</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_about_tag" type="text" class="he-input" placeholder="Who We Are">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_about_title" type="text" class="he-input" placeholder="Built for the Conditions We Work In">
                </div>
            </div>
            <div>
                <label class="he-label">Main Content</label>
                <textarea wire:model="home_about_content" rows="5" class="he-input he-textarea" placeholder="Write your about section text here. Use **bold** and _italic_ for emphasis..."></textarea>
                <p class="he-hint">Supports Markdown: **bold**, _italic_, ## Heading, - bullet list</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="he-label mb-0">Reality / Highlight Cards <span class="normal-case font-normal text-slate-400">(appear on the right side)</span></label>
                    <button wire:click="addAboutCard" class="he-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Card
                    </button>
                </div>
                <div class="space-y-2">
                    @forelse($about_cards as $i => $card)
                    <div class="he-row-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-400">Card {{ $i + 1 }}</span>
                            <button wire:click="removeAboutCard({{ $i }})" class="he-btn-remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="he-label">Card Title</label>
                                <input wire:model="about_cards.{{ $i }}.title" type="text" class="he-input" placeholder="Designed for Power Cuts">
                            </div>
                            <div>
                                <label class="he-label">Card Body</label>
                                <textarea wire:model="about_cards.{{ $i }}.body" rows="2" class="he-input he-textarea" placeholder="Short description of this card..."></textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2">No cards yet. Click "Add Card" to add one.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════ PRODUCTS ════════════════════════ --}}
        @if($activeTab === 'products')
        <div class="he-card space-y-4">
            <div class="he-section-head">🛍️ Featured Products Section heading</div>
            <div class="rounded-xl bg-cyan-50 border border-cyan-100 p-4 text-sm text-cyan-800">
                <p class="font-semibold mb-1">Products are pulled automatically</p>
                <p class="text-cyan-700 text-xs leading-relaxed">Featured & published products from <a href="{{ route('admin.shop-products') }}" wire:navigate class="underline font-semibold">Shop Products</a> appear automatically in this section. Only set the section heading text here.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_products_tag" type="text" class="he-input" placeholder="Our Software">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_products_title" type="text" class="he-input" placeholder="Products Built for African Businesses">
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════ APPROACH ════════════════════════ --}}
        @if($activeTab === 'approach')
        <div class="he-card space-y-4">
            <div class="he-section-head">🔧 Our Approach Section</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_approach_tag" type="text" class="he-input" placeholder="Our Approach">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_approach_title" type="text" class="he-input" placeholder="What We've Learned From Building Real Software">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="he-label mb-0">Approach Cards</label>
                    <button wire:click="addApproachCard" class="he-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Card
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse($approach_cards as $i => $card)
                    <div class="he-row-item">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-400">Card {{ $i + 1 }}</span>
                            <button wire:click="removeApproachCard({{ $i }})" class="he-btn-remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="he-label">Icon</label>
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @foreach($availableIcons as $iconKey => $iconLabel)
                                    <button type="button"
                                            wire:click="$set('approach_cards.{{ $i }}.icon', '{{ $iconKey }}')"
                                            class="icon-chip {{ ($card['icon'] ?? '') === $iconKey ? 'selected' : '' }}">
                                        {{ $iconLabel }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="he-label">Card Title</label>
                                    <input wire:model="approach_cards.{{ $i }}.title" type="text" class="he-input" placeholder="Offline-First by Default">
                                </div>
                                <div>
                                    <label class="he-label">Card Body</label>
                                    <input wire:model="approach_cards.{{ $i }}.body" type="text" class="he-input" placeholder="Short description...">
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2">No cards yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════ INDUSTRIES ════════════════════════ --}}
        @if($activeTab === 'industries')
        <div class="he-card space-y-4">
            <div class="he-section-head">🏭 Industries We Serve Section</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_industries_tag" type="text" class="he-input" placeholder="Experience">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_industries_title" type="text" class="he-input" placeholder="Industries We've Served">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="he-label mb-0">Industry Cards</label>
                    <button wire:click="addIndustryCard" class="he-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Industry
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @forelse($industry_cards as $i => $card)
                    <div class="he-row-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-400">Industry {{ $i + 1 }}</span>
                            <button wire:click="removeIndustryCard({{ $i }})" class="he-btn-remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="he-label">Industry Name</label>
                                <input wire:model="industry_cards.{{ $i }}.title" type="text" class="he-input" placeholder="Healthcare & Medical">
                            </div>
                            <div>
                                <label class="he-label">Short Description</label>
                                <textarea wire:model="industry_cards.{{ $i }}.body" rows="2" class="he-input he-textarea" placeholder="Patient management, inventory..."></textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2 col-span-2">No industries yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════ WHY US ════════════════════════ --}}
        @if($activeTab === 'why')
        <div class="he-card space-y-4">
            <div class="he-section-head">⭐ Why Us Section</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_why_tag" type="text" class="he-input" placeholder="Why Exchosoft">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_why_title" type="text" class="he-input" placeholder="The Exchosoft Difference">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="he-label mb-0">Why-Us Points</label>
                    <button wire:click="addWhyItem" class="he-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Point
                    </button>
                </div>
                <div class="space-y-2">
                    @forelse($why_items as $i => $item)
                    <div class="he-row-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-cyan-600 bg-cyan-50 border border-cyan-100 rounded-full px-2 py-0.5">{{ $i + 1 }}</span>
                            <button wire:click="removeWhyItem({{ $i }})" class="he-btn-remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-5 gap-3">
                            <div class="col-span-2">
                                <label class="he-label">Point Title</label>
                                <input wire:model="why_items.{{ $i }}.title" type="text" class="he-input" placeholder="We Know the Local Context">
                            </div>
                            <div class="col-span-3">
                                <label class="he-label">Description</label>
                                <textarea wire:model="why_items.{{ $i }}.body" rows="2" class="he-input he-textarea" placeholder="Why this matters to your customers..."></textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2">No points yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════ TRUST ════════════════════════ --}}
        @if($activeTab === 'trust')
        <div class="he-card space-y-4">
            <div class="he-section-head">🤝 Client Trust / Social Proof Section</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_trust_tag" type="text" class="he-input" placeholder="Trusted By">
                </div>
                <div>
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_trust_title" type="text" class="he-input" placeholder="Companies That Trust Us">
                </div>
                <div>
                    <label class="he-label">Subtitle</label>
                    <input wire:model="home_trust_subtitle" type="text" class="he-input" placeholder="From Ghana to the diaspora">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="he-label mb-0">Client / Company Names</label>
                    <button wire:click="addTrustClient" class="he-btn-add">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Client
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @forelse($trust_clients as $i => $client)
                    <div class="he-row-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-400">Client {{ $i + 1 }}</span>
                            <button wire:click="removeTrustClient({{ $i }})" class="he-btn-remove">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="he-label">Company / Client Name</label>
                                <input wire:model="trust_clients.{{ $i }}.name" type="text" class="he-input" placeholder="Acme Corp">
                            </div>
                            <div>
                                <label class="he-label">Industry (optional)</label>
                                <input wire:model="trust_clients.{{ $i }}.industry" type="text" class="he-input" placeholder="Healthcare">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic py-2 col-span-2">No clients listed yet.</p>
                    @endforelse
                </div>
            </div>

            @if(count($trust_clients) > 0)
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Preview</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($trust_clients as $client)
                    @if(!empty($client['name']))
                    <span class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700">
                        {{ $client['name'] }}
                        @if(!empty($client['industry']))
                            <span class="text-slate-400 text-xs ml-1">· {{ $client['industry'] }}</span>
                        @endif
                    </span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ════════════════════════ CTA ════════════════════════ --}}
        @if($activeTab === 'cta')
        <div class="space-y-4">
            {{-- Main CTA --}}
            <div class="he-card space-y-4">
                <div class="he-section-head">📣 Main Call-to-Action (bottom of page)</div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="he-label">CTA Headline</label>
                        <input wire:model="home_cta_title" type="text" class="he-input" placeholder="Ready to Transform Your Business?">
                    </div>
                    <div class="col-span-2">
                        <label class="he-label">CTA Subtitle</label>
                        <textarea wire:model="home_cta_subtitle" rows="2" class="he-input he-textarea" placeholder="Join hundreds of businesses..."></textarea>
                    </div>
                    <div>
                        <label class="he-label">Button Label</label>
                        <input wire:model="home_cta_btn" type="text" class="he-input" placeholder="Start Free Trial">
                    </div>
                    <div>
                        <label class="he-label">Small Note Under Button</label>
                        <input wire:model="home_cta_email_note" type="text" class="he-input" placeholder="No credit card required · Free 30-day trial">
                    </div>
                </div>

                @if($home_cta_title)
                <div class="bg-slate-900 rounded-xl p-6 text-center">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Preview</p>
                    <h3 class="text-lg font-bold text-white mb-2">{{ $home_cta_title }}</h3>
                    @if($home_cta_subtitle)
                        <p class="text-slate-400 text-sm mb-4">{{ $home_cta_subtitle }}</p>
                    @endif
                    @if($home_cta_btn)
                        <div class="inline-block bg-cyan-600 text-white text-sm font-semibold rounded-lg px-6 py-2">{{ $home_cta_btn }}</div>
                    @endif
                    @if($home_cta_email_note)
                        <p class="text-slate-600 text-xs mt-2">{{ $home_cta_email_note }}</p>
                    @endif
                </div>
                @endif
            </div>

            {{-- Demo CTA --}}
            <div class="he-card space-y-4">
                <div class="he-section-head">🗓️ Book a Demo CTA strip</div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="he-label">Demo CTA Title</label>
                        <input wire:model="home_demo_cta_title" type="text" class="he-input" placeholder="See It in Action">
                    </div>
                    <div>
                        <label class="he-label">Demo CTA Subtitle</label>
                        <input wire:model="home_demo_cta_subtitle" type="text" class="he-input" placeholder="Book a personalised demo with our team">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Bottom Save bar --}}
        <div class="flex justify-end pt-2 border-t border-slate-100">
            <button wire:click="save"
                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save All Changes
            </button>
        </div>

    </div>
</div>
