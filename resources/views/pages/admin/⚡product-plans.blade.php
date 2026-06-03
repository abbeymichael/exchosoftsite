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

    public string $search        = '';
    public string $filterProduct = '';
    public string $filterStatus  = '';

    public bool   $showForm  = false;
    public bool   $editMode  = false;
    public ?string $editId   = null;

    // Form fields
    public string $product_id      = '';
    public string $name            = '';
    public string $slug            = '';
    public string $description     = '';
    public string $price           = '0.00';
    public string $sale_price      = '';
    public string $currency        = 'USD';
    public int    $duration_days   = 0;      // 0 = lifetime
    public int    $trial_days      = 0;
    public bool   $is_trial_eligible = true;
    public bool   $is_renewable    = true;
    public bool   $is_active       = true;
    public int    $sort_order      = 0;
    public string $max_activations  = '';
    public string $offline_ttl_hours = '';
    public string $grace_period_days = '';

    // Duration type helper — drives duration_days
    public string $duration_type = 'lifetime'; // lifetime | monthly | yearly | custom

    public function updatedName(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    public function updatedDurationType(): void
    {
        $this->duration_days = match ($this->duration_type) {
            'monthly'  => 30,
            'yearly'   => 365,
            'lifetime' => 0,
            default    => $this->duration_days,
        };
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
        $this->price           = $plan->price;
        $this->sale_price      = $plan->sale_price ?? '';
        $this->currency        = $plan->currency;
        $this->duration_days   = $plan->duration_days;
        $this->trial_days      = $plan->trial_days;
        $this->is_trial_eligible = $plan->is_trial_eligible;
        $this->is_renewable    = $plan->is_renewable;
        $this->is_active       = $plan->is_active;
        $this->sort_order      = $plan->sort_order;
        $this->max_activations  = $plan->max_activations ?? '';
        $this->offline_ttl_hours = $plan->offline_ttl_hours ?? '';
        $this->grace_period_days = $plan->grace_period_days ?? '';

        // Determine duration type from duration_days
        $this->duration_type = match (true) {
            $plan->duration_days === 0              => 'lifetime',
            $plan->duration_days <= 31              => 'monthly',
            $plan->duration_days >= 365 && $plan->duration_days <= 366 => 'yearly',
            default                                 => 'custom',
        };

        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'product_id'       => 'required|exists:products,id',
            'name'             => 'required|string|max:100',
            'slug'             => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:3',
            'duration_days'    => 'required|integer|min:0',
            'trial_days'       => 'nullable|integer|min:0',
            'sort_order'       => 'nullable|integer|min:0',
            'max_activations'  => 'nullable|integer|min:1',
            'offline_ttl_hours'=> 'nullable|integer|min:0',
            'grace_period_days'=> 'nullable|integer|min:0',
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
            'max_activations'  => $this->max_activations ?: null,
            'offline_ttl_hours'=> $this->offline_ttl_hours ?: null,
            'grace_period_days'=> $this->grace_period_days ?: null,
        ];

        if ($this->editMode) {
            ProductPlan::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Plan updated successfully.');
        } else {
            ProductPlan::create($data);
            session()->flash('success', 'Plan created successfully.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        if ($plan->licenses()->exists()) {
            session()->flash('error', 'Cannot delete plan — it has active licenses attached.');
            return;
        }
        $plan->delete();
        session()->flash('success', 'Plan deleted.');
    }

    public function toggleStatus(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
    }

    public function resetForm(): void
    {
        $this->product_id       = $this->filterProduct ?: '';
        $this->name             = '';
        $this->slug             = '';
        $this->description      = '';
        $this->price            = '0.00';
        $this->sale_price       = '';
        $this->currency         = 'USD';
        $this->duration_days    = 0;
        $this->duration_type    = 'lifetime';
        $this->trial_days       = 0;
        $this->is_trial_eligible = true;
        $this->is_renewable     = true;
        $this->is_active        = true;
        $this->sort_order       = 0;
        $this->max_activations  = '';
        $this->offline_ttl_hours = '';
        $this->grace_period_days = '';
        $this->editId = null;
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
        return Product::where('is_active', true)->orderBy('name')->get();
    }
}; ?>

<div>
    <x-slot:heading>Product Plans</x-slot:heading>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
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
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search plans..."
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
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
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
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $plan->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $plan->slug }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-slate-700">{{ $plan->product->name ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $typeLabel = match(true) {
                                            $plan->duration_days === 0        => 'Lifetime',
                                            $plan->duration_days <= 31        => 'Monthly',
                                            $plan->duration_days <= 93        => 'Quarterly',
                                            $plan->duration_days <= 366       => 'Yearly',
                                            default                           => $plan->duration_days . 'd',
                                        };
                                        $typeColor = match($typeLabel) {
                                            'Lifetime' => 'purple',
                                            'Monthly'  => 'blue',
                                            'Quarterly'=> 'indigo',
                                            'Yearly'   => 'teal',
                                            default    => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $typeColor }}-100 text-{{ $typeColor }}-700">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</p>
                                        @if ($plan->is_on_sale)
                                            <p class="text-xs text-red-500 line-through">{{ number_format($plan->price, 2) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-semibold text-slate-700">{{ $plan->licenses()->count() }}</span>
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
                                            wire:confirm="Delete this plan? This cannot be undone."
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
                                    No plans found. Create one to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->plans->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">
                    {{ $this->plans->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Slide-over Form --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-hidden" x-data>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl overflow-y-auto">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ $editMode ? 'Edit Plan' : 'New Plan' }}
                    </h2>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="px-6 py-5 space-y-5">
                    {{-- Product --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Product <span class="text-red-500">*</span></label>
                        <select wire:model="product_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="">Select a product...</option>
                            @foreach ($this->products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name / Slug --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Plan Name <span class="text-red-500">*</span></label>
                            <input wire:model.live="name" type="text" placeholder="e.g. Monthly, Yearly, Lifetime"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Slug <span class="text-red-500">*</span></label>
                            <input wire:model="slug" type="text" placeholder="monthly"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="2" placeholder="Optional plan description..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400"></textarea>
                    </div>

                    {{-- Duration Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Billing Period <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['lifetime' => 'Lifetime', 'monthly' => 'Monthly', 'yearly' => 'Yearly', 'custom' => 'Custom'] as $val => $label)
                                <label class="flex flex-col items-center rounded-xl border-2 cursor-pointer p-3 transition-colors
                                    {{ $duration_type === $val ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="duration_type" value="{{ $val }}" class="sr-only">
                                    <span class="text-sm font-semibold {{ $duration_type === $val ? 'text-cyan-700' : 'text-slate-600' }}">{{ $label }}</span>
                                    <span class="text-xs text-slate-400 mt-0.5">
                                        {{ $val === 'lifetime' ? '∞' : ($val === 'monthly' ? '30d' : ($val === 'yearly' ? '365d' : '—')) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @if ($duration_type === 'custom')
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Duration in days</label>
                                <input wire:model="duration_days" type="number" min="1" placeholder="e.g. 90 for quarterly"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        @endif
                        @error('duration_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pricing --}}
                    <div class="rounded-xl bg-slate-50 p-4 space-y-3">
                        <p class="text-sm font-semibold text-slate-700">Pricing</p>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Price <span class="text-red-500">*</span></label>
                                <input wire:model="price" type="number" step="0.01" min="0" placeholder="0.00"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Currency</label>
                                <input wire:model="currency" type="text" maxlength="3" placeholder="USD"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Sale Price <span class="text-slate-400">(optional)</span></label>
                            <input wire:model="sale_price" type="number" step="0.01" min="0" placeholder="Leave blank if no sale"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>

                    {{-- Trial --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Trial Days</label>
                            <input wire:model="trial_days" type="number" min="0" placeholder="0 = no trial"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_trial_eligible" class="rounded text-cyan-600 focus:ring-cyan-500">
                                <span class="text-sm text-slate-700">Trial Eligible</span>
                            </label>
                        </div>
                    </div>

                    {{-- Overrides --}}
                    <div class="rounded-xl bg-slate-50 p-4 space-y-3">
                        <p class="text-sm font-semibold text-slate-700">Override Defaults <span class="text-xs font-normal text-slate-500">(leave blank to use product defaults)</span></p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Max Activations</label>
                                <input wire:model="max_activations" type="number" min="1" placeholder="—"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Offline TTL (hrs)</label>
                                <input wire:model="offline_ttl_hours" type="number" min="0" placeholder="—"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Grace Period (days)</label>
                                <input wire:model="grace_period_days" type="number" min="0" placeholder="—"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div class="flex flex-col gap-2 justify-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_renewable" class="rounded text-cyan-600 focus:ring-cyan-500">
                                <span class="text-sm text-slate-700">Renewable</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="rounded text-cyan-600 focus:ring-cyan-500">
                                <span class="text-sm text-slate-700">Active</span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            {{ $editMode ? 'Update Plan' : 'Create Plan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
