<?php

use App\Models\Product;
use App\Models\ProductPlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Product Plans — ExchoSoft')] class extends Component {
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string  $search        = '';
    public string  $filterProduct = '';
    public string  $filterStatus  = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool    $showForm  = false;
    public bool    $editMode  = false;
    public ?string $editId    = null;

    // ── Form Fields ───────────────────────────────────────────────────────────
    public string  $product_id     = '';
    public string  $name           = '';
    public string  $slug           = '';
    public string  $description    = '';
    public string  $price          = '0.00';
    public string  $sale_price     = '';
    public string  $currency       = 'USD';
    public int     $duration_days  = 30;
    public int     $trial_days     = 0;
    public bool    $is_trial_eligible = false;
    public bool    $is_renewable   = true;
    public bool    $is_active      = true;
    public int     $sort_order     = 0;

    // Duration type helper (drives duration_days)
    public string  $duration_type  = 'Monthly'; // Monthly | Quarterly | Yearly | Lifetime | Custom

    // ─────────────────────────────────────────────────────────────────────────

    public function updatedName(): void
    {
        if (! $this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    public function updatedDurationType(): void
    {
        if ($this->duration_type !== 'Custom') {
            $this->duration_days = match ($this->duration_type) {
                'Monthly'   => 30,
                'Quarterly' => 90,
                'Yearly'    => 365,
                'Lifetime'  => 0,
                default     => $this->duration_days,
            };
            // Also auto-set name if it matches a preset
            if (in_array($this->name, ['', 'Monthly', 'Quarterly', 'Yearly', 'Lifetime'])) {
                $this->name = $this->duration_type;
                $this->slug = str($this->name)->slug()->toString();
            }
        }
    }

    public function openCreate(?string $productId = null): void
    {
        $this->resetForm();
        if ($productId) {
            $this->product_id = $productId;
        }
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        $this->editId          = $id;
        $this->product_id      = $plan->product_id;
        $this->name            = $plan->name;
        $this->slug            = $plan->slug;
        $this->description     = $plan->description ?? '';
        $this->price           = (string) $plan->price;
        $this->sale_price      = (string) ($plan->sale_price ?? '');
        $this->currency        = $plan->currency;
        $this->duration_days   = $plan->duration_days;
        $this->trial_days      = $plan->trial_days;
        $this->is_trial_eligible = $plan->is_trial_eligible;
        $this->is_renewable    = $plan->is_renewable;
        $this->is_active       = $plan->is_active;
        $this->sort_order      = $plan->sort_order;

        $this->duration_type   = match (true) {
            $plan->duration_days === 0                                             => 'Lifetime',
            $plan->duration_days <= 31                                             => 'Monthly',
            $plan->duration_days <= 93                                             => 'Quarterly',
            $plan->duration_days >= 365 && $plan->duration_days <= 366            => 'Yearly',
            default                                                                => 'Custom',
        };

        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'product_id'    => 'required|exists:products,id',
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'sale_price'    => 'nullable|numeric|min:0',
            'currency'      => 'required|string|max:3',
            'duration_days' => 'required|integer|min:0',
            'trial_days'    => 'nullable|integer|min:0',
            'sort_order'    => 'nullable|integer|min:0',
        ]);

        // Unique slug per product
        $slugRule = 'unique:product_plans,slug,NULL,id,product_id,' . $this->product_id;
        if ($this->editMode) {
            $slugRule = 'unique:product_plans,slug,' . $this->editId . ',id,product_id,' . $this->product_id;
        }
        $this->validate(['slug' => $slugRule]);

        $data = [
            'product_id'       => $this->product_id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'description'      => $this->description ?: null,
            'price'            => $this->price,
            'sale_price'       => $this->sale_price ?: null,
            'currency'         => $this->currency,
            'duration_days'    => $this->duration_days,
            'trial_days'       => $this->trial_days,
            'is_trial_eligible'=> $this->is_trial_eligible,
            'is_renewable'     => $this->is_renewable,
            'is_active'        => $this->is_active,
            'sort_order'       => $this->sort_order,
        ];

        if ($this->editMode) {
            ProductPlan::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Plan updated.');
        } else {
            ProductPlan::create($data);
            session()->flash('success', 'Plan created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        if ($plan->licenses()->exists()) {
            session()->flash('error', 'Cannot delete plan — it has licenses attached.');
            return;
        }
        $plan->delete();
        session()->flash('success', 'Plan deleted.');
    }

    public function toggleStatus(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        $plan->update(['is_active' => ! $plan->is_active]);
    }

    public function resetForm(): void
    {
        $this->product_id      = $this->filterProduct ?: '';
        $this->name            = '';
        $this->slug            = '';
        $this->description     = '';
        $this->price           = '0.00';
        $this->sale_price      = '';
        $this->currency        = 'USD';
        $this->duration_days   = 30;
        $this->duration_type   = 'Monthly';
        $this->trial_days      = 0;
        $this->is_trial_eligible = false;
        $this->is_renewable    = true;
        $this->is_active       = true;
        $this->sort_order      = 0;
        $this->editId          = null;
        $this->resetValidation();
    }

    #[Computed]
    public function plans()
    {
        return ProductPlan::with('product')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->filterProduct, fn ($q) => $q->where('product_id', $this->filterProduct))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->paginate(20);
    }

    #[Computed]
    public function products()
    {
        return Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}; ?>

<div>
    <x-slot:heading>Product Plans</x-slot:heading>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search plans…"
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-48">
                </div>
                <select wire:model.live="filterProduct" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Products</option>
                    @foreach ($this->products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New Plan
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Billing</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Price</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Licenses</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->plans as $plan)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-slate-900">{{ $plan->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $plan->slug }}</p>
                                </td>
                                <td class="px-5 py-3 text-slate-700">{{ $plan->product->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $typeLabel = match(true) {
                                            $plan->duration_days === 0  => 'Lifetime',
                                            $plan->duration_days <= 31  => 'Monthly',
                                            $plan->duration_days <= 93  => 'Quarterly',
                                            $plan->duration_days <= 366 => 'Yearly',
                                            default                     => $plan->duration_days . 'd',
                                        };
                                        $typeColor = match($typeLabel) {
                                            'Lifetime'  => 'purple',
                                            'Monthly'   => 'blue',
                                            'Quarterly' => 'indigo',
                                            'Yearly'    => 'teal',
                                            default     => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $typeColor }}-100 text-{{ $typeColor }}-700">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <p class="font-semibold text-slate-900">{{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</p>
                                    @if ($plan->is_on_sale)
                                        <p class="text-xs text-red-500 line-through">{{ number_format($plan->price, 2) }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center font-semibold text-slate-700">
                                    {{ $plan->licenses()->count() }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <button wire:click="toggleStatus('{{ $plan->id }}')"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $plan->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openEdit('{{ $plan->id }}')"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete('{{ $plan->id }}')"
                                            wire:confirm="Delete this plan?"
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
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">
                                    No plans found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->plans->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">{{ $this->plans->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ── Slide-over Form ── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-hidden" x-data>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4 flex-shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $editMode ? 'Edit Plan' : 'New Plan' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Define a pricing plan for a product</p>
                    </div>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                    {{-- Product --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Product <span class="text-red-500">*</span></label>
                        <select wire:model="product_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 bg-white">
                            <option value="">Select a product…</option>
                            @foreach ($this->products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Billing Period --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Billing Period <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach(['Monthly' => '30d', 'Quarterly' => '90d', 'Yearly' => '365d', 'Lifetime' => '∞', 'Custom' => '?'] as $val => $hint)
                                <label class="flex flex-col items-center rounded-xl border-2 cursor-pointer p-2.5 transition-colors
                                    {{ $duration_type === $val ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="duration_type" value="{{ $val }}" class="sr-only">
                                    <span class="text-xs font-semibold {{ $duration_type === $val ? 'text-cyan-700' : 'text-slate-600' }}">{{ $val }}</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">{{ $hint }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if ($duration_type === 'Custom')
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Duration in days</label>
                                <input wire:model="duration_days" type="number" min="1" placeholder="e.g. 180"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        @endif
                        @error('duration_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name / Slug --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Plan Name <span class="text-red-500">*</span></label>
                            <input wire:model.live="name" type="text" placeholder="Monthly Pro"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Slug</label>
                            <input wire:model="slug" type="text" placeholder="monthly-pro"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-xs">
                            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
                        <textarea wire:model="description" rows="2" placeholder="Brief plan description…"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>

                    {{-- Pricing --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Pricing</p>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                                    <input wire:model="price" type="number" step="0.01" min="0" placeholder="0.00"
                                        class="w-full rounded-xl border border-slate-200 pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                                <input wire:model="currency" type="text" maxlength="3" placeholder="USD"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono uppercase">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Sale Price <span class="text-slate-400 font-normal">(optional)</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                                <input wire:model="sale_price" type="number" step="0.01" min="0" placeholder="Leave blank if no sale"
                                    class="w-full rounded-xl border border-slate-200 pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>
                    </div>

                    {{-- Trial --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Trial Days <span class="text-slate-400 text-xs font-normal">(0 = no trial)</span></label>
                            <input wire:model="trial_days" type="number" min="0" placeholder="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_trial_eligible" class="rounded text-cyan-600 focus:ring-cyan-500">
                                <span class="text-sm text-slate-700">Trial Eligible</span>
                            </label>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="flex flex-wrap gap-4 items-center">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_renewable" class="rounded text-cyan-600 focus:ring-cyan-500">
                            <span class="text-sm text-slate-700">Renewable</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded text-cyan-600 focus:ring-cyan-500">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0"
                                class="w-20 rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                            {{ $editMode ? 'Update Plan' : 'Create Plan' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
