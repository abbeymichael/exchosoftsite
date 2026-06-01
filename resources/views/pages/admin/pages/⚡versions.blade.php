<?php

use App\Models\Page;
use App\Models\PageVersion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Version History — ExchoSoft')] class extends Component
{
    public string      $key     = '';
    public ?Page       $page    = null;
    public ?int        $previewId = null;  // version being previewed
    public bool        $confirmRestore = false;
    public ?string     $restoreId = null;  // uuid of version to restore

    // ── Mount ─────────────────────────────────────────────────────────────────
    public function mount(string $key): void
    {
        $this->key  = $key;
        $this->page = Page::where('key', $key)->firstOrFail();
    }

    // ── Version list ──────────────────────────────────────────────────────────
    #[Computed]
    public function versions()
    {
        return $this->page->versions()->latest()->get();
    }

    // ── Preview diff ──────────────────────────────────────────────────────────
    public function preview(?string $id): void
    {
        $this->previewId = $id;
    }

    #[Computed]
    public function previewVersion(): ?PageVersion
    {
        if (!$this->previewId) return null;
        return $this->page->versions()->find($this->previewId);
    }

    // ── Restore ───────────────────────────────────────────────────────────────
    public function askRestore(string $id): void
    {
        $this->restoreId      = $id;
        $this->confirmRestore = true;
    }

    public function cancelRestore(): void
    {
        $this->restoreId      = null;
        $this->confirmRestore = false;
    }

    public function restore(): void
    {
        $version = $this->page->versions()->find($this->restoreId);
        if (!$version) return;

        $this->page->restoreVersion($version);

        $this->restoreId      = null;
        $this->confirmRestore = false;
        $this->previewId      = null;

        session()->flash('success', 'Page restored to the selected version successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function fieldLabel(string $field): string
    {
        return match($field) {
            'title'              => 'Page Title',
            'banner_heading'     => 'Banner Heading',
            'banner_subheading'  => 'Banner Subheading',
            'banner_image'       => 'Banner Image',
            'banner_cta_text'    => 'CTA Text',
            'banner_cta_url'     => 'CTA URL',
            'meta_title'         => 'Meta Title',
            'meta_description'   => 'Meta Description',
            'meta_keywords'      => 'Meta Keywords',
            'canonical_url'      => 'Canonical URL',
            'og_title'           => 'OG Title',
            'og_description'     => 'OG Description',
            'og_image'           => 'OG Image',
            'og_type'            => 'OG Type',
            'twitter_card'       => 'Twitter Card',
            'twitter_title'      => 'Twitter Title',
            'twitter_description'=> 'Twitter Description',
            'twitter_image'      => 'Twitter Image',
            'schema_markup'      => 'Schema Markup',
            'extra'              => 'Extra Data',
            'is_active'          => 'Active',
            default              => ucwords(str_replace('_', ' ', $field)),
        };
    }
}; ?>

<div>
    <x-slot:heading>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pages.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">Pages</a>
            <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('admin.pages.edit', $page->key) }}" class="text-slate-400 hover:text-slate-600 transition-colors">{{ $page->title }}</a>
            <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span>Version History</span>
        </div>
    </x-slot:heading>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Restore confirm modal --}}
    @if($confirmRestore)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60" wire:click.self="cancelRestore">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0 h-9 w-9 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.999l-6.928-12c-.77-1.333-2.694-1.333-3.464 0L3.34 16.001C2.57 17.333 3.532 19 5.072 19z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Restore this version?</h3>
                    <p class="text-xs text-slate-500 mt-1">The current page content will first be saved as a new version, then this version will be applied. This action can be undone.</p>
                </div>
            </div>
            <div class="flex gap-2 justify-end">
                <button wire:click="cancelRestore" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancel</button>
                <button wire:click="restore" class="rounded-xl bg-amber-500 text-white px-4 py-2 text-sm font-semibold hover:bg-amber-600">
                    Yes, Restore
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- ── Version list ── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">
                    {{ $this->versions->count() }} {{ Str::plural('Version', $this->versions->count()) }}
                </h2>
                <a href="{{ route('admin.pages.edit', $page->key) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Current
                </a>
            </div>

            @forelse($this->versions as $version)
            <div class="rounded-2xl border {{ $previewId === $version->id ? 'border-cyan-400 bg-cyan-50/40' : 'border-slate-100 bg-white' }} shadow-sm overflow-hidden transition-colors">
                <div class="px-4 py-3 flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-mono text-slate-400">v#{{ $loop->iteration }}</span>
                            @if($loop->first)
                                <span class="rounded-full bg-cyan-100 text-cyan-700 text-[10px] font-semibold px-2 py-0.5">Latest</span>
                            @endif
                            <span class="text-xs text-slate-500">
                                {{ $version->created_at->format('d M Y, H:i') }}
                                <span class="text-slate-300">({{ $version->created_at->diffForHumans() }})</span>
                            </span>
                        </div>
                        @if($version->changed_by)
                            <p class="text-xs text-slate-500 mt-0.5">
                                <svg class="h-3 w-3 inline text-slate-400 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $version->changed_by }}
                            </p>
                        @endif
                        @if($version->note)
                            <p class="mt-1 text-xs text-slate-600 bg-slate-100 rounded-lg px-2 py-1 inline-block max-w-full truncate">
                                "{{ $version->note }}"
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button wire:click="preview('{{ $version->id }}')"
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Preview changes">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button wire:click="askRestore('{{ $version->id }}')"
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-100 hover:text-amber-700 transition-colors" title="Restore this version">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Snapshot field diff summary --}}
                @php
                    $snap    = $version->snapshot ?? [];
                    $current = $page->toArray();
                    $diffFields = [];
                    $comparableKeys = ['title','banner_heading','banner_subheading','banner_image','meta_title','meta_description','meta_keywords','og_title','og_description','is_active'];
                    foreach($comparableKeys as $f) {
                        $snapVal    = $snap[$f]    ?? null;
                        $currentVal = $current[$f] ?? null;
                        if ($snapVal != $currentVal) {
                            $diffFields[] = $f;
                        }
                    }
                @endphp
                @if(count($diffFields) > 0)
                <div class="px-4 pb-3">
                    <p class="text-[10px] text-slate-400 uppercase font-semibold mb-1.5">Differs from current:</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($diffFields as $df)
                            <span class="rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-[10px] px-2 py-0.5">
                                {{ $this->fieldLabel($df) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="px-4 pb-3">
                    <span class="text-[10px] text-green-600 flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Identical to current version
                    </span>
                </div>
                @endif
            </div>
            @empty
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm px-5 py-12 text-center">
                <svg class="h-10 w-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-slate-400">No versions saved yet.</p>
                <p class="text-xs text-slate-300 mt-1">Versions are created automatically each time you save this page.</p>
            </div>
            @endforelse
        </div>

        {{-- ── Preview panel ── --}}
        <div>
            @if($this->previewVersion)
            @php $snap = $this->previewVersion->snapshot ?? []; @endphp
            <div class="rounded-2xl border border-cyan-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-cyan-50 border-b border-cyan-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Snapshot Preview</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Saved {{ $this->previewVersion->created_at->format('d M Y, H:i') }}
                            @if($this->previewVersion->changed_by) by {{ $this->previewVersion->changed_by }} @endif
                        </p>
                        @if($this->previewVersion->note)
                            <p class="text-xs text-cyan-700 mt-0.5">"{{ $this->previewVersion->note }}"</p>
                        @endif
                    </div>
                    <button wire:click="preview(null)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 space-y-4 overflow-y-auto max-h-[70vh]">

                    @php
                        $sections = [
                            'Page' => ['title','is_active'],
                            'Banner' => ['banner_heading','banner_subheading','banner_image','banner_cta_text','banner_cta_url'],
                            'SEO' => ['meta_title','meta_description','meta_keywords','canonical_url'],
                            'Open Graph' => ['og_title','og_description','og_image','og_type'],
                            'Twitter' => ['twitter_card','twitter_title','twitter_description','twitter_image'],
                            'Advanced' => ['schema_markup','extra'],
                        ];
                    @endphp

                    @foreach($sections as $sectionLabel => $fields)
                    @php
                        $hasAny = false;
                        foreach($fields as $f) { if(isset($snap[$f]) && filled($snap[$f])) { $hasAny = true; break; } }
                    @endphp
                    @if($hasAny)
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wide mb-2">{{ $sectionLabel }}</p>
                        <div class="space-y-2">
                            @foreach($fields as $field)
                            @php $val = $snap[$field] ?? null; @endphp
                            @if(filled($val))
                            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2">
                                <p class="text-[10px] text-slate-400 uppercase font-semibold mb-0.5">{{ $this->fieldLabel($field) }}</p>
                                @if(is_array($val))
                                    <pre class="text-xs text-slate-700 whitespace-pre-wrap font-mono break-all">{{ json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif(is_bool($val))
                                    <p class="text-sm text-slate-700">{{ $val ? 'Yes' : 'No' }}</p>
                                @else
                                    <p class="text-sm text-slate-700 break-words">{{ $val }}</p>
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach

                </div>
                <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                    <button wire:click="askRestore('{{ $this->previewVersion->id }}')"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 text-white px-4 py-2 text-sm font-semibold hover:bg-amber-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Restore This Version
                    </button>
                </div>
            </div>
            @else
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm px-5 py-12 text-center">
                <svg class="h-10 w-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <p class="text-sm text-slate-400">Click the <strong class="text-slate-600">eye icon</strong> on a version to preview its content here.</p>
                <p class="text-xs text-slate-300 mt-1">You can compare it against the current live version and restore it if needed.</p>
            </div>
            @endif
        </div>

    </div>
</div>
