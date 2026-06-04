<?php

use App\Models\Customer;
use App\Models\License;
use App\Models\Product;
use App\Models\ProductPlan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Licenses — ExchoLicense')] class extends Component
{
    use WithPagination;

    // ── Filter / List ─────────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterProduct = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool  $showModal  = false;
    public bool  $editing    = false;
    public       $editingId  = null;

    // ── Create form (minimal) ─────────────────────────────────────────────────
    public string $product_id      = '';
    public string $plan_id         = '';
    public string $customer_search = '';   // search by email or name
    public string $customer_id     = '';   // resolved customer UUID
    public string $customer_label  = '';   // display name shown in UI
    public bool   $showCustomerDropdown = false;
    public string $notes           = '';

    // ── Edit-only fields ──────────────────────────────────────────────────────
    public string $status          = 'active';
    public string $expires_at      = '';
    public string $edit_customer_display = '';
    public string $license_key     = '';

    // ─────────────────────────────────────────────────────────────────────────

    public function updatingSearch(): void { $this->resetPage(); }

    // ── When product changes, reset plan ──────────────────────────────────────
    public function updatedProductId(): void
    {
        $this->plan_id = '';
    }

    // ── Customer search ───────────────────────────────────────────────────────
    public function updatedCustomerSearch(): void
    {
        $this->customer_id    = '';
        $this->customer_label = '';
        $this->showCustomerDropdown = strlen($this->customer_search) >= 2;
    }

    public function selectCustomer(string $id, string $label): void
    {
        $this->customer_id              = $id;
        $this->customer_label           = $label;
        $this->customer_search          = $label;
        $this->showCustomerDropdown     = false;
    }

    public function clearCustomer(): void
    {
        $this->customer_id              = '';
        $this->customer_label           = '';
        $this->customer_search          = '';
        $this->showCustomerDropdown     = false;
    }

    #[Computed]
    public function customerSuggestions()
    {
        if (strlen($this->customer_search) < 2) return collect();
        return Customer::where(function ($q) {
            $q->where('name', 'like', '%' . $this->customer_search . '%')
              ->orWhere('email', 'like', '%' . $this->customer_search . '%');
        })->limit(8)->get(['id', 'name', 'email']);
    }

    // ── Open / Close ──────────────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetCreateForm();
        $this->editing   = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit($id): void
    {
        $license = License::with(['customer', 'plan', 'product'])->findOrFail($id);
        $this->editingId             = $id;
        $this->product_id            = (string) $license->product_id;
        $this->plan_id               = (string) ($license->plan_id ?? '');
        $this->license_key           = $license->license_key;
        $this->status                = $license->status;
        $this->expires_at            = $license->expires_at ? $license->expires_at->format('Y-m-d') : '';
        $this->notes                 = $license->notes ?? '';
        $this->edit_customer_display = $license->customer?->name ?? $license->customer?->email ?? '(no customer)';
        $this->editing               = true;
        $this->showModal             = true;
    }

    // ── Save ─────────────────────────────────────────────────────────────────
    public function save(): void
    {
        if (! $this->editing) {
            $this->validate([
                'product_id' => 'required|exists:products,id',
                'plan_id'    => 'required|exists:product_plans,id',
                'notes'      => 'nullable|string|max:1000',
            ]);

            // Resolve plan to set expiry + activations
            $plan = ProductPlan::findOrFail($this->plan_id);

            $expiresAt      = $plan->expires_at_for_new_license;
            $maxActivations = $plan->max_activations ?? config('licensing.max_activations', 1);

            // Determine edition / type from plan name (best-effort)
            $planName = strtolower($plan->name);
            $edition  = 'standard';
            if (str_contains($planName, 'enterprise')) $edition = 'enterprise';
            elseif (str_contains($planName, 'pro')) $edition = 'professional';

            $type = match (true) {
                $plan->duration_days === 0              => 'lifetime',
                $plan->duration_days <= 31              => 'monthly',
                $plan->duration_days <= 93              => 'monthly',  // quarterly still monthly type
                $plan->duration_days >= 365             => 'annual',
                default                                 => 'lifetime',
            };

            $data = [
                'product_id'      => $this->product_id,
                'plan_id'         => $this->plan_id,
                'edition'         => $edition,
                'type'            => $type,
                'max_activations' => $maxActivations,
                'status'          => 'active',
                'expires_at'      => $expiresAt,
                'notes'           => $this->notes ?: null,
            ];

            // Attach customer if selected
            if ($this->customer_id) {
                $data['customer_id'] = $this->customer_id;
            }

            License::create($data);
            session()->flash('success', 'License created successfully.');

        } else {
            $this->validate([
                'status'     => 'required|in:active,inactive,expired,suspended,revoked,trial',
                'expires_at' => 'nullable|date',
                'notes'      => 'nullable|string|max:1000',
            ]);

            $data = [
                'status'     => $this->status,
                'expires_at' => $this->expires_at ?: null,
                'notes'      => $this->notes ?: null,
            ];

            // Allow changing plan on edit
            if ($this->plan_id) {
                $data['plan_id'] = $this->plan_id;
            }

            License::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'License updated.');
        }

        $this->showModal = false;
        $this->resetCreateForm();
    }

    public function revoke($id): void
    {
        License::findOrFail($id)->update(['status' => 'revoked', 'revoked_at' => now()]);
        session()->flash('success', 'License revoked.');
    }

    public function deleteLicense($id): void
    {
        License::findOrFail($id)->delete();
        session()->flash('success', 'License deleted.');
    }

    private function resetCreateForm(): void
    {
        $this->product_id             = '';
        $this->plan_id                = '';
        $this->customer_search        = '';
        $this->customer_id            = '';
        $this->customer_label         = '';
        $this->showCustomerDropdown   = false;
        $this->notes                  = '';
        $this->status                 = 'active';
        $this->expires_at             = '';
        $this->license_key            = '';
        $this->edit_customer_display  = '';
        $this->editingId              = null;
        $this->resetValidation();
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function licenses()
    {
        return License::query()
            ->with(['product', 'customer', 'plan'])
            ->when($this->search, fn ($q) => $q
                ->where('license_key', 'like', "%{$this->search}%")
                ->orWhereHas('customer', fn ($c) => $c
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->filterStatus,  fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterProduct, fn ($q) => $q->where('product_id', $this->filterProduct))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function products()
    {
        return Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'product_code']);
    }

    #[Computed]
    public function plansForProduct()
    {
        if (! $this->product_id) return collect();
        return ProductPlan::where('product_id', $this->product_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }
}; ?>

<div>
    <x-slot:heading>Licenses</x-slot:heading>

    <div class="space-y-5">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search key, customer, email…"
                           class="pl-9 pr-4 py-2 w-64 rounded-lg border border-slate-300 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                </div>
                <select wire:model.live="filterStatus" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                    <option value="suspended">Suspended</option>
                    <option value="revoked">Revoked</option>
                    <option value="trial">Trial</option>
                </select>
                <select wire:model.live="filterProduct" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Products</option>
                    @foreach($this->products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="openCreate"
                    class="flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New License
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">License Key</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product / Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Activations</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Expires</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->licenses as $license)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 font-mono font-medium text-slate-900 text-xs">{{ $license->license_key }}</td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800">{{ $license->product?->name ?? '—' }}</p>
                                    @if($license->plan)
                                        <p class="text-xs text-slate-400">{{ $license->plan->name }} · {{ $license->plan->billing_label }}</p>
                                    @else
                                        <p class="text-xs text-slate-400 italic">No plan</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($license->customer)
                                        <p class="font-medium text-slate-800">{{ $license->customer->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $license->customer->email }}</p>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="{{ ($license->current_activations ?? 0) >= $license->max_activations ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                        {{ $license->current_activations ?? 0 }} / {{ $license->max_activations }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-600 text-xs">
                                    {{ $license->expires_at ? $license->expires_at->format('M d, Y') : '∞ Lifetime' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $badge = match($license->status) {
                                            'active'    => 'bg-green-50 text-green-700 ring-green-600/20',
                                            'inactive'  => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                                            'expired'   => 'bg-red-50 text-red-700 ring-red-600/20',
                                            'suspended' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                            'revoked'   => 'bg-slate-200 text-slate-500 ring-slate-500/20',
                                            'trial'     => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                            default     => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                        {{ ucfirst($license->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button wire:click="openEdit('{{ $license->id }}')"
                                                class="text-xs font-medium text-cyan-600 hover:text-cyan-800 transition-colors">Edit</button>
                                        @if(! in_array($license->status, ['revoked']))
                                            <button wire:click="revoke('{{ $license->id }}')"
                                                    wire:confirm="Revoke this license? The customer will lose access."
                                                    class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">Revoke</button>
                                        @endif
                                        <button wire:click="deleteLicense('{{ $license->id }}')"
                                                wire:confirm="Permanently delete this license?"
                                                class="text-xs font-medium text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No licenses found.</p>
                                        <button wire:click="openCreate" class="text-sm font-semibold text-cyan-600 hover:underline">Create your first license →</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $this->licenses->links() }}</div>

    </div>

    {{-- ────────────────────── Create / Edit Modal ────────────────────── --}}
    <div
        x-data
        x-show="$wire.showModal"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        style="display:none; position:fixed; inset:0; z-index:200; overflow-y:auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">

            {{-- Backdrop --}}
            <div style="position:fixed;inset:0;background:rgba(15,23,42,.65);backdrop-filter:blur(3px);"
                 x-on:click="$wire.set('showModal', false)"></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden" x-on:click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-cyan-50 to-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100">
                            <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $editing ? 'Edit License' : 'Issue New License' }}</h2>
                            <p class="text-xs text-slate-400">{{ $editing ? 'Update status or expiry' : 'Select a product and plan to generate a license' }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showModal', false)"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-5">

                    @if(! $editing)
                    {{-- ── CREATE FORM ── --}}

                    {{-- Step 1: Product --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">1</span>
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="product_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white">
                            <option value="">— Select a product —</option>
                            @foreach($this->products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}{{ $p->product_code ? ' ('.$p->product_code.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Step 2: Plan --}}
                    @if($product_id)
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">2</span>
                            Plan <span class="text-red-500">*</span>
                        </label>
                        @if($this->plansForProduct->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                No active plans for this product.
                                <a href="{{ route('admin.products') }}" wire:navigate class="underline font-semibold">Add plans →</a>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($this->plansForProduct as $plan)
                                    <label class="flex items-center gap-3 rounded-xl border-2 cursor-pointer px-4 py-3 transition-colors
                                                  {{ $plan_id === (string)$plan->id ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                        <input type="radio" wire:model.live="plan_id" value="{{ $plan->id }}" class="text-cyan-600 focus:ring-cyan-500">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-slate-800">{{ $plan->name }}</span>
                                                <span class="text-xs text-slate-400">· {{ $plan->billing_label }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if($plan->is_on_sale)
                                                    <span class="text-sm font-bold text-cyan-700">{{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</span>
                                                    <span class="text-xs text-slate-400 line-through">{{ number_format($plan->price, 2) }}</span>
                                                @else
                                                    <span class="text-sm font-bold text-slate-700">{{ $plan->currency }} {{ number_format($plan->price, 2) }}</span>
                                                @endif
                                                @if($plan->max_activations)
                                                    <span class="text-xs text-slate-400">· {{ $plan->max_activations }} device(s)</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($plan_id === (string)$plan->id)
                                            <svg class="h-5 w-5 text-cyan-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                            @error('plan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @endif
                    </div>
                    @endif

                    {{-- Step 3: Customer (optional) --}}
                    <div x-data>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold mr-1.5">3</span>
                            Customer
                            <span class="text-xs font-normal text-slate-400 ml-1">(optional)</span>
                        </label>

                        @if($customer_id)
                            {{-- Customer selected --}}
                            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                                <div class="h-8 w-8 rounded-full bg-green-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-green-700">{{ strtoupper(substr($customer_label, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-green-800 truncate">{{ $customer_label }}</p>
                                </div>
                                <button type="button" wire:click="clearCustomer" class="text-green-600 hover:text-red-500 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            {{-- Customer search --}}
                            <div class="relative">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input type="text" wire:model.live.debounce.200ms="customer_search"
                                           placeholder="Search by name or email…"
                                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                           autocomplete="off">
                                </div>
                                @if($showCustomerDropdown && $this->customerSuggestions->isNotEmpty())
                                    <div class="absolute z-10 top-full left-0 right-0 mt-1 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
                                        @foreach($this->customerSuggestions as $cust)
                                            <button type="button"
                                                wire:click="selectCustomer('{{ $cust->id }}', '{{ addslashes($cust->name ?? $cust->email) }}')"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-cyan-50 transition-colors">
                                                <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-xs font-semibold text-slate-600">{{ strtoupper(substr($cust->name ?? $cust->email, 0, 1)) }}</span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $cust->name ?? '(no name)' }}</p>
                                                    <p class="text-xs text-slate-400 truncate">{{ $cust->email }}</p>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($showCustomerDropdown && strlen($customer_search) >= 2)
                                    <div class="absolute z-10 top-full left-0 right-0 mt-1 bg-white rounded-xl border border-slate-200 shadow-lg px-4 py-3 text-sm text-slate-400">
                                        No customers found for "{{ $customer_search }}"
                                    </div>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Leave blank to create an unassigned license.</p>
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Notes <span class="text-slate-400 font-normal text-xs">(optional)</span>
                        </label>
                        <textarea wire:model="notes" rows="2"
                                  placeholder="Order reference, internal memo…"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 resize-none"></textarea>
                    </div>

                    @else
                    {{-- ── EDIT FORM ── --}}

                    {{-- Show current license key (read-only) --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-500 mb-0.5">License Key</p>
                        <p class="font-mono text-sm font-semibold text-slate-800">{{ $license_key }}</p>
                    </div>

                    {{-- Customer (read-only on edit) --}}
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-slate-400">Customer</p>
                            <p class="text-sm font-medium text-slate-800">{{ $edit_customer_display }}</p>
                        </div>
                    </div>

                    {{-- Plan change on edit --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Plan</label>
                        @if($product_id)
                            <select wire:model="plan_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="">— Keep current plan —</option>
                                @foreach($this->plansForProduct as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} · {{ $plan->billing_label }} · {{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="revoked">Revoked</option>
                            <option value="trial">Trial</option>
                            <option value="expired">Expired</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expiry --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Expires At <span class="text-slate-400 font-normal text-xs">(leave blank for lifetime)</span>
                        </label>
                        <input type="date" wire:model="expires_at"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 resize-none"></textarea>
                    </div>

                    @endif

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 shadow-sm transition-colors">
                            {{ $editing ? 'Save Changes' : 'Issue License' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
