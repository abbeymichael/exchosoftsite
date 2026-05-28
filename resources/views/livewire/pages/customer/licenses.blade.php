<?php

use App\Models\License;
use App\Models\OrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('My Licenses — ExchoSoft')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        // Get licenses from ExchoLicense module linked by customer email
        $email = auth()->user()->email;

        $licenses = License::with(['product', 'activations'])
            ->whereHas('customer', fn($q) => $q->where('email', $email))
            ->latest()->get();

        // Also get licenses issued directly via orders
        $orderKeys = OrderItem::whereNotNull('license_key_issued')
            ->whereHas('order', fn($q) => $q->where('customer_user_id', auth()->id()))
            ->with('shopProduct', 'order')
            ->get();

        return view('pages.customer.licenses', compact('licenses', 'orderKeys'));
    }
}; ?>

<div class="py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('customer.dashboard') }}" wire:navigate class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">My Licenses</h1>
        </div>

        @if($licenses->isEmpty() && $orderKeys->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-12 text-center">
            <svg class="mx-auto h-14 w-14 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <p class="font-semibold text-slate-600">No licenses yet</p>
            <p class="text-sm text-slate-400 mt-1 mb-5">Purchase a product to receive your license keys here.</p>
            <a href="{{ route('site.products') }}" wire:navigate class="inline-flex rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">Browse Products</a>
        </div>
        @else

            {{-- Formal Licenses from ExchoLicense --}}
            @if($licenses->isNotEmpty())
            <div class="mb-6">
                <h2 class="text-sm font-semibold uppercase text-slate-500 tracking-wide mb-3">Software Licenses</h2>
                <div class="space-y-3">
                    @foreach($licenses as $license)
                    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $license->product?->name ?? 'License' }}</p>
                                <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded mt-1 inline-block">{{ $license->license_key }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize
                                {{ $license->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $license->status }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-4 mt-3 text-xs text-slate-500">
                            <span>Edition: <strong class="text-slate-700 capitalize">{{ $license->edition ?? 'Standard' }}</strong></span>
                            @if($license->expires_at)
                                <span>Expires: <strong class="text-slate-700">{{ $license->expires_at->format('d M Y') }}</strong></span>
                            @else
                                <span>Expires: <strong class="text-slate-700">Lifetime</strong></span>
                            @endif
                            <span>Activations: <strong class="text-slate-700">{{ $license->activations->count() }} / {{ $license->max_activations }}</strong></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- License keys from order items --}}
            @if($orderKeys->isNotEmpty())
            <div>
                <h2 class="text-sm font-semibold uppercase text-slate-500 tracking-wide mb-3">License Keys from Orders</h2>
                <div class="space-y-3">
                    @foreach($orderKeys as $item)
                    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
                        <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                        <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded mt-1 inline-block">{{ $item->license_key_issued }}</p>
                        <p class="text-xs text-slate-400 mt-2">Order: {{ $item->order->order_number }} · {{ $item->order->created_at->format('d M Y') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
