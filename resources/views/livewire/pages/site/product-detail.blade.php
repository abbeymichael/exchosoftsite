<?php

use App\Models\ShopProduct;
use App\Models\DemoBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public ShopProduct $product;

    public bool $showPurchaseModal = false;
    public bool $purchaseSuccess = false;

    // Guest checkout fields
    public string $buyerName = '';
    public string $buyerEmail = '';
    public string $buyerPhone = '';
    public string $buyerNote = '';

    public function mount(string $slug): void
    {
        $this->product = ShopProduct::published()->where('slug', $slug)->firstOrFail();
    }

    public function getTitle(): string
    {
        return $this->product->name . ' — ExchoSoft';
    }

    public function openPurchase(): void
    {
        if (auth()->check()) {
            $this->buyerName  = auth()->user()->name;
            $this->buyerEmail = auth()->user()->email;
        }
        $this->showPurchaseModal = true;
    }

    public function purchase(): void
    {
        $this->validate([
            'buyerName'  => 'required|string|max:200',
            'buyerEmail' => 'required|email',
        ]);

        $price = $this->product->effective_price;

        $order = \App\Models\Order::create([
            'customer_user_id' => auth()->id(),
            'guest_name'       => auth()->check() ? null : $this->buyerName,
            'guest_email'      => auth()->check() ? null : $this->buyerEmail,
            'guest_phone'      => $this->buyerPhone,
            'subtotal'         => $price,
            'total'            => $price,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'customer_note'    => $this->buyerNote,
        ]);

        $order->items()->create([
            'shop_product_id' => $this->product->id,
            'product_name'    => $this->product->name,
            'product_version' => $this->product->version,
            'unit_price'      => $price,
            'quantity'        => 1,
            'total'           => $price,
        ]);

        $this->showPurchaseModal = false;
        $this->purchaseSuccess = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.product-detail')
            ->title($this->product->name . ' — ExchoSoft');
    }
}; ?>

<div>
    {{-- Breadcrumb --}}
    <div class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-slate-900">Home</a>
                <span>/</span>
                <a href="{{ route('site.products') }}" wire:navigate class="hover:text-slate-900">Products</a>
                <span>/</span>
                <span class="text-slate-900 font-medium">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    @if($purchaseSuccess)
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-8">
        <div class="rounded-2xl bg-green-50 border border-green-200 p-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 mb-4">
                <svg class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-xl font-bold text-green-800 mb-2">Order Placed!</h2>
            <p class="text-green-700 mb-4">Your order has been placed successfully. We'll contact you with payment details shortly.</p>
            <a href="{{ route('home') }}" wire:navigate class="inline-flex rounded-xl bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">Back to Home</a>
        </div>
    </div>
    @endif

    {{-- Product Detail --}}
    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-2">
                {{-- Image --}}
                <div class="rounded-2xl bg-gradient-to-br from-slate-100 to-slate-50 aspect-square flex items-center justify-center overflow-hidden">
                    @if($product->cover_image)
                        <img src="{{ asset('storage/'.$product->cover_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <div class="flex flex-col items-center gap-3 text-slate-300">
                            <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm">Product Image Placeholder</p>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $product->category }}</span>
                        @if($product->platform)<span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700">{{ $product->platform }}</span>@endif
                        @if($product->version)<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">v{{ $product->version }}</span>@endif
                    </div>

                    <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ $product->name }}</h1>
                    @if($product->tagline)<p class="text-lg text-slate-500 mb-4">{{ $product->tagline }}</p>@endif
                    @if($product->description)<p class="text-slate-600 mb-6 leading-relaxed">{{ $product->description }}</p>@endif

                    {{-- Pricing --}}
                    <div class="flex items-end gap-3 mb-6">
                        @if($product->is_on_sale)
                            <p class="text-2xl font-bold text-green-600">GHS {{ number_format($product->sale_price, 2) }}</p>
                            <p class="text-lg text-slate-400 line-through mb-0.5">GHS {{ number_format($product->price, 2) }}</p>
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">SALE</span>
                        @else
                            <p class="text-2xl font-bold text-slate-900">GHS {{ number_format($product->price, 2) }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button wire:click="openPurchase" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-6 py-3 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-md shadow-cyan-500/25">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Buy Now
                        </button>
                        @if($product->demo_url)
                        <a href="{{ $product->demo_url }}" target="_blank"
                           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Live Demo
                        </a>
                        @endif
                        <a href="{{ route('site.book-demo') }}" wire:navigate
                           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Book a Demo
                        </a>
                    </div>

                    {{-- Features --}}
                    @if($product->features)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Key Features</p>
                        <ul class="space-y-1.5">
                            @foreach($product->features as $feature)
                            <li class="flex items-start gap-2 text-sm text-slate-700">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($product->documentation_url)
                    <a href="{{ $product->documentation_url }}" target="_blank" class="inline-flex items-center gap-1.5 mt-4 text-sm text-cyan-600 hover:underline">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        View Documentation
                    </a>
                    @endif
                </div>
            </div>

            {{-- Full Description --}}
            @if($product->full_description)
            <div class="mt-14 prose prose-slate max-w-none">
                {!! $product->full_description !!}
            </div>
            @endif
        </div>
    </section>

    {{-- Purchase Modal --}}
    @if($showPurchaseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60" wire:click="$set('showPurchaseModal', false)"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-1">Complete Purchase</h3>
            <p class="text-sm text-slate-500 mb-4">{{ $product->name }} — GHS {{ number_format($product->effective_price, 2) }}</p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name *</label>
                    <input wire:model="buyerName" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('buyerName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address *</label>
                    <input wire:model="buyerEmail" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('buyerEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone</label>
                    <input wire:model="buyerPhone" type="tel" placeholder="Optional" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Notes</label>
                    <textarea wire:model="buyerNote" rows="2" placeholder="Any specific requirements..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
                <p class="text-xs text-slate-400">We'll send you payment instructions and your license key once the order is confirmed.</p>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="purchase" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">Place Order</button>
                <button wire:click="$set('showPurchaseModal', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
