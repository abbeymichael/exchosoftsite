<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Orders — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPayment = '';
    public ?int $viewId = null;

    public function viewOrder(int $id): void
    {
        $this->viewId = $id;
    }

    public function closeView(): void
    {
        $this->viewId = null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        if ($status === 'completed') {
            $order->update(['fulfillment_status' => 'fulfilled', 'fulfilled_at' => now()]);
        }
        session()->flash('success', 'Order status updated.');
    }

    public function markPaid(int $id): void
    {
        Order::findOrFail($id)->update([
            'payment_status' => 'paid',
            'status'         => 'processing',
            'paid_at'        => now(),
        ]);
        session()->flash('success', 'Order marked as paid.');
    }

    public function render(): \Illuminate\View\View
    {
        $orders = Order::with(['customerUser', 'items'])
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', '%'.$this->search.'%')
                  ->orWhere('guest_email', 'like', '%'.$this->search.'%')
                  ->orWhereHas('customerUser', fn($u) => $u->where('email', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->latest()
            ->paginate(15);

        $viewOrder = $this->viewId ? Order::with(['customerUser', 'items.shopProduct'])->find($this->viewId) : null;

        $stats = [
            'total'    => Order::count(),
            'pending'  => Order::where('status', 'pending')->count(),
            'paid'     => Order::where('payment_status', 'paid')->count(),
            'revenue'  => Order::where('payment_status', 'paid')->sum('total'),
        ];

        return view('livewire.pages.admin.orders', compact('orders', 'viewOrder', 'stats'));
    }
}; ?>

<div>
    <x-slot:heading>Orders</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</p>
                <p class="text-sm text-slate-500">Total Orders</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-amber-600">{{ number_format($stats['pending']) }}</p>
                <p class="text-sm text-slate-500">Pending</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['paid']) }}</p>
                <p class="text-sm text-slate-500">Paid Orders</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-cyan-600">GHS {{ number_format($stats['revenue'], 2) }}</p>
                <p class="text-sm text-slate-500">Total Revenue</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Order # or email..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
            </div>
            <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select wire:model.live="filterPayment" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                <option value="">All Payments</option>
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Total</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Payment</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                        @php
                            $statusColors = ['pending'=>'amber','processing'=>'blue','completed'=>'green','cancelled'=>'red','refunded'=>'slate'];
                            $sc = $statusColors[$order->status] ?? 'slate';
                            $payColors = ['unpaid'=>'red','paid'=>'green','failed'=>'red','refunded'=>'slate'];
                            $pc = $payColors[$order->payment_status] ?? 'slate';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold font-mono text-slate-900 text-xs">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-400">{{ $order->items->count() }} item(s)</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-900">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-400">{{ $order->customer_email }}</p>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-900">GHS {{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $pc }}-100 text-{{ $pc }}-700 capitalize">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $sc }}-100 text-{{ $sc }}-700 capitalize">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($order->payment_status !== 'paid')
                                    <button wire:click="markPaid({{ $order->id }})" class="rounded-lg px-2 py-1 text-xs font-medium text-green-600 hover:bg-green-50 transition-colors">Mark Paid</button>
                                    @endif
                                    <button wire:click="viewOrder({{ $order->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                        </button>
                                        <div x-show="open" @click.away="open=false" class="absolute right-0 mt-1 w-40 rounded-xl bg-white shadow-lg ring-1 ring-slate-100 z-20 py-1">
                                            <button wire:click="updateStatus({{ $order->id }}, 'processing')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50">Set Processing</button>
                                            <button wire:click="updateStatus({{ $order->id }}, 'completed')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50">Set Completed</button>
                                            <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Cancel Order</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No orders found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Order Detail Slide-over --}}
    @if($viewOrder)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="closeView"></div>
        <div class="relative ml-auto w-full max-w-lg bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Order Detail</h2>
                    <p class="text-xs font-mono text-slate-400">{{ $viewOrder->order_number }}</p>
                </div>
                <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-5">
                {{-- Customer --}}
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Customer</p>
                    <p class="font-semibold text-slate-900">{{ $viewOrder->customer_name }}</p>
                    <p class="text-sm text-slate-500">{{ $viewOrder->customer_email }}</p>
                    @if($viewOrder->guest_phone) <p class="text-sm text-slate-500">{{ $viewOrder->guest_phone }}</p> @endif
                    @if($viewOrder->guest_company) <p class="text-sm text-slate-500">{{ $viewOrder->guest_company }}</p> @endif
                </div>
                {{-- Items --}}
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Items</p>
                    <div class="space-y-2">
                        @foreach($viewOrder->items as $item)
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                @if($item->product_version) <p class="text-xs text-slate-400">v{{ $item->product_version }}</p> @endif
                                @if($item->license_key_issued) <p class="text-xs font-mono text-cyan-600 mt-0.5">{{ $item->license_key_issued }}</p> @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-400">{{ $item->quantity }} × GHS {{ number_format($item->unit_price, 2) }}</p>
                                <p class="font-semibold text-slate-900">GHS {{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{-- Totals --}}
                <div class="rounded-xl bg-slate-50 p-4 space-y-1.5">
                    <div class="flex justify-between text-sm text-slate-600"><span>Subtotal</span><span>GHS {{ number_format($viewOrder->subtotal, 2) }}</span></div>
                    @if($viewOrder->discount > 0)
                    <div class="flex justify-between text-sm text-green-600"><span>Discount</span><span>-GHS {{ number_format($viewOrder->discount, 2) }}</span></div>
                    @endif
                    @if($viewOrder->tax > 0)
                    <div class="flex justify-between text-sm text-slate-600"><span>Tax</span><span>GHS {{ number_format($viewOrder->tax, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-slate-900 pt-1 border-t border-slate-200"><span>Total</span><span>GHS {{ number_format($viewOrder->total, 2) }}</span></div>
                </div>
                {{-- Meta --}}
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-xs text-slate-500">Payment Method</p><p class="font-medium text-slate-900 capitalize">{{ $viewOrder->payment_method ?? 'N/A' }}</p></div>
                    <div><p class="text-xs text-slate-500">Payment Status</p><p class="font-medium text-slate-900 capitalize">{{ $viewOrder->payment_status }}</p></div>
                    <div><p class="text-xs text-slate-500">Order Status</p><p class="font-medium text-slate-900 capitalize">{{ $viewOrder->status }}</p></div>
                    <div><p class="text-xs text-slate-500">Fulfillment</p><p class="font-medium text-slate-900 capitalize">{{ $viewOrder->fulfillment_status }}</p></div>
                    @if($viewOrder->paid_at)
                    <div><p class="text-xs text-slate-500">Paid At</p><p class="font-medium text-slate-900">{{ $viewOrder->paid_at->format('d M Y H:i') }}</p></div>
                    @endif
                </div>
                @if($viewOrder->customer_note)
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Customer Note</p><p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">{{ $viewOrder->customer_note }}</p></div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
