<?php

use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('ExchoSoft — Innovative Software Solutions for Africa')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        $featuredProducts = ShopProduct::published()->featured()->orderBy('sort_order')->limit(3)->get();
        $latestPosts      = BlogPost::published()->latest('published_at')->limit(3)->get();
        $featuredCases    = CaseStudy::published()->featured()->limit(3)->get();
        $featuredWork     = PortfolioItem::published()->featured()->orderBy('sort_order')->limit(4)->get();

        return view('livewire.pages.site.home', compact('featuredProducts', 'latestPosts', 'featuredCases', 'featuredWork'));
    }
}; ?>

<div>

    {{-- ══════════════════════════════════════════════════════════════════════
         HERO — PLACEHOLDER (Replace with your own design)
    ══════════════════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 text-white">
        {{-- Decorative circles --}}
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-violet-500/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="max-w-3xl">
                {{-- Badge --}}
                <span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs font-semibold text-cyan-300 mb-6">
                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    Ghana's Leading Software Company
                </span>
                {{-- Headline PLACEHOLDER --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight mb-6">
                    🚧 Hero Section
                    <span class="text-cyan-400">Placeholder</span>
                </h1>
                <p class="text-lg sm:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                    Replace this with your own compelling headline and description about ExchoSoft's mission, products, and value proposition. This section is intentionally left as a placeholder.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('site.products') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-3 text-sm font-semibold text-white hover:bg-cyan-400 transition-colors shadow-lg shadow-cyan-500/25">
                        Explore Products
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('site.book-demo') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                        Book a Demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════════
         STATS — PLACEHOLDER
    ══════════════════════════════════════════════════════════════════════ --}}
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                @foreach([['5+', 'Software Products'], ['100+', 'Happy Customers'], ['3+', 'Years Experience'], ['10+', 'Industries Served']] as [$num, $label])
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900">{{ $num }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $label }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════════
         FEATURED PRODUCTS
    ══════════════════════════════════════════════════════════════════════ --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-cyan-600 mb-2">Our Software</p>
                <h2 class="text-3xl font-bold text-slate-900">Featured Products</h2>
                <p class="mt-3 text-slate-500 max-w-xl mx-auto">Enterprise software built for African businesses — robust, reliable, and ready to scale.</p>
            </div>

            @if($featuredProducts->isEmpty())
            {{-- Placeholder cards --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(range(1,3) as $i)
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-slate-200 text-slate-400">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="font-semibold text-slate-400">Product {{ $i }} — Placeholder</p>
                    <p class="text-xs text-slate-400 mt-1">Add products from the admin panel</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($featuredProducts as $product)
                <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate
                   class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    <div class="bg-gradient-to-br from-slate-100 to-slate-50 h-40 flex items-center justify-center">
                        @if($product->cover_image)
                            <img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">{{ $product->name }}</p>
                        @if($product->tagline)<p class="text-sm text-slate-500 mt-1">{{ $product->tagline }}</p>@endif
                        <div class="flex items-center justify-between mt-4">
                            <div>
                                @if($product->is_on_sale)
                                    <p class="text-xs text-slate-400 line-through">GHS {{ number_format($product->price, 2) }}</p>
                                    <p class="font-bold text-green-600">GHS {{ number_format($product->sale_price, 2) }}</p>
                                @else
                                    <p class="font-bold text-slate-900">GHS {{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 capitalize">{{ $product->category }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            <div class="text-center mt-10">
                <a href="{{ route('site.products') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    View All Products
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════════
         CONSULTING CTA — PLACEHOLDER
    ══════════════════════════════════════════════════════════════════════ --}}
    <section class="bg-gradient-to-r from-cyan-600 to-violet-600 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">🚧 Consulting/Gigs CTA — Placeholder</h2>
            <p class="text-cyan-100 max-w-xl mx-auto mb-8">Replace with your consulting services pitch. Highlight your expertise and what problems you solve for clients.</p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('site.consulting') }}" wire:navigate class="inline-flex rounded-xl bg-white px-6 py-3 text-sm font-semibold text-cyan-700 hover:bg-cyan-50 transition-colors shadow-lg">
                    View Services
                </a>
                <a href="{{ route('site.book-demo') }}" wire:navigate class="inline-flex rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20 transition-colors">
                    Book a Free Consultation
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════════
         FEATURED WORK
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($featuredWork->isNotEmpty())
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-cyan-600 mb-2">Our Work</p>
                <h2 class="text-3xl font-bold text-slate-900">Portfolio Highlights</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featuredWork as $item)
                <a href="{{ route('site.portfolio.show', $item->slug) }}" wire:navigate
                   class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all p-5">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 capitalize mb-3">{{ $item->category }}</span>
                    <p class="font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">{{ $item->title }}</p>
                    @if($item->client_name)<p class="text-xs text-slate-400 mt-1">{{ $item->client_name }}</p>@endif
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('site.portfolio') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">View Full Portfolio</a>
            </div>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         LATEST BLOG POSTS
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($latestPosts->isNotEmpty())
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-cyan-600 mb-2">Stay Informed</p>
                <h2 class="text-3xl font-bold text-slate-900">From the Blog</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($latestPosts as $post)
                <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="bg-slate-100 h-36 flex items-center justify-center">
                        @if($post->cover_image)
                            <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-semibold uppercase text-cyan-600 capitalize">{{ $post->category }}</span>
                        <p class="font-bold text-slate-900 mt-1 group-hover:text-cyan-700 transition-colors line-clamp-2">{{ $post->title }}</p>
                        @if($post->excerpt)<p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $post->excerpt }}</p>@endif
                        <p class="text-xs text-slate-400 mt-3">{{ $post->published_at?->format('d M Y') }} · {{ $post->read_time_minutes }} min read</p>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('site.blog') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">Read More Posts</a>
            </div>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         BOOK DEMO CTA
    ══════════════════════════════════════════════════════════════════════ --}}
    <section class="bg-slate-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">See Our Products in Action</h2>
            <p class="text-slate-400 max-w-xl mx-auto mb-8">Book a personalized demo with our team and see how ExchoSoft products can transform your business operations.</p>
            <a href="{{ route('site.book-demo') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-3 text-sm font-semibold text-white hover:bg-cyan-400 transition-colors shadow-lg shadow-cyan-500/25">
                Book a Free Demo
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>
        </div>
    </section>

</div>
