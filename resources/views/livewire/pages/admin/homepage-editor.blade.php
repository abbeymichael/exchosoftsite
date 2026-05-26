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
<x-slot:heading>
    <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100">
            <svg class="h-4 w-4 text-cyan-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        Homepage Editor
    </div>
</x-slot:heading>

<style>
/* ── Tab nav ── */
.he-tabs { display:flex; flex-wrap:wrap; gap:4px; }
.he-tab {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 14px; border-radius:8px; font-size:0.8rem; font-weight:600;
    cursor:pointer; border:none; transition:all 0.15s; white-space:nowrap;
    background:#f1f5f9; color:#64748b;
}
.he-tab:hover { background:#e2e8f0; color:#334155; }
.he-tab.active { background:#0e7490; color:#fff; box-shadow:0 2px 8px rgba(14,116,144,0.25); }
.he-tab-dot { width:6px; height:6px; border-radius:50%; background:currentColor; opacity:0.6; }

/* ── Cards ── */
.he-card {
    background:#fff; border-radius:14px; border:1px solid #e2e8f0;
    overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.04);
}
.he-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;
    background:linear-gradient(135deg,#f8fafc 0%,#fff 100%);
}
.he-card-title { font-size:0.9rem; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
.he-card-title-icon { font-size:1rem; }
.he-card-body { padding:1.25rem; display:flex; flex-direction:column; gap:1rem; }

/* ── Field groups ── */
.he-field { display:flex; flex-direction:column; gap:5px; }
.he-field-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.he-label {
    font-size:0.7rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.07em; color:#94a3b8; display:flex; align-items:center; gap:6px;
}
.he-label-badge {
    font-size:0.6rem; font-weight:700; padding:1px 6px; border-radius:100px;
    text-transform:none; letter-spacing:0;
}
.badge-md { background:#fef3c7; color:#92400e; }
.badge-json { background:#ede9fe; color:#5b21b6; }
.badge-text { background:#e0f2fe; color:#0369a1; }

/* ── Inputs ── */
.he-input, .he-textarea, .he-select {
    width:100%; border:1.5px solid #e2e8f0; border-radius:9px;
    padding:0.55rem 0.85rem; font-size:0.875rem; color:#1e293b;
    font-family:inherit; background:#fff;
    transition:border-color 0.15s, box-shadow 0.15s;
}
.he-input:focus, .he-textarea:focus, .he-select:focus {
    outline:none; border-color:#0891b2;
    box-shadow:0 0 0 3px rgba(8,145,178,0.1);
}
.he-textarea { resize:vertical; min-height:80px; }
.he-mono { font-family:'SFMono-Regular',Menlo,monospace; font-size:0.78rem; }
.he-hint { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
.he-hint code { background:#f1f5f9; padding:1px 5px; border-radius:4px; font-size:0.7rem; color:#475569; }
.he-error { font-size:0.75rem; color:#dc2626; margin-top:3px; display:flex; align-items:center; gap:4px; }

/* ── Preview blocks ── */
.he-preview {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:9px; padding:1rem;
    font-size:0.85rem; color:#475569;
}
.he-preview-label { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94a3b8; margin-bottom:0.5rem; }
.he-stat-preview { display:flex; flex-wrap:wrap; gap:0.75rem; }
.he-stat-pill { background:#ecfeff; border:1px solid #a5f3fc; border-radius:10px; padding:0.5rem 1rem; text-align:center; }
.he-stat-num { font-size:1.1rem; font-weight:800; color:#0e7490; line-height:1; }
.he-stat-lbl { font-size:0.65rem; color:#64748b; margin-top:2px; }
.he-client-pill { background:#fff; border:1px solid #e2e8f0; border-radius:100px; padding:4px 12px; font-size:0.8rem; color:#475569; }

/* ── Save button ── */
.he-save-btn {
    display:inline-flex; align-items:center; gap:8px;
    background:#0e7490; color:#fff; padding:9px 22px; border-radius:10px;
    font-size:0.875rem; font-weight:700; border:none; cursor:pointer;
    transition:background 0.15s, transform 0.1s; box-shadow:0 2px 8px rgba(14,116,144,0.3);
}
.he-save-btn:hover { background:#0c6179; transform:translateY(-1px); }
.he-save-btn svg { width:15px; height:15px; }
.he-preview-btn {
    display:inline-flex; align-items:center; gap:7px;
    background:#fff; color:#475569; padding:9px 16px; border-radius:10px;
    font-size:0.875rem; font-weight:600; border:1.5px solid #e2e8f0; cursor:pointer;
    transition:border-color 0.15s, color 0.15s; text-decoration:none;
}
.he-preview-btn:hover { border-color:#0891b2; color:#0e7490; }
.he-preview-btn svg { width:14px; height:14px; }

/* ── Info box ── */
.he-info-box {
    background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px;
    padding:0.85rem 1rem; font-size:0.8rem; color:#0369a1;
    display:flex; gap:0.6rem; align-items:flex-start;
}
.he-info-box svg { width:16px; height:16px; flex-shrink:0; margin-top:1px; color:#0284c7; }

/* ── Success toast ── */
.he-toast {
    display:flex; align-items:center; gap:10px;
    background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px;
    padding:12px 16px; font-size:0.875rem; color:#166534;
}
.he-toast svg { width:18px; height:18px; color:#16a34a; flex-shrink:0; }

@media(max-width:768px) { .he-field-row { grid-template-columns:1fr; } }
</style>

<div class="space-y-5">

    {{-- Toast --}}
    @if(session('success'))
    <div class="he-toast">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Top action bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Edit homepage sections live. Changes are instantly reflected on the public site.</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="he-preview-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Preview Live
            </a>
            <button wire:click="save" class="he-save-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save All Changes
            </button>
        </div>
    </div>

    {{-- Tab navigation --}}
    <div class="he-tabs">
        @foreach([
            ['hero',       '🎯', 'Hero'],
            ['stats',      '📊', 'Stats'],
            ['about',      '🏢', 'About'],
            ['products',   '🛍️', 'Products'],
            ['approach',   '🔧', 'Approach'],
            ['industries', '🏭', 'Industries'],
            ['why',        '⭐', 'Why Us'],
            ['trust',      '🤝', 'Trust'],
            ['cta',        '📣', 'CTA'],
        ] as [$tab, $icon, $label])
        <button wire:click="$set('activeTab','{{ $tab }}')"
                class="he-tab {{ $activeTab === $tab ? 'active' : '' }}">
            {{ $icon }} {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ══════════ HERO ══════════ --}}
    @if($activeTab === 'hero')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🎯</span> Hero Section</div>
            <span class="text-xs text-slate-400">The first thing visitors see</span>
        </div>
        <div class="he-card-body">
            <div class="he-field">
                <label class="he-label">Tag Badge <span class="he-label-badge badge-text">text</span></label>
                <input wire:model="home_hero_tag" type="text" class="he-input" placeholder="Ghana-Based · Africa · Caribbean · Diaspora">
                <p class="he-hint">Small badge shown above the main title.</p>
            </div>
            <div class="he-field">
                <label class="he-label">Main Title <span class="he-label-badge badge-text">text</span></label>
                <input wire:model="home_hero_title" type="text" class="he-input" placeholder="Technology Consultancy Built on **Real-World** Experience">
                <p class="he-hint">Wrap words in <code>**double asterisks**</code> for cyan highlight accent.</p>
            </div>
            <div class="he-field">
                <label class="he-label">Sub-headline <span class="he-label-badge badge-text">text</span></label>
                <textarea wire:model="home_hero_subtitle" rows="3" class="he-textarea" placeholder="We're a software development and technology consultancy..."></textarea>
            </div>
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Primary Button</label>
                    <input wire:model="home_hero_btn_primary_label" type="text" class="he-input" placeholder="Talk to Us">
                </div>
                <div class="he-field">
                    <label class="he-label">Secondary Button</label>
                    <input wire:model="home_hero_btn_secondary_label" type="text" class="he-input" placeholder="Our Products">
                </div>
            </div>
            @if($home_hero_title)
            <div class="he-preview">
                <div class="he-preview-label">Title Preview</div>
                <div style="font-size:1.1rem;font-weight:800;color:#0f172a;line-height:1.2;">
                    {!! preg_replace('/\*\*(.+?)\*\*/', '<span style="color:#0e7490">$1</span>', e($home_hero_title)) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════ STATS ══════════ --}}
    @if($activeTab === 'stats')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">📊</span> Stats Bar</div>
            <span class="text-xs text-slate-400">Numbers shown below the hero</span>
        </div>
        <div class="he-card-body">
            <div class="he-info-box">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                JSON array of objects. Each object needs a <code class="font-mono text-xs bg-blue-50 px-1 rounded">"num"</code> and <code class="font-mono text-xs bg-blue-50 px-1 rounded">"label"</code> key.
            </div>
            <div class="he-field">
                <label class="he-label">Stats Array <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_stats" rows="8" class="he-textarea he-mono" placeholder='[{"num":"10+","label":"Industries served"},{"num":"3","label":"Continents"},{"num":"5+","label":"Years building"}]'></textarea>
                @error('home_stats') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
            @if($home_stats && count(json_decode($home_stats, true) ?? []) > 0)
            <div>
                <p class="he-preview-label">Preview</p>
                <div class="he-stat-preview">
                    @foreach(json_decode($home_stats, true) ?? [] as $stat)
                    <div class="he-stat-pill">
                        <div class="he-stat-num">{{ $stat['num'] ?? '' }}</div>
                        <div class="he-stat-lbl">{{ $stat['label'] ?? '' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════ ABOUT ══════════ --}}
    @if($activeTab === 'about')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🏢</span> About / Who We Are Section</div>
            <span class="text-xs text-slate-400">Rendered as formatted markdown on the homepage</span>
        </div>
        <div class="he-card-body">
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag <span class="he-label-badge badge-text">text</span></label>
                    <input wire:model="home_about_tag" type="text" class="he-input" placeholder="Who We Are">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title <span class="he-label-badge badge-text">text</span></label>
                    <input wire:model="home_about_title" type="text" class="he-input" placeholder="Built for the Conditions...">
                </div>
            </div>
            <div class="he-field">
                <label class="he-label">Main Content <span class="he-label-badge badge-md">Markdown</span></label>
                <div class="he-info-box" style="margin-bottom:0.5rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    This field renders as <strong>real Markdown</strong> on the site using league/commonmark. Use <code>**bold**</code>, <code>_italic_</code>, <code># Heading</code>, <code>- lists</code>.
                </div>
                <textarea wire:model="home_about_content" rows="8" class="he-textarea" placeholder="## About Exchosoft&#10;&#10;We are a Ghana-based technology consultancy...&#10;&#10;- Point one&#10;- Point two"></textarea>
            </div>
            <div class="he-field">
                <label class="he-label">Reality Cards <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_about_cards" rows="8" class="he-textarea he-mono" placeholder='[{"title":"Offline-First Design","body":"Systems built for unreliable power and internet"},...]'></textarea>
                <p class="he-hint">JSON array: <code>[{"title":"Card Title","body":"Card description"},...]</code></p>
                @error('home_about_cards') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════ PRODUCTS ══════════ --}}
    @if($activeTab === 'products')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🛍️</span> Featured Products Section</div>
        </div>
        <div class="he-card-body">
            <div class="he-info-box">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>Products are pulled <strong>dynamically from the database</strong>. Only <strong>Featured + Published</strong> products appear here.
                Manage which products appear via <a href="{{ route('admin.shop-products') }}" wire:navigate class="underline font-semibold">Shop Products →</a></div>
            </div>
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_products_tag" type="text" class="he-input" placeholder="Our Software">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_products_title" type="text" class="he-input" placeholder="Products Built for African Businesses">
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════ APPROACH ══════════ --}}
    @if($activeTab === 'approach')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🔧</span> Our Approach Section</div>
        </div>
        <div class="he-card-body">
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_approach_tag" type="text" class="he-input" placeholder="Our Approach">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_approach_title" type="text" class="he-input" placeholder="What We've Learned...">
                </div>
            </div>
            <div class="he-field">
                <label class="he-label">Approach Cards <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_approach_cards" rows="14" class="he-textarea he-mono" placeholder='[{"icon":"grid","title":"Card Title","body":"Description"},...]'></textarea>
                <p class="he-hint">JSON array: <code>[{"icon":"grid","title":"Card Title","body":"Description"},...]</code> — Icons: grid, offline, data, lan, shield, partner</p>
                @error('home_approach_cards') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════ INDUSTRIES ══════════ --}}
    @if($activeTab === 'industries')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🏭</span> Industries Section</div>
        </div>
        <div class="he-card-body">
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_industries_tag" type="text" class="he-input" placeholder="Experience">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_industries_title" type="text" class="he-input" placeholder="Industries We've Served">
                </div>
            </div>
            <div class="he-field">
                <label class="he-label">Industry Cards <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_industries_cards" rows="12" class="he-textarea he-mono" placeholder='[{"title":"Healthcare & Medical","body":"Description text"},...]'></textarea>
                <p class="he-hint">JSON array: <code>[{"title":"Healthcare &amp; Medical","body":"Description text"},...]</code></p>
                @error('home_industries_cards') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════ WHY US ══════════ --}}
    @if($activeTab === 'why')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">⭐</span> Why Us Section</div>
        </div>
        <div class="he-card-body">
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_why_tag" type="text" class="he-input" placeholder="Why Exchosoft">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_why_title" type="text" class="he-input" placeholder="The Exchosoft Difference">
                </div>
            </div>
            <div class="he-field">
                <label class="he-label">Why Us Items <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_why_items" rows="10" class="he-textarea he-mono" placeholder='[{"title":"Point Title","body":"Description"},...]'></textarea>
                <p class="he-hint">JSON array: <code>[{"title":"Point Title","body":"Description"},...]</code></p>
                @error('home_why_items') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════ TRUST ══════════ --}}
    @if($activeTab === 'trust')
    <div class="he-card">
        <div class="he-card-header">
            <div class="he-card-title"><span class="he-card-title-icon">🤝</span> Trust & Clients Section</div>
        </div>
        <div class="he-card-body">
            <div class="he-field-row">
                <div class="he-field">
                    <label class="he-label">Section Tag</label>
                    <input wire:model="home_trust_tag" type="text" class="he-input" placeholder="Trusted By">
                </div>
                <div class="he-field">
                    <label class="he-label">Section Title</label>
                    <input wire:model="home_trust_title" type="text" class="he-input" placeholder="Organisations That Trust Exchosoft">
                </div>
            </div>
            <div class="he-field">
                <label class="he-label">Section Subtitle</label>
                <input wire:model="home_trust_subtitle" type="text" class="he-input" placeholder="We've delivered solutions across...">
            </div>
            <div class="he-field">
                <label class="he-label">Client Pills <span class="he-label-badge badge-json">JSON</span></label>
                <textarea wire:model="home_trust_clients" rows="5" class="he-textarea he-mono" placeholder='["Healthcare Facilities","Church Networks","Laundry Businesses"]'></textarea>
                <p class="he-hint">JSON string array: <code>["Name 1","Name 2",...]</code></p>
                @error('home_trust_clients') <p class="he-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>{{ $message }}</p> @enderror
            </div>
            @if($home_trust_clients && count(json_decode($home_trust_clients, true) ?? []) > 0)
            <div>
                <p class="he-preview-label">Client Pills Preview</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(json_decode($home_trust_clients, true) ?? [] as $client)
                    <span class="he-client-pill">{{ $client }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════ CTA ══════════ --}}
    @if($activeTab === 'cta')
    <div class="space-y-4">
        <div class="he-card">
            <div class="he-card-header">
                <div class="he-card-title"><span class="he-card-title-icon">🔔</span> Main CTA (Cyan Strip)</div>
                <span class="text-xs text-slate-400">Large call-to-action near page bottom</span>
            </div>
            <div class="he-card-body">
                <div class="he-field">
                    <label class="he-label">Title</label>
                    <input wire:model="home_cta_title" type="text" class="he-input" placeholder="Ready to Build Something That Actually Works?">
                </div>
                <div class="he-field">
                    <label class="he-label">Subtitle</label>
                    <textarea wire:model="home_cta_subtitle" rows="2" class="he-textarea" placeholder="Tell us what you need..."></textarea>
                </div>
                <div class="he-field-row">
                    <div class="he-field">
                        <label class="he-label">Button Label</label>
                        <input wire:model="home_cta_btn" type="text" class="he-input" placeholder="Start a Conversation">
                    </div>
                    <div class="he-field">
                        <label class="he-label">Email Note (below button)</label>
                        <input wire:model="home_cta_email_note" type="text" class="he-input" placeholder="Or email us at hello@exchosoft.com">
                    </div>
                </div>
            </div>
        </div>
        <div class="he-card">
            <div class="he-card-header">
                <div class="he-card-title"><span class="he-card-title-icon">📅</span> Demo CTA (Navy Strip)</div>
                <span class="text-xs text-slate-400">Dark demo booking section</span>
            </div>
            <div class="he-card-body">
                <div class="he-field">
                    <label class="he-label">Title</label>
                    <input wire:model="home_demo_cta_title" type="text" class="he-input" placeholder="See Our Software in Action">
                </div>
                <div class="he-field">
                    <label class="he-label">Subtitle</label>
                    <textarea wire:model="home_demo_cta_subtitle" rows="2" class="he-textarea" placeholder="Book a live demonstration..."></textarea>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Bottom Save --}}
    <div class="flex justify-end pt-1">
        <button wire:click="save" class="he-save-btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save All Homepage Changes
        </button>
    </div>

</div>
</div>
