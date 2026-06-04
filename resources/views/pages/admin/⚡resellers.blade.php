<?php

use App\Models\Reseller;
use App\Models\User;
use App\Models\LicenseBatch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Resellers — ExchoSoft')] class extends Component {
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search       = '';
    public string $filterStatus = '';
    public string $filterType   = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool    $showForm  = false;
    public bool    $editMode  = false;
    public ?string $editId    = null;
    public ?string $viewId    = null;

    // ── Form fields ───────────────────────────────────────────────────────────
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

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function updatedType(): void
    {
        // Reset irrelevant rates when type changes
        if ($this->type === 'referral') {
            $this->discount_rate = '0.00';
        } elseif ($this->type === 'wholesale') {
            $this->commission_rate = '0.00';
        }
    }

    // ── Open / Close ──────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $r = Reseller::findOrFail($id);
        $this->editId          = $id;
        $this->user_id         = $r->user_id;
        $this->company_name    = $r->company_name ?? '';
        $this->reseller_code   = $r->reseller_code;
        $this->type            = $r->type;
        $this->commission_rate = (string) $r->commission_rate;
        $this->discount_rate   = (string) $r->discount_rate;
        $this->status          = $r->status;
        $this->payout_method   = $r->payout_method ?? '';
        $this->currency        = $r->currency;
        $this->minimum_payout  = (string) $r->minimum_payout;
        $this->notes           = $r->notes ?? '';
        $this->showForm        = true;
        $this->editMode        = true;
        $this->viewId          = null;
    }

    public function openView(string $id): void
    {
        $this->viewId    = $id;
        $this->showForm  = false;
    }

    public function closeView(): void
    {
        $this->viewId = null;
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $rules = [
            'user_id'         => 'required|exists:users,id',
            'reseller_code'   => 'required|string|max:32|unique:resellers,reseller_code' . ($this->editMode ? ',' . $this->editId : ''),
            'type'            => 'required|in:wholesale,referral,both',
            'status'          => 'required|in:pending,active,suspended',
            'minimum_payout'  => 'required|numeric|min:0',
            'currency'        => 'required|string|max:3',
        ];

        // Only validate the rates that apply to the selected type
        if (in_array($this->type, ['referral', 'both'])) {
            $rules['commission_rate'] = 'required|numeric|min:0|max:100';
        }
        if (in_array($this->type, ['wholesale', 'both'])) {
            $rules['discount_rate'] = 'required|numeric|min:0|max:100';
        }

        $this->validate($rules);

        $data = [
            'user_id'         => $this->user_id,
            'company_name'    => $this->company_name ?: null,
            'reseller_code'   => strtoupper($this->reseller_code),
            'type'            => $this->type,
            'commission_rate' => in_array($this->type, ['referral', 'both']) ? $this->commission_rate : 0,
            'discount_rate'   => in_array($this->type, ['wholesale', 'both']) ? $this->discount_rate : 0,
            'status'          => $this->status,
            'payout_method'   => $this->payout_method ?: null,
            'currency'        => strtoupper($this->currency),
            'minimum_payout'  => $this->minimum_payout,
            'notes'           => $this->notes ?: null,
        ];

        if ($this->editMode) {
            $reseller = Reseller::findOrFail($this->editId);
            if ($data['status'] === 'active' && $reseller->status !== 'active') {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
            $reseller->update($data);
            session()->flash('success', 'Reseller updated successfully.');
        } else {
            if ($data['status'] === 'active') {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
            Reseller::create($data);
            session()->flash('success', 'Reseller created successfully.');
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
        session()->flash('success', 'Reseller approved and activated.');
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

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function resellers()
    {
        return Reseller::with('user')
            ->when($this->search, fn ($q) => $q
                ->where('company_name', 'like', '%' . $this->search . '%')
                ->orWhere('reseller_code', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', fn ($u) => $u
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType,   fn ($q) => $q->where('type', $this->filterType))
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
        return $this->viewId
            ? Reseller::with(['user', 'commissions' => fn($q) => $q->latest()->limit(5), 'payouts' => fn($q) => $q->latest()->limit(5), 'orders', 'licenses', 'batches.product'])
                ->find($this->viewId)
            : null;
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'     => Reseller::count(),
            'active'    => Reseller::where('status', 'active')->count(),
            'pending'   => Reseller::where('status', 'pending')->count(),
            'wholesale' => Reseller::whereIn('type', ['wholesale', 'both'])->where('status', 'active')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>Resellers</x-slot:heading>

    <div class="space-y-5">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total Resellers',  'value' => $this->stats['total'],     'color' => 'slate',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ['label' => 'Active',            'value' => $this->stats['active'],    'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Pending Approval',  'value' => $this->stats['pending'],   'color' => 'amber',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Wholesale Active',  'value' => $this->stats['wholesale'], 'color' => 'purple', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ] as $stat)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 flex items-center gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-{{ $stat['color'] }}-50">
                        <svg class="h-5 w-5 text-{{ $stat['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
                        <p class="text-xs text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Toolbar --}}
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
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Rates</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Balance</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Activity</th>
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
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $reseller->user?->email }}
                                            <span class="mx-1 text-slate-300">·</span>
                                            <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono">{{ $reseller->reseller_code }}</code>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $typeColors = ['referral' => 'blue', 'wholesale' => 'purple', 'both' => 'teal'];
                                        $typeIcons  = [
                                            'referral'  => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
                                            'wholesale' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                            'both'      => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                                        ];
                                        $tc = $typeColors[$reseller->type] ?? 'slate';
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $tc }}-100 text-{{ $tc }}-700">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcons[$reseller->type] ?? '' }}"/>
                                        </svg>
                                        {{ ucfirst($reseller->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="space-y-0.5 text-xs">
                                        @if (in_array($reseller->type, ['referral', 'both']))
                                            <div class="flex items-center justify-center gap-1">
                                                <span class="text-slate-400">Commission:</span>
                                                <span class="font-semibold text-blue-700">{{ $reseller->commission_rate }}%</span>
                                            </div>
                                        @endif
                                        @if (in_array($reseller->type, ['wholesale', 'both']))
                                            <div class="flex items-center justify-center gap-1">
                                                <span class="text-slate-400">Discount:</span>
                                                <span class="font-semibold text-purple-700">{{ $reseller->discount_rate }}%</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <p class="font-semibold text-slate-900">{{ $reseller->currency }} {{ number_format($reseller->balance, 2) }}</p>
                                    <p class="text-xs text-slate-400">of {{ number_format($reseller->total_earned, 2) }} earned</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="space-y-0.5 text-xs">
                                        <p class="text-slate-600"><span class="font-semibold">{{ $reseller->orders_count ?? $reseller->orders()->count() }}</span> orders</p>
                                        <p class="text-slate-400"><span class="font-semibold text-slate-600">{{ $reseller->licenses()->count() }}</span> licenses</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $sc = match($reseller->status) { 'active' => 'green', 'pending' => 'amber', 'suspended' => 'red', default => 'slate' };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                        {{ ucfirst($reseller->status) }}
                                    </span>
                                    @if($reseller->approved_at)
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $reseller->approved_at->format('M d, Y') }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <button wire:click="openView('{{ $reseller->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="View Details">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        {{-- Approve --}}
                                        @if ($reseller->status === 'pending')
                                            <button wire:click="approve('{{ $reseller->id }}')"
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-green-50 hover:text-green-600 transition-colors" title="Approve">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Suspend --}}
                                        @if ($reseller->status === 'active')
                                            <button wire:click="suspend('{{ $reseller->id }}')"
                                                    wire:confirm="Suspend {{ $reseller->display_name }}?"
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Suspend">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Edit --}}
                                        <button wire:click="openEdit('{{ $reseller->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        {{-- Delete --}}
                                        <button wire:click="delete('{{ $reseller->id }}')"
                                                wire:confirm="Remove {{ $reseller->display_name }}? This cannot be undone."
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
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No resellers found.</p>
                                        <button wire:click="openCreate" class="text-sm font-semibold text-cyan-600 hover:underline">Add your first reseller →</button>
                                    </div>
                                </td>
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

    {{-- ═══════════════ View Detail Modal ═══════════════ --}}
    @if ($viewId && $this->viewReseller)
        @php $r = $this->viewReseller; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="closeView"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">

                {{-- Modal header --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl
                            {{ match($r->type) { 'referral' => 'bg-blue-50', 'wholesale' => 'bg-purple-50', default => 'bg-teal-50' } }}">
                            <span class="text-sm font-bold
                                {{ match($r->type) { 'referral' => 'text-blue-700', 'wholesale' => 'text-purple-700', default => 'text-teal-700' } }}">
                                {{ strtoupper(substr($r->display_name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $r->display_name }}</h2>
                            <p class="text-xs text-slate-500">
                                <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono">{{ $r->reseller_code }}</code>
                                <span class="mx-1">·</span>
                                {{ ucfirst($r->type) }}
                                @if($r->status === 'active')
                                    <span class="ml-1 inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs font-medium">Active</span>
                                @elseif($r->status === 'pending')
                                    <span class="ml-1 inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-medium">Pending</span>
                                @else
                                    <span class="ml-1 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs font-medium">Suspended</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Account & Financial summary --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-2">Account</p>
                            <p class="font-semibold text-slate-900">{{ $r->user?->name }}</p>
                            <p class="text-sm text-slate-600">{{ $r->user?->email }}</p>
                            @if($r->company_name)
                                <p class="text-xs text-slate-500 mt-1">{{ $r->company_name }}</p>
                            @endif
                            @if($r->approved_at)
                                <p class="text-xs text-slate-400 mt-2">Approved {{ $r->approved_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-2">Financials</p>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Total Earned</span>
                                    <span class="font-semibold text-slate-800">{{ $r->currency }} {{ number_format($r->total_earned, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Total Paid Out</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($r->total_paid, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm border-t border-slate-200 pt-1 mt-1">
                                    <span class="text-slate-600 font-medium">Balance</span>
                                    <span class="font-bold {{ $r->balance > 0 ? 'text-green-700' : 'text-slate-600' }}">{{ number_format($r->balance, 2) }}</span>
                                </div>
                                <p class="text-xs text-slate-400">Min payout: {{ $r->currency }} {{ number_format($r->minimum_payout, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Rates card --}}
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-3">Rates & Settings</p>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            @if(in_array($r->type, ['referral', 'both']))
                                <div class="text-center rounded-lg bg-blue-50 p-3">
                                    <p class="text-2xl font-bold text-blue-700">{{ $r->commission_rate }}%</p>
                                    <p class="text-xs text-blue-600 mt-0.5">Referral Commission</p>
                                </div>
                            @endif
                            @if(in_array($r->type, ['wholesale', 'both']))
                                <div class="text-center rounded-lg bg-purple-50 p-3">
                                    <p class="text-2xl font-bold text-purple-700">{{ $r->discount_rate }}%</p>
                                    <p class="text-xs text-purple-600 mt-0.5">Wholesale Discount</p>
                                </div>
                            @endif
                            <div class="text-center rounded-lg bg-slate-50 p-3">
                                <p class="text-lg font-bold text-slate-700">{{ $r->payout_method ? ucfirst($r->payout_method) : '—' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">Payout Method</p>
                            </div>
                        </div>
                    </div>

                    {{-- Activity stats --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->orders->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Referred Orders</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->licenses->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Licenses Issued</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $r->batches->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Batches</p>
                        </div>
                    </div>

                    {{-- Batches (wholesale) --}}
                    @if(in_array($r->type, ['wholesale', 'both']) && $r->batches->count())
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Recent Batches</p>
                            <div class="space-y-2">
                                @foreach($r->batches->take(4) as $batch)
                                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-2.5">
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $batch->label }}</p>
                                            <p class="text-xs text-slate-400">{{ $batch->product?->name }} · {{ number_format($batch->total_generated) }} keys</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ $batch->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ ucfirst($batch->status) }}
                                            </span>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $batch->total_used }} / {{ $batch->total_generated }} used</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Recent commissions --}}
                    @if($r->commissions->count())
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Recent Commissions</p>
                            <div class="space-y-2">
                                @foreach($r->commissions as $comm)
                                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-2">
                                        <div>
                                            <p class="text-xs text-slate-500">{{ $comm->created_at->format('M d, Y') }}</p>
                                            @if(isset($comm->description)) <p class="text-sm text-slate-700">{{ $comm->description }}</p> @endif
                                        </div>
                                        <span class="font-semibold text-green-700 text-sm">
                                            {{ $r->currency }} {{ number_format($comm->amount ?? 0, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notes --}}
                    @if($r->notes)
                        <div class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3">
                            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1">Notes</p>
                            <p class="text-sm text-amber-800">{{ $r->notes }}</p>
                        </div>
                    @endif

                    {{-- Footer actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                        @if ($r->status === 'pending')
                            <button wire:click="approve('{{ $r->id }}')"
                                    class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                                Approve Reseller
                            </button>
                        @endif
                        @if ($r->status === 'active')
                            <button wire:click="suspend('{{ $r->id }}')" wire:confirm="Suspend this reseller?"
                                    class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">
                                Suspend
                            </button>
                        @endif
                        <button wire:click="openEdit('{{ $r->id }}')"
                                class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            Edit Reseller
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════ Create / Edit Slide-over ═══════════════ --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl overflow-y-auto">

                {{-- Header --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100">
                            <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $editMode ? 'Edit Reseller' : 'Add Reseller' }}</h2>
                            <p class="text-xs text-slate-400">{{ $editMode ? 'Update reseller settings' : 'Add a new referral or wholesale partner' }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="px-6 py-5 space-y-5">

                    {{-- User Account --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            User Account <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="user_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 bg-white">
                            <option value="">Select user account...</option>
                            @foreach ($this->users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company & Code --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Company Name</label>
                            <input wire:model="company_name" type="text" placeholder="Optional"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Reseller Code <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="reseller_code" type="text" placeholder="e.g. PARTNER01"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm uppercase focus:outline-none focus:border-cyan-400">
                            @error('reseller_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Reseller Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['value' => 'referral',  'label' => 'Referral',  'desc' => 'Earns % commission on each sale they refer', 'color' => 'blue'],
                                ['value' => 'wholesale', 'label' => 'Wholesale', 'desc' => 'Buys license batches at a discount, resells independently', 'color' => 'purple'],
                                ['value' => 'both',      'label' => 'Both',      'desc' => 'Supports both referral and wholesale modes', 'color' => 'teal'],
                            ] as $t)
                                <label class="cursor-pointer rounded-xl border-2 p-3 transition-colors
                                    {{ $type === $t['value'] ? 'border-'.$t['color'].'-500 bg-'.$t['color'].'-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="type" value="{{ $t['value'] }}" class="sr-only">
                                    <p class="text-sm font-semibold text-slate-800">{{ $t['label'] }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 leading-tight">{{ $t['desc'] }}</p>
                                </label>
                            @endforeach
                        </div>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rates — conditional by type --}}
                    @if(in_array($type, ['referral', 'both']))
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-3">Referral Settings</p>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Commission Rate (%) <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input wire:model="commission_rate" type="number" step="0.01" min="0" max="100"
                                           class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:outline-none focus:border-blue-400">
                                    <p class="text-xs text-slate-500">% of each referred sale credited to reseller balance</p>
                                </div>
                                @error('commission_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    @if(in_array($type, ['wholesale', 'both']))
                        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4">
                            <p class="text-xs font-semibold text-purple-700 uppercase tracking-wide mb-3">Wholesale Settings</p>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Discount Rate (%) <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input wire:model="discount_rate" type="number" step="0.01" min="0" max="100"
                                           class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:outline-none focus:border-purple-400">
                                    <p class="text-xs text-slate-500">% discount applied to wholesale batch purchases</p>
                                </div>
                                @error('discount_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Payout --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Payout Method</label>
                            <select wire:model="payout_method"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">— Select —</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="crypto">Crypto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                            <select wire:model="currency"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="CAD">CAD</option>
                                <option value="AUD">AUD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Min. Payout Amount</label>
                            <input wire:model="minimum_payout" type="number" step="0.01" min="0"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('minimum_payout') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                            <select wire:model="status"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="pending">Pending Approval</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"
                                  placeholder="Internal notes about this reseller…"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 shadow-sm transition-colors">
                            <span wire:loading.remove wire:target="save">{{ $editMode ? 'Save Changes' : 'Add Reseller' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
