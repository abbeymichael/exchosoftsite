<?php

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Products — Exchosoft Consult')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    // Static data loaded once in mount
    public mixed $featuredGroups = null;
    public mixed $allPublished = null;
    public mixed $washSections = null;
    public mixed $churchSections = null;
    public array $linkedCodes = [];

    public function mount(): void
    {
        $this->featuredGroups = Product::published()->featured()->orderBy('sort_order')->take(3)->get();
        $this->allPublished = Product::published()->orderBy('sort_order')->get();
        $this->linkedCodes = $this->featuredGroups->keys()->toArray();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        return Product::published()
            ->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('name', 'like', '%' . $this->search . '%')->orWhere('tagline', 'like', '%' . $this->search . '%')))
            ->when($this->filterCategory, fn($q) => $q->inCategory($this->filterCategory))
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);
    }
}; ?>


<div class="font-sans bg-background text-on-surface overflow-x-hidden">

    {{-- ── HERO ── --}}
    <x-page-banner
        class="text-center flex flex-col items-center justify-center"
        tag="Our Products"
        tagIcon="deployed_code"
        title='Applications Built for <span style="color:#00b8db;">Your Reality</span>'
        subtitle="Ready-to-deploy tools and platforms crafted for African businesses — offline-first, resilient, and built to scale."
        glowX="68%"
        glowX2="12%"
    >
        <svg
            slot="ornament"
            class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none"
            viewBox="0 0 180 180"
            fill="none"
        >
            <rect x="1" y="1" width="178" height="178" rx="16" stroke="#00b8db" stroke-width="1" />
            <rect x="22" y="22" width="136" height="136" rx="10" stroke="#00b8db" stroke-width="1" />
            <rect x="44" y="44" width="92" height="92" rx="6" stroke="#00b8db" stroke-width="1" />
            <rect x="66" y="66" width="48" height="48" rx="4" stroke="#00b8db" stroke-width="1" />
            <line x1="90" y1="1" x2="90" y2="179" stroke="#00b8db" stroke-width=".5" />
            <line x1="1" y1="90" x2="179" y2="90" stroke="#00b8db" stroke-width=".5" />
            <circle cx="90" cy="90" r="5" fill="#00b8db" />
        </svg>
    </x-page-banner>

    {{-- ── CONTROLS: SEARCH & FILTER PILLS ── --}}
    <section class="max-w-[1440px] mx-auto px-6 md:px-16 pt-12 pb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between pb-6 border-b border-outline-variant/20">

            <div class="relative w-full md:w-96 group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface/40 group-focus-within:text-[#00b8db] transition-colors">
                    search
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search systems..."
                    class="w-full pl-12 pr-4 py-3 bg-surface-container border border-outline-variant/40 rounded-2xl text-body-md focus:outline-none focus:border-[#00b8db]/80 focus:ring-2 focus:ring-[#00b8db]/10 transition-all placeholder:text-on-surface/30"
                />
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface/30 hover:text-error transition-colors">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                @endif
            </div>

            <div class="flex flex-wrap gap-2 items-center w-full md:w-auto overflow-x-auto no-scrollbar">
                <button
                    wire:click="$set('filterCategory', '')"
                    class="px-5 py-2.5 rounded-full text-label-md font-display tracking-wide border transition-all whitespace-nowrap {{ $filterCategory === '' ? 'bg-[#00b8db] text-white border-[#00b8db] shadow-md' : 'bg-surface border-outline-variant/40 text-on-surface/60 hover:border-[#00b8db]/40 hover:text-on-surface' }}"
                >
                    All Architecture
                </button>
                @foreach(['retail', 'services', 'logistics', 'hospitality', 'enterprise'] as $cat)
                    <button
                        wire:click="$set('filterCategory', '{{ $cat }}')"
                        class="px-5 py-2.5 rounded-full text-label-md font-display tracking-wide border transition-all whitespace-nowrap capitalize {{ $filterCategory === $cat ? 'bg-[#00b8db] text-white border-[#00b8db] shadow-md' : 'bg-surface border-outline-variant/40 text-on-surface/60 hover:border-[#00b8db]/40 hover:text-on-surface' }}"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ── FEATURED SHOWCASE (ONLY SHOWS WHEN NOT SEARCHING/FILTERING) ── --}}
    @if(!$search && !$filterCategory && $featuredGroups && $featuredGroups->count() > 0)
        <section class="max-w-[1440px] mx-auto px-6 md:px-16 py-8">
            <p class="text-label-sm text-[#00b8db] mb-2 uppercase tracking-widest font-display font-semibold">Flagship Implementations</p>
            <h2 class="font-display text-headline-xl text-on-surface tracking-tight mb-8">Featured Solutions</h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @foreach($featuredGroups as $feat)
                    <div class="group relative flex flex-col bg-surface-container border border-outline-variant/40 rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-[#00b8db]/30">

                        <div class="h-44 overflow-hidden bg-primary relative flex items-center justify-center">
                            <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:2rem_2rem] opacity-20"></div>
                            @if($feat->cover_image)
                                <img src="{{ asset(Storage::url($feat->cover_image)) }}" alt="{{ $feat->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500">
                            @else
                                <span class="material-symbols-outlined text-on-primary/10 select-none" style="font-size: 5rem;">lan</span>
                            @endif
                            <div class="absolute top-4 left-4 px-3 py-1 bg-secondary/90 backdrop-blur-sm rounded-md border border-secondary/20 text-label-sm font-semibold text-on-secondary font-display uppercase tracking-wider">
                                {{ $feat->platform ?? 'LAN Node' }}
                            </div>
                        </div>

                        <div class="p-6 flex flex-col flex-1 space-y-4">
                            <div>
                                <h3 class="font-display text-headline-md text-on-surface group-hover:text-[#00b8db] transition-colors leading-snug">{{ $feat->name }}</h3>
                                <p class="text-body-sm text-on-surface/40 mt-1 font-mono">v{{ $feat->current_version ?? '1.0.0' }}</p>
                            </div>

                            <p class="text-body-md text-on-surface/70 line-clamp-2 flex-1">{{ $feat->tagline ?? $feat->description }}</p>

                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-outline-variant/20 text-label-sm text-on-surface/50">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[#00b8db] text-base">wifi_off</span>
                                    <span>{{ $feat->offline_ttl_hours ?? 'Infinite' }}h Offline TTL</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[#00b8db] text-base">devices</span>
                                    <span>Max {{ $feat->max_devices ?? 'Unlimited' }} Devices</span>
                                </div>
                            </div>

                            <div class="pt-4 flex items-center justify-between border-t border-outline-variant/20">
                                <div>
                                    <span class="text-label-sm text-on-surface/40 block font-display">Starting price</span>
                                    <span class="text-headline-sm font-bold text-on-surface font-display">
                                        @if($feat->starting_price)
                                            {{ $feat->currency }} {{ number_format($feat->starting_price, 2) }}
                                        @else
                                            Free / Demo
                                        @endif
                                    </span>
                                </div>
                                <a href="{{ route('site.products.show', $feat->slug) }}" wire:navigate class="inline-flex items-center justify-center bg-secondary text-on-secondary font-semibold p-3.5 rounded-2xl hover:bg-[#00b8db] hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── MAIN PRODUCTS GRID WITH PAGINATION ── --}}
    <section class="max-w-[1440px] mx-auto px-6 md:px-16 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-display text-headline-lg text-on-surface tracking-tight">
                    {{ $search || $filterCategory ? 'Filtered Architectures' : 'Complete Software Catalog' }}
                </h2>
                <p class="text-body-sm text-on-surface/40 mt-1">Showing localized operations stacks</p>
            </div>
            <span class="text-label-md font-mono bg-surface-container-high px-3 py-1.5 rounded-lg border border-outline-variant/30 text-on-surface/60">
                Total: {{ $this->products->total() }}
            </span>
        </div>

        @if($this->products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->products as $product)
                    <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate class="group flex flex-col bg-surface border border-outline-variant/30 rounded-2xl overflow-hidden no-underline hover:border-[#00b8db]/40 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">

                        <div class="h-64 bg-surface-container-high flex items-center justify-center relative p-4 shrink-0 border-b border-outline-variant/20">
                            @if($product->cover_image)
                                <img src="{{ asset(Storage::url($product->cover_image)) }}" alt="{{ $product->name }} Logo" class="h-auto w-auto object-contain transition-transform group-hover:scale-105 duration-300">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-[#00b8db]/10 flex items-center justify-center text-[#00b8db]">
                                    <span class="material-symbols-outlined text-2xl">deployed_code</span>
                                </div>
                            @endif

                            <span class="absolute top-3 right-3 px-2 py-0.5 bg-surface rounded text-label-xs font-mono border border-outline-variant/20 text-on-surface/50">
                                {{ $product->platform ?? 'LAN' }}
                            </span>
                        </div>

                        <div class="p-5 flex flex-col flex-1 justify-between space-y-4">
                            <div>
                                <h3 class="font-display text-headline-sm text-on-surface group-hover:text-[#00b8db] transition-colors line-clamp-1 leading-snug">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-body-sm text-on-surface/50 line-clamp-2 mt-1.5 leading-relaxed">
                                    {{ $product->tagline ?? $product->description }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-1.5 pt-2">
                                <span class="text-label-xs font-mono bg-surface-container px-2 py-0.5 rounded text-on-surface/60 border border-outline-variant/10">
                                    Offline Capable
                                </span>
                                @if($product->category)
                                    <span class="text-label-xs font-mono bg-[#00b8db]/5 text-[#00b8db] px-2 py-0.5 rounded border border-[#00b8db]/10 capitalize">
                                        {{ $product->category }}
                                    </span>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-outline-variant/10 flex items-center justify-between text-label-sm">
                                <span class="text-on-surface/40 font-display">Starting at</span>
                                <span class="font-display font-bold text-on-surface">
                                    @if($product->starting_price)
                                        {{ $product->currency }} {{ number_format($product->starting_price, 0) }}
                                    @else
                                        Setup/Quote
                                    @endif
                                </span>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

            <div class="mt-12 pt-6 border-t border-outline-variant/20">
                {{ $this->products->links() }}
            </div>

        @else
            <div class="text-center py-20 bg-surface-container/30 border border-dashed border-outline-variant/60 rounded-3xl max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-surface-container-high flex items-center justify-center mx-auto text-on-surface/30">
                    <span class="material-symbols-outlined text-3xl">search_off</span>
                </div>
                <h3 class="font-display text-headline-md text-on-surface">No deployments match your search</h3>
                <p class="text-body-md text-on-surface/50 max-w-sm mx-auto">
                    We are constantly blueprinting new offline-first structures. Try updating your filters or reach out directly for a custom stack build.
                </p>
                <div class="pt-2">
                    <button wire:click="$set('search', ''); $set('filterCategory', '');" class="text-label-md font-display font-semibold text-[#00b8db] hover:underline">
                        Clear all filters
                    </button>
                </div>
            </div>
        @endif
    </section>

</div>
