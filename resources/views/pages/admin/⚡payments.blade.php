<?php

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Payments — ExchoSoft')] class extends Component {
    use WithPagination;

    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterGateway = '';
    public ?string $viewId       = null;

    // Manual payment form
    public bool   $showForm      = false;
    public string $order_id      = '';
    public string $gateway       = 'manual';
    public string $amount        = '0.00';
    public string $currency      = 'USD';
    public string $gateway_transaction_id = '';
    public string $status        = 'completed';
    public string $paid_at       = '';
    public string $notes         = '';

    public function viewPayment(string $id): void
    {
        $this->viewId = $id;
    }

    public function closeView(): void
    {
        $this->viewId = null;
    }

    public function openManualPayment(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function saveManualPayment(): void
    {
        $this->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway'  => 'required|string',
            'amount'   => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status'   => 'required|string',
        ]);

        $payment = Payment::create([
            'order_id'                => $this->order_id,
            'user_id'                 => auth()->id(),
            'gateway'                 => $this->gateway,
            'gateway_transaction_id'  => $this->gateway_transaction_id ?: null,
            'amount'                  => $this->amount,
            'net'                     => $this->amount,
            'currency'                => $this->currency,
            'status'                  => $this->status,
            'paid_at'                 => $this->status === 'completed' ? ($this->paid_at ?: now()) : null,
            'metadata'                => ['notes' => $this->notes ?: null, 'recorded_by' => auth()->id()],
        ]);

        // If completed, update the order's payment status
        if ($payment->status === 'completed') {
            $payment->order()->update([
                'payment_status' => 'paid',
                'paid_at'        => $payment->paid_at ?? now(),
                'payment_method' => $this->gateway,
                'payment_reference' => $this->gateway_transaction_id ?: null,
                'status'         => 'processing',
            ]);
        }

        session()->flash('success', 'Payment recorded.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function markRefunded(string $id): void
    {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'status'          => 'refunded',
            'refunded_amount' => $payment->amount,
            'refunded_at'     => now(),
        ]);
        $payment->order()->update(['payment_status' => 'refunded', 'status' => 'refunded']);
        session()->flash('success', 'Payment marked as refunded.');
    }

    public function resetForm(): void
    {
        $this->order_id                 = '';
        $this->gateway                  = 'manual';
        $this->amount                   = '0.00';
        $this->currency                 = 'USD';
        $this->gateway_transaction_id   = '';
        $this->status                   = 'completed';
        $this->paid_at                  = '';
        $this->notes                    = '';
        $this->resetValidation();
    }

    #[Computed]
    public function payments()
    {
        return Payment::with(['order', 'user'])
            ->when($this->search, fn ($q) => $q
                ->where('gateway_transaction_id', 'like', '%' . $this->search . '%')
                ->orWhere('gateway_reference', 'like', '%' . $this->search . '%')
                ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', '%' . $this->search . '%'))
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', '%' . $this->search . '%')))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterGateway, fn ($q) => $q->where('gateway', $this->filterGateway))
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function viewPaymentData()
    {
        return $this->viewId
            ? Payment::with(['order.items.product', 'order.items.plan', 'user', 'resellerCommissions.reseller'])->find($this->viewId)
            : null;
    }

    #[Computed]
    public function stats()
    {
        return [
            'total'     => Payment::where('status', 'completed')->sum('amount'),
            'today'     => Payment::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'pending'   => Payment::where('status', 'pending')->count(),
            'refunded'  => Payment::where('status', 'refunded')->sum('refunded_amount'),
        ];
    }
}; ?>

<div>
    <x-slot:heading>Payments</x-slot:heading>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Revenue</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($this->stats['total'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Today</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ number_format($this->stats['today'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $this->stats['pending'] }}</p>
            </div>
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Refunded</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ number_format($this->stats['refunded'], 2) }}</p>
            </div>
        </div>

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Order # or transaction ID..."
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                    <option value="disputed">Disputed</option>
                </select>
                <select wire:model.live="filterGateway" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Gateways</option>
                    <option value="stripe">Stripe</option>
                    <option value="paypal">PayPal</option>
                    <option value="paystack">Paystack</option>
                    <option value="flutterwave">Flutterwave</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <button wire:click="openManualPayment"
                class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Record Payment
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Payment</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Order</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Gateway</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->payments as $payment)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div>
                                        <p class="font-mono text-xs text-slate-700">{{ substr($payment->id, 0, 8) }}…</p>
                                        @if ($payment->gateway_transaction_id)
                                            <p class="text-xs text-slate-500 font-mono">{{ $payment->gateway_transaction_id }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $payment->order?->order_number }}</p>
                                        <p class="text-xs text-slate-500">{{ $payment->user?->name ?? $payment->guest_email ?? '—' }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ ucfirst($payment->gateway) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
                                        @if ($payment->fee > 0)
                                            <p class="text-xs text-slate-500">Fee: {{ number_format($payment->fee, 2) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $statusColor = match($payment->status) {
                                            'completed'          => 'green',
                                            'pending'            => 'amber',
                                            'processing'         => 'blue',
                                            'failed'             => 'red',
                                            'refunded'           => 'slate',
                                            'partially_refunded' => 'orange',
                                            'disputed'           => 'violet',
                                            default              => 'gray',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                                        {{ ucwords(str_replace('_', ' ', $payment->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center text-xs text-slate-600">
                                    {{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="viewPayment('{{ $payment->id }}')"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="View">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        @if ($payment->status === 'completed')
                                            <button wire:click="markRefunded('{{ $payment->id }}')"
                                                wire:confirm="Mark this payment as refunded?"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Refund">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->payments->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">{{ $this->payments->links() }}</div>
            @endif
        </div>
    </div>

    {{-- View Modal --}}
    @if ($viewId && $this->viewPaymentData)
        @php $p = $this->viewPaymentData; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="closeView"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Payment Details</h2>
                    <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Order</p>
                            <p class="font-semibold text-slate-900">{{ $p->order?->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Status</p>
                            <p class="font-semibold capitalize">{{ str_replace('_', ' ', $p->status) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Amount</p>
                            <p class="font-semibold text-slate-900">{{ $p->currency }} {{ number_format($p->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Net</p>
                            <p class="font-semibold text-slate-900">{{ number_format($p->net, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Gateway</p>
                            <p class="font-semibold capitalize">{{ $p->gateway }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Date</p>
                            <p class="text-slate-700">{{ $p->paid_at?->format('d M Y H:i') ?? $p->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @if ($p->gateway_transaction_id)
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Transaction ID</p>
                            <p class="font-mono text-sm bg-slate-50 rounded-lg px-3 py-2 text-slate-700">{{ $p->gateway_transaction_id }}</p>
                        </div>
                    @endif
                    @if ($p->order && $p->order->items->count())
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Order Items</p>
                            <div class="space-y-2">
                                @foreach ($p->order->items as $item)
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $item->product_name }}</p>
                                            @if ($item->plan_name)
                                                <p class="text-xs text-slate-500">{{ $item->plan_name }}</p>
                                            @endif
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">{{ number_format($item->total, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($p->resellerCommissions->count())
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Reseller Commissions</p>
                            @foreach ($p->resellerCommissions as $commission)
                                <div class="flex items-center justify-between rounded-xl bg-amber-50 px-3 py-2 text-sm">
                                    <p>{{ $commission->reseller?->display_name ?? 'Reseller' }}</p>
                                    <p class="font-semibold text-amber-700">{{ $commission->currency }} {{ number_format($commission->commission_amount, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Manual Payment Form --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl overflow-y-auto">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Record Manual Payment</h2>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit="saveManualPayment" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Order ID <span class="text-red-500">*</span></label>
                        <input wire:model="order_id" type="text" placeholder="Order UUID or numeric ID"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('order_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Gateway</label>
                            <select wire:model="gateway" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="manual">Manual</option>
                                <option value="stripe">Stripe</option>
                                <option value="paypal">PayPal</option>
                                <option value="paystack">Paystack</option>
                                <option value="flutterwave">Flutterwave</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount <span class="text-red-500">*</span></label>
                            <input wire:model="amount" type="number" step="0.01" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Currency</label>
                            <input wire:model="currency" type="text" maxlength="3"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Transaction Reference</label>
                        <input wire:model="gateway_transaction_id" type="text" placeholder="Optional"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
