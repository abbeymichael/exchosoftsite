<?php

use App\Models\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Subscriptions — ExchoLicense')] class extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterCycle  = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCycle(): void
    {
        $this->resetPage();
    }

    public function cancel(int $id): void
    {
        Subscription::findOrFail($id)->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
        session()->flash('success', 'Subscription cancelled.');
    }

    #[Computed]
    public function subscriptions()
    {
        return Subscription::query()
            ->with(['license.product', 'license.customer'])
            ->when($this->search, fn ($q) => $q
                ->whereHas('license', fn ($l) => $l
                    ->where('license_key', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"))))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCycle, fn ($q) => $q->where('billing_cycle', $this->filterCycle))
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'active'    => Subscription::where('status', 'active')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'past_due'  => Subscription::where('status', 'past_due')->count(),
            'total_mrr' => Subscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->sum('amount'),
        ];
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Subscriptions</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-green-600">{{ number_format($this->stats['active']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Active</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-slate-500">{{ number_format($this->stats['cancelled']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Cancelled</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-amber-600">{{ number_format($this->stats['past_due']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Past Due</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-cyan-600">${{ number_format($this->stats['total_mrr'], 2) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Monthly MRR</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <input type="text"
                   wire:model.live="search"
                   placeholder="Search license key or customer…"
                   class="w-full sm:w-72 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            <select wire:model.live="filterStatus"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="cancelled">Cancelled</option>
                <option value="past_due">Past Due</option>
            </select>
            <select wire:model.live="filterCycle"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">All Billing Cycles</option>
                <option value="monthly">Monthly</option>
                <option value="annual">Annual</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">License</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Cycle</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Renewal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->subscriptions as $subscription)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 text-sm font-semibold text-slate-900">
                                {{ $subscription->license?->customer?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-600">
                                {{ $subscription->license?->product?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm font-mono text-slate-600">
                                {{ $subscription->license?->license_key ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-slate-900">
                                ${{ number_format($subscription->amount, 2) }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $subscription->billing_cycle === 'annual' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ucfirst($subscription->billing_cycle) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-500">
                                {{ $subscription->next_billing_date ? $subscription->next_billing_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ match($subscription->status) {
                                        'active'    => 'bg-green-50 text-green-700',
                                        'cancelled' => 'bg-slate-100 text-slate-600',
                                        'past_due'  => 'bg-amber-50 text-amber-700',
                                        default     => 'bg-slate-100 text-slate-600',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                @if($subscription->status === 'active')
                                    <button wire:click="cancel({{ $subscription->id }})"
                                            wire:confirm="Cancel this subscription? The customer will lose access at the next billing date."
                                            class="text-sm font-medium text-red-600 hover:text-red-700">
                                        Cancel
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-400">
                                No subscriptions found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $this->subscriptions->links() }}
        </div>

    </div>

</div>
