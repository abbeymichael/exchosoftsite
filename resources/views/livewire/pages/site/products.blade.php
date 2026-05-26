<?php

use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Products — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public function render(): \Illuminate\View\View
    {
        $products = ShopProduct::published()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('sort_order')->latest()
            ->paginate(12);

        return view('livewire.pages.site.products', compact('products'));
    }
}; ?>

<div>
    {{-- Header --}}
    <section class="bg-slate-900 text-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Software Products</p>
            <h1 class="text-4xl font-bold mb-4">🚧 Products Page — Placeholder</h1>
            <p class="text-slate-400 max-w-xl mx-auto">Browse our software products, tools, and services. This page pulls live data from your admin panel.</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <div class="relative flex-1 sm:flex-none">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..." class="pl-9 pr-4 py-2 w-full sm:w-60 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                </div>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="software">Software</option>
                    <option value="template">Template</option>
                    <option value="course">Course</option>
                    <option value="service">Service</option>
                </select>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-20 text-slate-400">
                <svg class="mx-auto h-14 w-14 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-lg font-semibold">No products found</p>
                <p class="text-sm mt-1">Products added from the admin panel will appear here.</p>
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate
                   class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden">
                    <div class="bg-gradient-to-br from-slate-100 to-slate-50 h-40 flex items-center justify-center relative">
                        @if($product->cover_image)
                            <img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                        @if($product->is_on_sale)
                            <span class="absolute top-2 right-2 rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">SALE</span>
                        @endif
                        @if($product->is_featured)
                            <span class="absolute top-2 left-2 rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-white">⭐ Featured</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-medium text-slate-500 capitalize">{{ $product->category }}</span>
                        <p class="font-bold text-slate-900 mt-1 group-hover:text-cyan-700 transition-colors">{{ $product->name }}</p>
                        @if($product->tagline)<p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $product->tagline }}</p>@endif
                        <div class="flex items-center justify-between mt-4">
                            <div>
                                @if($product->is_on_sale)
                                    <p class="text-xs text-slate-400 line-through">GHS {{ number_format($product->price, 2) }}</p>
                                    <p class="text-sm font-bold text-green-600">GHS {{ number_format($product->sale_price, 2) }}</p>
                                @else
                                    <p class="text-sm font-bold text-slate-900">GHS {{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-lg bg-cyan-50 px-2 py-1 text-xs font-medium text-cyan-700">View →</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($products->hasPages())<div class="mt-10">{{ $products->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
