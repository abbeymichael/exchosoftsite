<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('My Orders — ExchoSoft')] class extends Component
{
    use WithPagination;
    public ?int $viewId = null;

    public function render(): \Illuminate\View\View
    {
        $orders = Order::where('customer_user_id', auth()->id())
            ->with('items.shopProduct')
            ->latest()->paginate(10);

        $viewOrder = $this->viewId
            ? Order::where('customer_user_id', auth()->id())->with('items.shopProduct')->find($this->viewId)
            : null;

        return view('livewire.pages.customer.orders', compact('orders', 'viewOrder'));
    }
}; ?>

<div class="py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('customer.dashboard') }}" wire:navigate class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">My Orders</h1>
        </div>

        @if($orders->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-12 text-center">
            <svg class="mx-auto h-14 w-14 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <p class="font-semibold text-slate-600">No orders yet</p>
            <p class="text-sm text-slate-400 mt-1 mb-5">Start shopping to see your orders here.</p>
            <a href="{{ route('site.products') }}" wire:navigate class="inline-flex rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">Browse Products</a>
        </div>
        @else
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Order</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Total</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Payment</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs font-semibold text-slate-700">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-400">{{ $order->items->count() }} item(s)</p>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">GHS {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : ($order->payment_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <button wire:click="$set('viewId', {{ $order->id }})" class="text-xs text-cyan-600 hover:underline">View</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($orders->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $orders->links() }}</div>@endif
        </div>
        @endif

        {{-- Order Detail Modal --}}
        @if($viewOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60" wire:click="$set('viewId', null)"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-y-auto max-h-[90vh] p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-900">Order Detail</h3>
                        <p class="text-xs font-mono text-slate-400">{{ $viewOrder->order_number }}</p>
                    </div>
                    <button wire:click="$set('viewId', null)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="space-y-2">
                        @foreach($viewOrder->items as $item)
                        <div class="flex justify-between items-center bg-slate-50 rounded-xl p-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                @if($item->license_key_issued)
                                    <p class="text-xs font-mono text-cyan-600 mt-0.5">Key: {{ $item->license_key_issued }}</p>
                                @endif
                            </div>
                            <p class="font-semibold text-slate-900">GHS {{ number_format($item->total, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 space-y-1">
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Total</span><span class="font-bold">GHS {{ number_format($viewOrder->total, 2) }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Payment</span><span class="capitalize">{{ $viewOrder->payment_status }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Status</span><span class="capitalize">{{ $viewOrder->status }}</span></div>
                    </div>
                    @if($viewOrder->payment_status === 'unpaid')
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                        Payment pending. We'll contact you with payment instructions via email.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
