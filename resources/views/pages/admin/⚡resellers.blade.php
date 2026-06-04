<?php

use App\Models\Reseller;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Resellers — ExchoSoft')] class extends Component {
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterType   = '';

    public bool    $showForm  = false;
    public bool    $editMode  = false;
    public ?string $editId    = null;
    public ?string $viewId    = null;

    // Form fields
    public string  $user_id         = '';
    public string  $company_name    = '';
    public string  $reseller_code   = '';
    public string  $type            = 'referral';
    public string  $commission_rate = '0.00';
    public string  $discount_rate   = '0.00';
    public string  $status          = 'pending';
    public string  $payout_method   = '';
    public string  $currency        = 'USD';
    public string  $minimum_payout  = '50.00';
    public string  $notes           = '';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $r = Reseller::findOrFail($id);
        $this->editId         = $id;
        $this->user_id        = $r->user_id;
        $this->company_name   = $r->company_name ?? '';
        $this->reseller_code  = $r->reseller_code;
        $this->type           = $r->type;
        $this->commission_rate= $r->commission_rate;
        $this->discount_rate  = $r->discount_rate;
        $this->status         = $r->status;
        $this->payout_method  = $r->payout_method ?? '';
        $this->currency       = $r->currency;
        $this->minimum_payout = $r->minimum_payout;
        $this->notes          = $r->notes ?? '';
        $this->showForm       = true;
        $this->editMode       = true;
    }



    public function closeView(): void
    {
        $this->viewId = null;
    }

    public function save(): void
    {
        $this->validate([
            'user_id'         => 'required|exists:users,id',
            'reseller_code'   => 'required|string|max:32|unique:resellers,reseller_code' . ($this->editMode ? ',' . $this->editId : ''),
            'type'            => 'required|in:wholesale,referral,both',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'discount_rate'   => 'required|numeric|min:0|max:100',
            'status'          => 'required|in:pending,active,suspended',
            'minimum_payout'  => 'required|numeric|min:0',
            'currency'        => 'required|string|max:3',
        ]);

        $data = [
            'user_id'         => $this->user_id,
            'company_name'    => $this->company_name ?: null,
            'reseller_code'   => strtoupper($this->reseller_code),
            'type'            => $this->type,
            'commission_rate' => $this->commission_rate,
            'discount_rate'   => $this->discount_rate,
            'status'          => $this->status,
            'payout_method'   => $this->payout_method ?: null,
            'currency'        => $this->currency,
            'minimum_payout'  => $this->minimum_payout,
            'notes'           => $this->notes ?: null,
        ];

        if ($this->editMode) {
            $reseller = Reseller::findOrFail($this->editId);
            // Auto-set approved_at when activating
            if ($data['status'] === 'active' && $reseller->status !== 'active') {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
            $reseller->update($data);
            session()->flash('success', 'Reseller updated.');
        } else {
            if ($data['status'] === 'active') {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
            Reseller::create($data);
            session()->flash('success', 'Reseller created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function approve(string $id): void
    {
        Reseller::findOrFail($id)->update([
            'status'      => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        session()->flash('success', 'Reseller approved.');
    }

    public function suspend(string $id): void
    {
        Reseller::findOrFail($id)->update(['status' => 'suspended']);
        session()->flash('success', 'Reseller suspended.');
    }

    public function delete(string $id): void
    {
        Reseller::findOrFail($id)->delete();
        session()->flash('success', 'Reseller removed.');
    }

    public function resetForm(): void
    {
        $this->user_id         = '';
        $this->company_name    = '';
        $this->reseller_code   = '';
        $this->type            = 'referral';
        $this->commission_rate = '0.00';
        $this->discount_rate   = '0.00';
        $this->status          = 'pending';
        $this->payout_method   = '';
        $this->currency        = 'USD';
        $this->minimum_payout  = '50.00';
        $this->notes           = '';
        $this->editId          = null;
        $this->resetValidation();
    }

    #[Computed]
    public function resellers()
    {
        return Reseller::with('user')
            ->when($this->search, fn ($q) => $q
                ->where('company_name', 'like', '%' . $this->search . '%')
                ->orWhere('reseller_code', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function users()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function viewReseller()
    {
        return $this->viewId ? Reseller::with(['user', 'commissions', 'payouts', 'orders', 'licenses'])->find($this->viewId) : null;
    }
}; ?>

<div>
    <x-slot:heading>Resellers</x-slot:heading>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search resellers..."
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-48">
                </div>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
                <select wire:model.live="filterType" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Types</option>
                    <option value="referral">Referral</option>
                    <option value="wholesale">Wholesale</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Reseller
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Reseller</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Commission</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Balance</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->resellers as $reseller)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $reseller->display_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $reseller->user?->email }} · <code class="bg-slate-100 px-1 rounded text-xs">{{ $reseller->reseller_code }}</code></p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $typeColor = match($reseller->type) {
                                            'referral'  => 'blue',
                                            'wholesale' => 'purple',
                                            'both'      => 'teal',
                                            default     => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $typeColor }}-100 text-{{ $typeColor }}-700">
                                        {{ ucfirst($reseller->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="text-xs text-slate-600 space-y-0.5">
                                        @if (in_array($reseller->type, ['referral', 'both']))
                                            <p>Ref: {{ $reseller->commission_rate }}%</p>
                                        @endif
                                        @if (in_array($reseller->type, ['wholesale', 'both']))
                                            <p>Disc: {{ $reseller->discount_rate }}%</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $reseller->currency }} {{ number_format($reseller->balance, 2) }}</p>
                                        <p class="text-xs text-slate-500">Earned: {{ number_format($reseller->total_earned, 2) }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $statusColor = match($reseller->status) {
                                            'active'    => 'green',
                                            'pending'   => 'amber',
                                            'suspended' => 'red',
                                            default     => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                                        {{ ucfirst($reseller->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="viewReseller('{{ $reseller->id }}')"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="View">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        @if ($reseller->status === 'pending')
                                            <button wire:click="approve('{{ $reseller->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-green-50 hover:text-green-600 transition-colors" title="Approve">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif
                                        <button wire:click="openEdit('{{ $reseller->id }}')"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete('{{ $reseller->id }}')"
                                            wire:confirm="Remove this reseller?"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No resellers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->resellers->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">{{ $this->resellers->links() }}</div>
            @endif
        </div>
    </div>

    {{-- View Modal --}}
    @if ($viewId && $this->viewReseller)
        @php $r = $this->viewReseller; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="closeView"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-screen">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-900">{{ $r->display_name }}</h2>
                    <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Account</p>
                            <p class="font-semibold text-slate-900">{{ $r->user?->name }}</p>
                            <p class="text-sm text-slate-600">{{ $r->user?->email }}</p>
                            <p class="mt-2 text-xs"><code class="bg-white border border-slate-200 px-2 py-0.5 rounded">{{ $r->reseller_code }}</code></p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Financials</p>
                            <p class="text-sm text-slate-700">Total Earned: <span class="font-semibold">{{ $r->currency }} {{ number_format($r->total_earned, 2) }}</span></p>
                            <p class="text-sm text-slate-700">Total Paid: <span class="font-semibold">{{ number_format($r->total_paid, 2) }}</span></p>
                            <p class="text-sm text-slate-700">Balance: <span class="font-bold text-green-700">{{ number_format($r->balance, 2) }}</span></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->orders->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Referred Orders</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->licenses->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Licenses Issued</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->commissions->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Commissions</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        @if ($r->status === 'pending')
                            <button wire:click="approve('{{ $r->id }}')" wire:click="closeView"
                                class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                Approve Reseller
                            </button>
                        @endif
                        @if ($r->status === 'active')
                            <button wire:click="suspend('{{ $r->id }}')"
                                class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                                Suspend
                            </button>
                        @endif
                        <button wire:click="openEdit('{{ $r->id }}')" wire:click="closeView"
                            class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Create/Edit Slide-over --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl overflow-y-auto">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-900">{{ $editMode ? 'Edit Reseller' : 'Add Reseller' }}</h2>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="px-6 py-5 space-y-5">
                    {{-- User --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">User Account <span class="text-red-500">*</span></label>
                        <select wire:model="user_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="">Select user...</option>
                            @foreach ($this->users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                            <input wire:model="company_name" type="text" placeholder="Optional"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Reseller Code <span class="text-red-500">*</span></label>
                            <input wire:model="reseller_code" type="text" placeholder="e.g. PARTNER01"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm uppercase focus:outline-none focus:border-cyan-400">
                            @error('reseller_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Reseller Type <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['referral' => ['Referral', 'Earns % on referred sales'], 'wholesale' => ['Wholesale', 'Buys batches at discount'], 'both' => ['Both', 'Referral + Wholesale']] as $val => [$label, $desc])
                                <label class="flex flex-col rounded-xl border-2 cursor-pointer p-3 transition-colors
                                    {{ $type === $val ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" wire:model="type" value="{{ $val }}" class="sr-only">
                                    <span class="text-sm font-semibold {{ $type === $val ? 'text-cyan-700' : 'text-slate-700' }}">{{ $label }}</span>
                                    <span class="text-xs text-slate-500 mt-0.5">{{ $desc }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rates --}}
                    <div class="grid grid-cols-2 gap-4">
                        @if (in_array($type, ['referral', 'both']))
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Commission Rate (%)</label>
                                <input wire:model="commission_rate" type="number" step="0.01" min="0" max="100"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('commission_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                        @if (in_array($type, ['wholesale', 'both']))
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Wholesale Discount (%)</label>
                                <input wire:model="discount_rate" type="number" step="0.01" min="0" max="100"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('discount_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Status & Payout --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Payout Method</label>
                            <select wire:model="payout_method" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">Not set</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="crypto">Crypto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Currency</label>
                            <input wire:model="currency" type="text" maxlength="3" placeholder="USD"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Min Payout Amount</label>
                            <input wire:model="minimum_payout" type="number" step="0.01" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            {{ $editMode ? 'Update Reseller' : 'Add Reseller' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
