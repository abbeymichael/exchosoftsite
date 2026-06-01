<?php
use App\Models\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin')] #[Title('Edit Page — ExchoSoft')] class extends Component {
    use WithFileUploads;

    // ── Route param ───────────────────────────────────────────────────────────
    public string $key = '';

    // ── Page record ───────────────────────────────────────────────────────────
    public ?Page $page = null;

    // ── Banner fields ─────────────────────────────────────────────────────────
    public string $title = '';
    public $content = '';
    public string $banner_heading = '';
    public string $banner_subheading = '';
    public string $banner_image = '';
    public string $banner_cta_text = '';
    public string $banner_cta_url = '';

    // ── Image uploads (temporary) ─────────────────────────────────────────────
    public $uploadBannerImage = null;
    public $uploadOgImage = null;
    public $uploadTwitterImage = null;

    // ── SEO fields ────────────────────────────────────────────────────────────
    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';
    public string $canonical_url = '';

    // ── OpenGraph fields ──────────────────────────────────────────────────────
    public string $og_title = '';
    public string $og_description = '';
    public string $og_image = '';
    public string $og_type = 'website';

    // ── Twitter card fields ───────────────────────────────────────────────────
    public string $twitter_card = 'summary_large_image';
    public string $twitter_title = '';
    public string $twitter_description = '';
    public string $twitter_image = '';

    // ── Advanced fields ───────────────────────────────────────────────────────
    public string $schema_markup_raw = '';
    public string $extra_raw = '';

    // ── State ─────────────────────────────────────────────────────────────────
    public bool $is_active = true;
    public string $saveNote = '';
    public string $activeTab = 'banner';

    // ── Validation ────────────────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'content'             => ['nullable', 'string'],
            'banner_heading'      => ['nullable', 'string', 'max:255'],
            'banner_subheading'   => ['nullable', 'string', 'max:255'],
            'banner_image'        => ['nullable', 'string', 'max:500'],
            'banner_cta_text'     => ['nullable', 'string', 'max:100'],
            'banner_cta_url'      => ['nullable', 'string', 'max:500'],
            'uploadBannerImage'   => ['nullable', 'image', 'max:2048'],
            'uploadOgImage'       => ['nullable', 'image', 'max:2048'],
            'uploadTwitterImage'  => ['nullable', 'image', 'max:2048'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'meta_keywords'       => ['nullable', 'string', 'max:500'],
            'canonical_url'       => ['nullable', 'string', 'max:500'],
            'og_title'            => ['nullable', 'string', 'max:255'],
            'og_description'      => ['nullable', 'string', 'max:500'],
            'og_image'            => ['nullable', 'string', 'max:500'],
            'og_type'             => ['nullable', 'string', 'max:50'],
            'twitter_card'        => ['nullable', 'string', 'max:50'],
            'twitter_title'       => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image'       => ['nullable', 'string', 'max:500'],
            'schema_markup_raw'   => ['nullable', 'string'],
            'extra_raw'           => ['nullable', 'string'],
            'is_active'           => ['boolean'],
        ];
    }

    // ── Mount ─────────────────────────────────────────────────────────────────
    public function mount(string $key): void
    {
        $this->key  = $key;
        $this->page = Page::where('key', $key)->firstOrFail();

        $this->fill([
            'title'               => $this->page->title ?? '',
            'content'             => $this->page->content ?? '',
            'banner_heading'      => $this->page->banner_heading ?? '',
            'banner_subheading'   => $this->page->banner_subheading ?? '',
            'banner_image'        => $this->page->banner_image ?? '',
            'banner_cta_text'     => $this->page->banner_cta_text ?? '',
            'banner_cta_url'      => $this->page->banner_cta_url ?? '',
            'meta_title'          => $this->page->meta_title ?? '',
            'meta_description'    => $this->page->meta_description ?? '',
            'meta_keywords'       => $this->page->meta_keywords ?? '',
            'canonical_url'       => $this->page->canonical_url ?? '',
            'og_title'            => $this->page->og_title ?? '',
            'og_description'      => $this->page->og_description ?? '',
            'og_image'            => $this->page->og_image ?? '',
            'og_type'             => $this->page->og_type ?? 'website',
            'twitter_card'        => $this->page->twitter_card ?? 'summary_large_image',
            'twitter_title'       => $this->page->twitter_title ?? '',
            'twitter_description' => $this->page->twitter_description ?? '',
            'twitter_image'       => $this->page->twitter_image ?? '',
            'schema_markup_raw'   => $this->page->schema_markup
                                        ? json_encode($this->page->schema_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                        : '',
            'extra_raw'           => $this->page->extra
                                        ? json_encode($this->page->extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                        : '',
            'is_active'           => $this->page->is_active,
        ]);
    }

    // ── Tab switching ─────────────────────────────────────────────────────────
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ── Remove a saved image ──────────────────────────────────────────────────
    public function removeImage(string $field): void
    {
        // Delete from storage if it's a stored path (not an external URL)
        $current = $this->{$field};
        if ($current && !str_starts_with($current, 'http')) {
            Storage::disk('public')->delete($current);
        }
        $this->{$field} = '';

        // Also clear the pending upload
        $uploadField = match($field) {
            'banner_image'  => 'uploadBannerImage',
            'og_image'      => 'uploadOgImage',
            'twitter_image' => 'uploadTwitterImage',
            default         => null,
        };
        if ($uploadField) {
            $this->{$uploadField} = null;
        }
    }

    // ── Store a single upload, delete old file, return new path ───────────────
    private function storeUpload($upload, string $currentPath): string
    {
        // Delete old stored file (skip external URLs)
        if ($currentPath && !str_starts_with($currentPath, 'http')) {
            Storage::disk('public')->delete($currentPath);
        }

        return $upload->store('pages', 'public'); // → storage/app/public/pages/
    }

    // ── Save ─────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate();

        // Handle image uploads
        if ($this->uploadBannerImage) {
            $this->banner_image = $this->storeUpload($this->uploadBannerImage, $this->banner_image);
            $this->uploadBannerImage = null;
        }
        if ($this->uploadOgImage) {
            $this->og_image = $this->storeUpload($this->uploadOgImage, $this->og_image);
            $this->uploadOgImage = null;
        }
        if ($this->uploadTwitterImage) {
            $this->twitter_image = $this->storeUpload($this->uploadTwitterImage, $this->twitter_image);
            $this->uploadTwitterImage = null;
        }

        // Parse JSON fields
        $schemaMarkup = null;
        $extra        = null;

        if (filled($this->schema_markup_raw)) {
            $decoded = json_decode($this->schema_markup_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('schema_markup_raw', 'Invalid JSON: ' . json_last_error_msg());
                return;
            }
            $schemaMarkup = $decoded;
        }

        if (filled($this->extra_raw)) {
            $decoded = json_decode($this->extra_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('extra_raw', 'Invalid JSON: ' . json_last_error_msg());
                return;
            }
            $extra = $decoded;
        }

        // Snapshot
        $changedBy = auth()->user()?->name ?? (auth()->user()?->email ?? 'admin');
        $this->page->snapshot(
            $changedBy,
            filled($this->saveNote) ? $this->saveNote : 'Admin edit via Page Editor'
        );

        // Persist
        $this->page->update([
            'title'               => $this->title,
            'content'             => $this->content,
            'banner_heading'      => filled($this->banner_heading)      ? $this->banner_heading      : null,
            'banner_subheading'   => filled($this->banner_subheading)   ? $this->banner_subheading   : null,
            'banner_image'        => filled($this->banner_image)        ? $this->banner_image        : null,
            'banner_cta_text'     => filled($this->banner_cta_text)     ? $this->banner_cta_text     : null,
            'banner_cta_url'      => filled($this->banner_cta_url)      ? $this->banner_cta_url      : null,
            'meta_title'          => filled($this->meta_title)          ? $this->meta_title          : null,
            'meta_description'    => filled($this->meta_description)    ? $this->meta_description    : null,
            'meta_keywords'       => filled($this->meta_keywords)       ? $this->meta_keywords       : null,
            'canonical_url'       => filled($this->canonical_url)       ? $this->canonical_url       : null,
            'og_title'            => filled($this->og_title)            ? $this->og_title            : null,
            'og_description'      => filled($this->og_description)      ? $this->og_description      : null,
            'og_image'            => filled($this->og_image)            ? $this->og_image            : null,
            'og_type'             => filled($this->og_type)             ? $this->og_type             : 'website',
            'twitter_card'        => filled($this->twitter_card)        ? $this->twitter_card        : 'summary_large_image',
            'twitter_title'       => filled($this->twitter_title)       ? $this->twitter_title       : null,
            'twitter_description' => filled($this->twitter_description) ? $this->twitter_description : null,
            'twitter_image'       => filled($this->twitter_image)       ? $this->twitter_image       : null,
            'schema_markup'       => $schemaMarkup,
            'extra'               => $extra,
            'is_active'           => $this->is_active,
        ]);

        $this->saveNote = '';
        session()->flash('success', 'Page updated successfully.');
    }

    // ── Copy SEO → OG ─────────────────────────────────────────────────────────
    public function copyMetaToOg(): void
    {
        $this->og_title       = $this->meta_title;
        $this->og_description = $this->meta_description;
    }

    // ── Copy OG → Twitter ─────────────────────────────────────────────────────
    public function copyOgToTwitter(): void
    {
        $this->twitter_title       = $this->og_title;
        $this->twitter_description = $this->og_description;
        $this->twitter_image       = $this->og_image;
        $this->uploadTwitterImage  = null; // clear pending upload if OG string is being copied
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private static array $routeMap = [
        'home'                      => 'home',
        'about'                     => 'site.about',
        'services'                  => 'site.services',
        'contact'                   => 'site.contact',
        'products'                  => 'site.products',
        'portfolio'                 => 'site.portfolio',
        'case-studies'              => 'site.case-studies',
        'white-papers'              => 'site.white-papers',
        'blog'                      => 'site.blog',
        'consulting'                => 'site.consulting',
        'book-demo'                 => 'site.book-demo',
        'privacy-policy'            => 'site.privacy-policy',
        'terms-of-service'          => 'site.terms-of-service',
        'security'                  => 'site.security',
        'cookie-policy'             => 'site.cookie-policy',
        'data-processing-agreement' => 'site.data-processing-agreement',
    ];

    #[Computed]
    public function liveUrl(): ?string
    {
        $routeName = self::$routeMap[$this->key] ?? null;
        if (!$routeName) return null;
        try {
            return route($routeName);
        } catch (\Exception) {
            return null;
        }
    }
}; ?>

<div>
    <x-slot:heading>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pages.index') }}"
                class="text-slate-400 hover:text-slate-600 transition-colors">Pages</a>
            <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span>{{ $page->title }}</span>
        </div>
    </x-slot:heading>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-5">

        {{-- Header bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="font-mono text-xs bg-slate-100 text-slate-600 rounded-lg px-2.5 py-1.5">{{ $page->key }}</span>
                @if ($this->liveUrl)
                    <a href="{{ $this->liveUrl }}" target="_blank"
                        class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-600 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View live page
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.versions', $page->key) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Version History
                    <span class="rounded-full bg-slate-100 px-1.5 text-[10px] font-semibold text-slate-500">{{ $page->versions()->count() }}</span>
                </a>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="relative w-9 h-5 bg-slate-200 peer-checked:bg-green-500 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
                    <span class="text-xs text-slate-600">{{ $is_active ? 'Active' : 'Inactive' }}</span>
                </label>
            </div>
        </div>

        {{-- Tab bar --}}
        @php
            $tabs = [
                ['banner',   'Banner',     '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                ['content',  'Content',    '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
                ['seo',      'SEO',        '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
                ['og',       'Open Graph', '<path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>'],
                ['twitter',  'Twitter',    '<path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>'],
                ['advanced', 'Advanced',   '<path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
            ];
        @endphp

        <div class="flex gap-1 rounded-xl bg-slate-100 p-1 w-fit">
            @foreach ($tabs as [$tab, $label, $icon])
                <button wire:click="setTab('{{ $tab }}')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all
                           {{ $activeTab === $tab ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $icon !!}</svg>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">

            {{-- ── BANNER TAB ── --}}
            @if ($activeTab === 'banner')
                <div class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Page Title <span class="text-red-400">*</span></h3>
                        <p class="text-xs text-slate-400 mb-2">The HTML <code>&lt;title&gt;</code> tag — shown in browser tab and used as a fallback for SEO/OG.</p>
                        <input wire:model="title" type="text" placeholder="e.g. About Us — Exchosoft Consult"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400 @error('title') border-red-400 @enderror">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">{{ strlen($title) }}/255 characters</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Banner Heading</label>
                            <input wire:model="banner_heading" type="text" placeholder="e.g. Built From Here. Built For Here."
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            <p class="mt-1 text-xs text-slate-400">Main headline displayed in the page hero banner.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Banner Subheading</label>
                            <input wire:model="banner_subheading" type="text" placeholder="Short supporting line…"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            <p class="mt-1 text-xs text-slate-400">Shorter supporting text beneath the heading.</p>
                        </div>
                    </div>

                    {{-- Banner Image Upload --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Banner Image</label>
                        <p class="text-xs text-slate-400 mb-2">Background or hero image. Recommended: 1920×600px. Max 2MB.</p>

                        @if ($uploadBannerImage)
                            {{-- New upload preview --}}
                            <div class="relative rounded-xl overflow-hidden border border-cyan-200 bg-slate-50 h-36">
                                <img src="{{ $uploadBannerImage->temporaryUrl() }}" class="w-full h-full object-cover" alt="Banner preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="$set('uploadBannerImage', null)" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">
                                        Remove
                                    </button>
                                </div>
                                <span class="absolute bottom-2 left-2 rounded-md bg-black/50 text-white text-[10px] px-2 py-0.5">New — not saved yet</span>
                            </div>
                        @elseif ($banner_image)
                            {{-- Existing saved image --}}
                            <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-50 h-36">
                                <img src="{{ Str::startsWith($banner_image, 'http') ? $banner_image : Storage::url($banner_image) }}"
                                    class="w-full h-full object-cover" alt="Banner preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="removeImage('banner_image')" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Replace image
                                    <input type="file" wire:model="uploadBannerImage" accept="image/*" class="sr-only">
                                </label>
                            </div>
                        @else
                            {{-- Empty drop zone --}}
                            <label class="flex flex-col items-center justify-center w-full h-36 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 hover:bg-cyan-50/30 transition-colors">
                                <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-slate-400">Click to upload banner image</span>
                                <span class="text-[10px] text-slate-300 mt-0.5">PNG, JPG, WEBP — max 2MB</span>
                                <input type="file" wire:model="uploadBannerImage" accept="image/*" class="sr-only">
                            </label>
                        @endif

                        <div wire:loading wire:target="uploadBannerImage" class="mt-1.5 text-xs text-cyan-600 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Uploading…
                        </div>
                        @error('uploadBannerImage')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">CTA Button Text</label>
                            <input wire:model="banner_cta_text" type="text" placeholder="e.g. Get in Touch"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">CTA Button URL</label>
                            <input wire:model="banner_cta_url" type="text" placeholder="e.g. /contact or https://…"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>

                    {{-- Live preview card --}}
                    @if ($banner_heading || $banner_subheading)
                        <div class="rounded-xl border border-slate-200 bg-slate-900 p-6 mt-2">
                            <p class="text-xs text-slate-500 mb-3 uppercase font-semibold tracking-wide">Banner Preview</p>
                            @if ($banner_heading)
                                <h2 class="text-xl font-bold text-white mb-1">{{ $banner_heading }}</h2>
                            @endif
                            @if ($banner_subheading)
                                <p class="text-sm text-slate-400">{{ $banner_subheading }}</p>
                            @endif
                            @if ($banner_cta_text)
                                <div class="mt-3">
                                    <span class="inline-block rounded-lg bg-cyan-500 text-white text-xs font-semibold px-3 py-1.5">{{ $banner_cta_text }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── CONTENT TAB ── --}}
            @if ($activeTab === 'content')
                <div class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Page Content</h3>
                        <p class="text-xs text-slate-400 mb-2">Markdown — used for legal and static pages.</p>
                        <livewire:markdown-editor
                            wire:model="content"
                            placeholder="Write your page content with markdown..."
                            :rows="15"
                            :show-toolbar="true"
                            :show-upload="true"
                        />
                    </div>
                </div>
            @endif

            {{-- ── SEO TAB ── --}}
            @if ($activeTab === 'seo')
                <div class="space-y-5">
                    <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-xs text-blue-700">
                        <strong>SEO tip:</strong> Meta title should be 50–60 characters. Meta description should be 120–160 characters.
                        If left blank, the page <em>title</em> field is used as a fallback.
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Meta Title</label>
                        <input wire:model="meta_title" type="text" placeholder="Appears in Google search results…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">Aim for 50–60 characters.</p>
                            <span class="text-xs {{ strlen($meta_title) > 60 ? 'text-red-500 font-semibold' : (strlen($meta_title) >= 50 ? 'text-green-600' : 'text-slate-400') }}">
                                {{ strlen($meta_title) }}/60
                            </span>
                        </div>
                        @if ($meta_title)
                            <div class="mt-2 rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-[10px] text-slate-400 uppercase font-semibold mb-1">Google SERP Preview</p>
                                <div class="text-xs text-green-700">{{ url($canonical_url ?: '/') }}</div>
                                <div class="text-sm font-medium text-blue-700 mt-0.5 truncate">{{ $meta_title }}</div>
                                @if ($meta_description)
                                    <div class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $meta_description }}</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Meta Description</label>
                        <textarea wire:model="meta_description" rows="3" placeholder="Concise summary of the page for search engines…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">Aim for 120–160 characters.</p>
                            <span class="text-xs {{ strlen($meta_description) > 160 ? 'text-red-500 font-semibold' : (strlen($meta_description) >= 120 ? 'text-green-600' : 'text-slate-400') }}">
                                {{ strlen($meta_description) }}/160
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Meta Keywords</label>
                        <input wire:model="meta_keywords" type="text" placeholder="comma, separated, keywords"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        <p class="mt-1 text-xs text-slate-400">Low SEO impact in modern search, but useful for indexers and internal search.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Canonical URL</label>
                        <input wire:model="canonical_url" type="text" placeholder="/about or https://exchosoft.com/about"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        <p class="mt-1 text-xs text-slate-400">Tells search engines the preferred URL for this page. Usually the page's own path.</p>
                    </div>
                </div>
            @endif

            {{-- ── OPEN GRAPH TAB ── --}}
            @if ($activeTab === 'og')
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Open Graph</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Controls how this page looks when shared on Facebook, LinkedIn, Slack, etc.</p>
                        </div>
                        <button wire:click="copyMetaToOg" type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                            ← Copy from SEO
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">OG Title</label>
                        <input wire:model="og_title" type="text" placeholder="Title shown when shared on social…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        <p class="mt-1 text-xs text-slate-400">Falls back to meta_title → page title if blank.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">OG Description</label>
                        <textarea wire:model="og_description" rows="3" placeholder="Description shown when shared on social…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>

                    {{-- OG Image Upload --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">OG Image</label>
                        <p class="text-xs text-slate-400 mb-2">Recommended: 1200×630px. Shown as the link preview image on social platforms. Max 2MB.</p>

                        @if ($uploadOgImage)
                            <div class="relative rounded-xl overflow-hidden border border-cyan-200 bg-slate-50" style="height:120px;max-width:400px;">
                                <img src="{{ $uploadOgImage->temporaryUrl() }}" class="w-full h-full object-cover" alt="OG preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="$set('uploadOgImage', null)" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">Remove</button>
                                </div>
                                <span class="absolute bottom-2 left-2 rounded-md bg-black/50 text-white text-[10px] px-2 py-0.5">New — not saved yet</span>
                            </div>
                        @elseif ($og_image)
                            <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-50" style="height:120px;max-width:400px;">
                                <img src="{{ Str::startsWith($og_image, 'http') ? $og_image : Storage::url($og_image) }}"
                                    class="w-full h-full object-cover" alt="OG preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="removeImage('og_image')" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">Remove</button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Replace image
                                    <input type="file" wire:model="uploadOgImage" accept="image/*" class="sr-only">
                                </label>
                            </div>
                        @else
                            <label class="flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 hover:bg-cyan-50/30 transition-colors" style="height:120px;max-width:400px;">
                                <svg class="h-7 w-7 text-slate-300 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-slate-400">Click to upload OG image</span>
                                <span class="text-[10px] text-slate-300 mt-0.5">1200×630px recommended</span>
                                <input type="file" wire:model="uploadOgImage" accept="image/*" class="sr-only">
                            </label>
                        @endif

                        <div wire:loading wire:target="uploadOgImage" class="mt-1.5 text-xs text-cyan-600 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Uploading…
                        </div>
                        @error('uploadOgImage')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">OG Type</label>
                            <select wire:model="og_type"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="website">website</option>
                                <option value="article">article</option>
                                <option value="profile">profile</option>
                                <option value="product">product</option>
                            </select>
                        </div>
                    </div>

                    {{-- OG Card Preview --}}
                    @php
                        $ogPreviewSrc = $uploadOgImage
                            ? $uploadOgImage->temporaryUrl()
                            : ($og_image ? (Str::startsWith($og_image, 'http') ? $og_image : Storage::url($og_image)) : null);
                    @endphp
                    @if ($og_title || $og_description)
                        <div class="rounded-xl border border-slate-200 overflow-hidden max-w-sm">
                            <p class="text-[10px] text-slate-400 uppercase font-semibold px-3 pt-2">Social Preview</p>
                            @if ($ogPreviewSrc)
                                <div class="h-32 bg-slate-100"><img src="{{ $ogPreviewSrc }}" alt="" class="w-full h-full object-cover"></div>
                            @else
                                <div class="h-32 bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                                    <span class="text-slate-500 text-xs">No image set</span>
                                </div>
                            @endif
                            <div class="p-3 bg-slate-50 border-t border-slate-200">
                                <p class="text-[10px] uppercase text-slate-400 mb-0.5">exchosoft.com</p>
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $og_title ?: $meta_title ?: $title }}</p>
                                @if ($og_description)
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $og_description }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── TWITTER TAB ── --}}
            @if ($activeTab === 'twitter')
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Twitter / X Card</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Controls how this page looks when shared on Twitter/X. Falls back to OG values if blank.</p>
                        </div>
                        <button wire:click="copyOgToTwitter" type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                            ← Copy from OG
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Card Type</label>
                        <select wire:model="twitter_card"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="summary_large_image">summary_large_image (recommended — large banner)</option>
                            <option value="summary">summary (small thumbnail)</option>
                            <option value="app">app</option>
                            <option value="player">player</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Twitter Title</label>
                        <input wire:model="twitter_title" type="text" placeholder="Overrides OG title on Twitter/X…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Twitter Description</label>
                        <textarea wire:model="twitter_description" rows="3" placeholder="Overrides OG description on Twitter/X…"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>

                    {{-- Twitter Image Upload --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Twitter Image</label>
                        <p class="text-xs text-slate-400 mb-2">Recommended: 1200×600px for summary_large_image. Fallback: OG image. Max 2MB.</p>

                        @if ($uploadTwitterImage)
                            <div class="relative rounded-xl overflow-hidden border border-cyan-200 bg-slate-50" style="height:120px;max-width:400px;">
                                <img src="{{ $uploadTwitterImage->temporaryUrl() }}" class="w-full h-full object-cover" alt="Twitter preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="$set('uploadTwitterImage', null)" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">Remove</button>
                                </div>
                                <span class="absolute bottom-2 left-2 rounded-md bg-black/50 text-white text-[10px] px-2 py-0.5">New — not saved yet</span>
                            </div>
                        @elseif ($twitter_image)
                            <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-50" style="height:120px;max-width:400px;">
                                <img src="{{ Str::startsWith($twitter_image, 'http') ? $twitter_image : Storage::url($twitter_image) }}"
                                    class="w-full h-full object-cover" alt="Twitter preview">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="removeImage('twitter_image')" type="button"
                                        class="rounded-lg bg-red-500 text-white text-xs font-semibold px-3 py-1.5">Remove</button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Replace image
                                    <input type="file" wire:model="uploadTwitterImage" accept="image/*" class="sr-only">
                                </label>
                            </div>
                        @else
                            <label class="flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 hover:bg-cyan-50/30 transition-colors" style="height:120px;max-width:400px;">
                                <svg class="h-7 w-7 text-slate-300 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-slate-400">Click to upload Twitter image</span>
                                <span class="text-[10px] text-slate-300 mt-0.5">1200×600px recommended</span>
                                <input type="file" wire:model="uploadTwitterImage" accept="image/*" class="sr-only">
                            </label>
                        @endif

                        <div wire:loading wire:target="uploadTwitterImage" class="mt-1.5 text-xs text-cyan-600 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Uploading…
                        </div>
                        @error('uploadTwitterImage')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- ── ADVANCED TAB ── --}}
            @if ($activeTab === 'advanced')
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Schema Markup (JSON-LD)</label>
                        <p class="text-xs text-slate-400 mb-2">
                            Structured data injected as a <code class="bg-slate-100 px-1 rounded">&lt;script type="application/ld+json"&gt;</code> tag.
                            Must be valid JSON. <a href="https://schema.org" target="_blank" class="text-cyan-600 hover:underline">schema.org reference ↗</a>
                        </p>
                        <textarea wire:model="schema_markup_raw" rows="10"
                            placeholder='{&#10;  "@@context": "https://schema.org",&#10;  "@@type": "WebPage",&#10;  "name": "About Us"&#10;}'
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-mono focus:outline-none focus:border-cyan-400 resize-y {{ $errors->has('schema_markup_raw') ? 'border-red-400' : '' }}"></textarea>
                        @error('schema_markup_raw')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Extra Data (JSON)</label>
                        <p class="text-xs text-slate-400 mb-2">
                            Free-form JSON for page-specific overrides, feature flags, or CMS keys. Available in the Livewire component as <code class="bg-slate-100 px-1 rounded">$page->extra['key']</code>.
                        </p>
                        <textarea wire:model="extra_raw" rows="8"
                            placeholder='{&#10;  "section": "legal",&#10;  "show_cta": true&#10;}'
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-mono focus:outline-none focus:border-cyan-400 resize-y @error('extra_raw') border-red-400 @enderror"></textarea>
                        @error('extra_raw')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

        </div>

        {{-- Save bar --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Save Note <span class="text-slate-400 font-normal">(optional — stored in version history)</span></label>
                    <input wire:model="saveNote" type="text" placeholder="e.g. Updated banner heading and SEO description"
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <div class="flex items-center gap-2 sm:mt-5">
                    <a href="{{ route('admin.pages.index') }}"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="rounded-xl bg-cyan-500 text-white px-5 py-2 text-sm font-semibold hover:bg-cyan-600 transition-colors disabled:opacity-60 flex items-center gap-2">
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save Page</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
