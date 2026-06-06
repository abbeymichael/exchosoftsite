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

    // ── Context State ────────────────────────────────────────────────────────
    public ?string $selectedProductId = null; // Tracks current sidebar active product

    // ── Filters & Search ──────────────────────────────────────────────────────
    public string  $search        = '';
    public string  $filterStatus  = '';

    // ── UI Modal Drawer State ────────────────────────────────────────────────
    public bool    $showForm  = false;
    public bool    $editMode  = false;
    public ?string $editId    = null;

    // ── Form Fields ───────────────────────────────────────────────────────────
    public string  $product_id        = '';
    public string  $name              = '';
    public string  $slug              = '';
    public string  $description       = '';
    public string  $price             = '0.00';
    public string  $sale_price        = '';
    public string  $currency          = 'USD';
    public int     $duration_days     = 30;
    public int     $trial_days        = 0;
    public bool    $is_trial_eligible = false;
    public bool    $is_renewable      = true;
    public bool    $is_active         = true;
    public int     $sort_order        = 0;

    // ── Topology Trait Metrics ────────────────────────────────────────────────
    public string  $form_factor       = 'standalone';
    public int     $max_activations   = 1;
    public int     $offline_ttl_hours = 72;
    public int     $grace_period_days = 7;

    public string  $duration_type     = 'Monthly';

    public function mount(): void
    {
        // Auto-select the first product if any exist to initialize clean UI state
        $firstProduct = Product::orderBy('name')->first();
        if ($firstProduct) {
            $this->selectedProductId = $firstProduct->id;
        }
    }

    public function selectProduct(string $productId): void
    {
        $this->selectedProductId = $productId;
        $this->resetPage(); // Reset pagination on switch
    }

    public function updatedName(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    public function updatedDurationType($value): void
    {
        if ($value === 'Monthly') $this->duration_days = 30;
        elseif ($value === 'Quarterly') $this->duration_days = 90;
        elseif ($value === 'Yearly') $this->duration_days = 365;
        elseif ($value === 'Lifetime') $this->duration_days = 0;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        // Contextually bind the form to whichever product is currently open in view
        $this->product_id = $this->selectedProductId ?? '';
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        $this->editId             = $id;
        $this->product_id         = $plan->product_id;
        $this->name               = $plan->name;
        $this->slug               = $plan->slug;
        $this->description        = $plan->description ?? '';
        $this->price              = (string) $plan->price;
        $this->sale_price         = (string) ($plan->sale_price ?? '');
        $this->currency           = $plan->currency;
        $this->duration_days      = $plan->duration_days;
        $this->trial_days         = $plan->trial_days;
        $this->is_trial_eligible  = $plan->is_trial_eligible;
        $this->is_renewable       = $plan->is_renewable;
        $this->is_active          = $plan->is_active;
        $this->sort_order         = $plan->sort_order;

        $this->form_factor       = $plan->form_factor ?? 'standalone';
        $this->max_activations   = $plan->max_activations ?? 1;
        $this->offline_ttl_hours = $plan->offline_ttl_hours ?? 72;
        $this->grace_period_days = $plan->grace_period_days ?? 7;

        if ($this->duration_days === 0) $this->duration_type = 'Lifetime';
        elseif ($this->duration_days <= 31) $this->duration_type = 'Monthly';
        elseif ($this->duration_days <= 93) $this->duration_type = 'Quarterly';
        elseif ($this->duration_days <= 366) $this->duration_type = 'Yearly';
        else $this->duration_type = 'Custom';

        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'product_id'        => 'required|exists:products,id',
            'name'              => 'required|string|max:100',
            'slug'              => 'required|string|max:100',
            'description'       => 'nullable|string|max:255',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'currency'          => 'required|string|max:3',
            'duration_days'     => 'required|integer|min:0',
            'trial_days'        => 'required|integer|min:0',
            'sort_order'        => 'required|integer|min:0',
            'form_factor'       => 'required|string|in:standalone,lan_orchestrated,hybrid_cloud',
            'max_activations'   => 'required|integer|min:1',
            'offline_ttl_hours' => 'required|integer|min:0',
            'grace_period_days' => 'required|integer|min:0',
        ]);

        $data = [
            'product_id'        => $this->product_id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description ?: null,
            'price'             => $this->price,
            'sale_price'        => ($this->sale_price !== '' && $this->sale_price !== null) ? $this->sale_price : null,
            'currency'          => strtoupper($this->currency),
            'duration_days'     => $this->duration_days,
            'trial_days'        => $this->trial_days,
            'is_trial_eligible' => $this->is_trial_eligible,
            'is_renewable'      => $this->is_renewable,
            'is_active'         => $this->is_active,
            'sort_order'        => $this->sort_order,
            'form_factor'       => $this->form_factor,
            'max_activations'   => $this->max_activations,
            'offline_ttl_hours' => $this->offline_ttl_hours,
            'grace_period_days' => $this->grace_period_days,
        ];

        if ($this->editMode) {
            ProductPlan::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Plan mapping refreshed successfully.');
        } else {
            ProductPlan::create($data);
            session()->flash('success', 'New variant topology deployed.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        ProductPlan::findOrFail($id)->delete();
        session()->flash('success', 'Plan purged from registry.');
    }

    public function toggleStatus(string $id): void
    {
        $plan = ProductPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
    }

    private function resetForm(): void
    {
        $this->product_id         = '';
        $this->name               = '';
        $this->slug               = '';
        $this->description        = '';
        $this->price              = '0.00';
        $this->sale_price         = '';
        $this->currency           = 'USD';
        $this->duration_days      = 30;
        $this->duration_type      = 'Monthly';
        $this->trial_days         = 0;
        $this->is_trial_eligible  = false;
        $this->is_renewable       = true;
        $this->is_active          = true;
        $this->sort_order         = 0;
        $this->form_factor       = 'standalone';
        $this->max_activations   = 1;
        $this->offline_ttl_hours = 72;
        $this->grace_period_days = 7;
        $this->editId             = null;
        $this->resetValidation();
    }

    #[Computed]
    public function sidebarProducts()
    {
        // Retrieves listing count inline so the side navigation looks gorgeous
        return Product::orderBy('name')
            ->withCount('plans')
            ->get();
    }

    #[Computed]
    public function currentProduct()
    {
        return $this->selectedProductId ? Product::find($this->selectedProductId) : null;
    }

    #[Computed]
    public function plans()
    {
        return ProductPlan::where('product_id', $this->selectedProductId)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->orderBy('sort_order')
            ->paginate(9);
    }
}; ?>

<div class="h-full">
    <x-slot:heading>App Architecture Topologies & Tiers</x-slot:heading>

    {{-- System Success Alerts --}}
    @if (session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2 shadow-sm">
            <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── MASTER-DETAIL CLEAN SPLIT VIEW LAYOUT ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT SIDEBAR: Software App Context Selector (3 Cols) --}}
        <div class="lg:col-span-3 space-y-3">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Applications</p>
            <div class="rounded-2xl border border-slate-200/80 bg-white p-2 space-y-1 shadow-sm">
                @forelse($this->sidebarProducts as $sidebarProd)
                    <button type="button" wire:click="selectProduct('{{ $sidebarProd->id }}')"
                        class="w-full text-left flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all group
                        {{ $selectedProductId === $sidebarProd->id
                            ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/10'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5 truncate">
                            <div class="h-2 w-2 rounded-full transition-colors
                                {{ $selectedProductId === $sidebarProd->id ? 'bg-white' : 'bg-slate-300 group-hover:bg-cyan-500' }}">
                            </div>
                            <span class="truncate">{{ $sidebarProd->name }}</span>
                        </div>
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md border font-bold transition-colors
                            {{ $selectedProductId === $sidebarProd->id
                                ? 'bg-cyan-700/50 border-cyan-500/30 text-cyan-100'
                                : 'bg-slate-50 border-slate-100 text-slate-400 group-hover:bg-slate-100' }}">
                            {{ $sidebarProd->plans_count }}
                        </span>
                    </button>
                @empty
                    <p class="text-xs text-slate-400 p-3 text-center">No base products found.</p>
                @endforelse
            </div>
        </div>

        {{-- RIGHT MAIN CANVAS: Target Product Strategy Matrix Layout (9 Cols) --}}
        <div class="lg:col-span-9 space-y-5">

            @if($this->currentProduct)
                {{-- Product Canvas Header / Toolbar Control --}}
                <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-50 border border-slate-200/60 rounded-2xl p-4 shadow-sm">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900">{{ $this->currentProduct->name }}</h2>
                            <span class="text-[11px] px-2 py-0.5 font-mono bg-slate-200 text-slate-700 font-bold rounded border border-slate-300/40">
                                {{ $this->currentProduct->product_code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Managing architecture topographies, validation node restrictions, and offline window criteria.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Context Search --}}
                        <div class="relative">
                            <input wire:model.live.debounce.250ms="search" type="text" placeholder="Search product plans..."
                                class="pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-cyan-400 w-44 bg-white">
                        </div>

                        <button type="button" wire:click="openCreate"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Variant
                        </button>
                    </div>
                </div>

                {{-- Cards Grid Area --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    @forelse($this->plans as $plan)
                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden relative group
                            {{ !$plan->is_active ? 'opacity-75 bg-slate-50/50' : '' }}">

                            {{-- Form Factor Top Bar Indicator Banner --}}
                            <div class="px-4 py-2 flex items-center justify-between text-xs border-b border-slate-100 font-semibold
                                {{ $plan->form_factor === 'standalone' ? 'bg-slate-50 text-slate-700' : '' }}
                                {{ $plan->form_factor === 'lan_orchestrated' ? 'bg-indigo-50/80 text-indigo-700 border-indigo-100/50' : '' }}
                                {{ $plan->form_factor === 'hybrid_cloud' ? 'bg-cyan-50/80 text-cyan-700 border-cyan-100/50' : '' }}
                            ">
                                <span class="uppercase tracking-wider font-mono text-[10px]">
                                    {{ str_replace('_', ' ', $plan->form_factor) }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button type="button" wire:click="toggleStatus('{{ $plan->id }}')"
                                        class="h-2 w-2 rounded-full {{ $plan->is_active ? 'bg-green-500' : 'bg-slate-400' }}"
                                        title="Click to toggle status"></button>
                                </div>
                            </div>

                            {{-- Variant Core Details Content --}}
                            <div class="p-4 flex-1 space-y-3">
                                <div>
                                    <h3 class="font-bold text-slate-900 group-hover:text-cyan-600 transition-colors">{{ $plan->name }}</h3>
                                    <p class="text-xs text-slate-400 line-clamp-2 mt-0.5 min-h-[2rem]">{{ $plan->description ?: 'No brief summary added for this package topology configuration tier.' }}</p>
                                </div>

                                {{-- Node & Licensing Metrics Specifications --}}
                                <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 grid grid-cols-2 gap-x-2 gap-y-1.5 text-[11px] font-medium text-slate-500">
                                    <div class="flex items-center gap-1 truncate">
                                        <span class="font-bold text-slate-700 font-mono">{{ $plan->max_activations }}</span>
                                        <span class="truncate">Node Nodes</span>
                                    </div>
                                    <div class="flex items-center gap-1 truncate">
                                        <span class="font-bold text-slate-700 font-mono">{{ $plan->offline_ttl_hours }}h</span>
                                        <span class="truncate">Offline Sync</span>
                                    </div>
                                    <div class="flex items-center gap-1 truncate">
                                        <span class="font-bold text-slate-700 font-mono">{{ $plan->grace_period_days }}d</span>
                                        <span class="truncate">Grace Period</span>
                                    </div>
                                    <div class="flex items-center gap-1 truncate">
                                        <span class="text-cyan-600 font-bold font-mono">{{ $plan->billing_label }}</span>
                                    </div>
                                </div>

                                {{-- Price Line Presentation --}}
                                <div class="pt-1 flex items-baseline justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Pricing Rate</span>
                                    <div class="font-mono font-bold text-base text-slate-900">
                                        @if($plan->sale_price)
                                            <span class="text-xs font-normal line-through text-slate-400 mr-1">{{ number_format($plan->price, 2) }}</span>
                                            <span class="text-emerald-600">{{ $plan->currency }} {{ number_format($plan->sale_price, 2) }}</span>
                                        @else
                                            {{ $plan->currency }} {{ number_format($plan->price, 2) }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Actions Footer Drawer Overlay Trigger --}}
                            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-2 flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" wire:click="openEdit('{{ $plan->id }}')"
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200/60 hover:text-slate-800 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="delete('{{ $plan->id }}')"
                                    wire:confirm="Purge this execution plan mapping matrix permanently?"
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center space-y-2">
                            <p class="text-sm text-slate-500 font-medium">No plans or variants configured for this application yet.</p>
                            <button type="button" wire:click="openCreate" class="text-xs text-cyan-600 font-bold hover:underline">+ Provision First Variant</button>
                        </div>
                    @endforelse

                </div>

                {{-- Pagination Links Block --}}
                @if ($this->plans->hasPages())
                    <div class="pt-2">{{ $this->plans->links() }}</div>
                @endif

            @else
                {{-- Global Empty Workspace Placeholder --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400 max-w-md mx-auto shadow-sm">
                    <p class="text-sm font-medium">Select a software product application context from the sidebar layout to map out structure pricing configurations.</p>
                </div>
            @endif

        </div>
    </div>

    {{-- Slide-Over Side Drawer Configuration Module Form --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="relative ml-auto w-full max-w-xl bg-white shadow-2xl flex flex-col h-full animate-slide-in">

                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 flex-shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $editMode ? 'Edit Plan Variant Topology' : 'Provision Plan Variant' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Map licensing behaviors, offline heartbeats, and pricing targets.</p>
                    </div>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 overflow-y-auto">
                    <div class="px-6 py-5 space-y-4">

                        {{-- Hidden/Context-Assigned Base App ID --}}
                        <input type="hidden" wire:model="product_id">

                        {{-- Plan Title + Slug --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Plan Variant Title <span class="text-red-500">*</span></label>
                                <input wire:model.live="name" type="text" placeholder="e.g., Multi-Workstation Pack"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">System Identifier Slug <span class="text-red-500">*</span></label>
                                <input wire:model="slug" type="text" placeholder="multi-workstation-pack"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-xs">
                                @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Variant Description Summary --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Variant Brief Summary</label>
                            <input wire:model="description" type="text" placeholder="Perfect for high-throughput single-LAN local business operations..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- HARDWARE ARCHITECTURE / METRIC METRICS CARD --}}
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-3 shadow-inner">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Architectural Topology Allocation
                            </p>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Infrastructure Form Factor Strategy Target</label>
                                <select wire:model.live="form_factor" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 bg-white shadow-sm">
                                    <option value="standalone">Standalone Instance (Single Device Isolation)</option>
                                    <option value="lan_orchestrated">Local LAN Multi-Workstation Orchestrator</option>
                                    <option value="hybrid_cloud">Hybrid Infrastructure (Local Orchestrator + Cloud Relay)</option>
                                </select>
                                @error('form_factor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1" title="Max concurrent paired validation nodes">Node Activations</label>
                                    <input wire:model="max_activations" type="number" min="1"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 bg-white shadow-sm">
                                    @error('max_activations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1" title="Maximum isolation duration allowed without central validation check">Offline TTL (Hrs)</label>
                                    <input wire:model="offline_ttl_hours" type="number" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 bg-white shadow-sm">
                                    @error('offline_ttl_hours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1" title="Days running operations past structural subscription window expiration">Grace Period (Days)</label>
                                    <input wire:model="grace_period_days" type="number" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 bg-white shadow-sm">
                                    @error('grace_period_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Price Parameters Tiers --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                                <input wire:model="currency" type="text" maxlength="3" placeholder="USD"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono uppercase text-center">
                                @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Base Cost Rate <span class="text-red-500">*</span></label>
                                <input wire:model="price" type="number" step="0.01" min="0"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-right">
                                @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Sale Promo Rate</label>
                                <input wire:model="sale_price" type="number" step="0.01" min="0" placeholder="Optional"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-right">
                                @error('sale_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Renewal Window & Terms Selector --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Billing Terms Duration Tier</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Monthly' => '30 Days', 'Quarterly' => '90 Days', 'Yearly' => '365 Days', 'Lifetime' => 'Infinite', 'Custom' => 'Manual'] as $typeKey => $lblHint)
                                    <label class="flex items-center gap-1.5 border rounded-xl px-3 py-2 cursor-pointer transition-all text-xs font-medium
                                        {{ $duration_type === $typeKey ? 'border-cyan-500 bg-cyan-50/60 text-cyan-700 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-600' }}
                                    ">
                                        <input type="radio" wire:model.live="duration_type" value="{{ $typeKey }}" class="sr-only">
                                        <span>{{ $typeKey }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal">({{ $lblHint }})</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($duration_type === 'Custom')
                                <div class="mt-3 bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Custom Expiration Matrix Period (Days)</label>
                                    <input wire:model="duration_days" type="number" min="1"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:border-cyan-400 bg-white">
                                </div>
                            @endif
                            @error('duration_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Trials Window Rules Config --}}
                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Trial Window Period (Days)</label>
                                <input wire:model="trial_days" type="number" min="0"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('trial_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col justify-end space-y-2 pb-1.5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_trial_eligible" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-xs font-medium text-slate-600">First-time trials only</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_renewable" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-xs font-medium text-slate-600">Auto standard renewal</span>
                                </label>
                            </div>
                        </div>

                        {{-- Priority Weight Sorting + Activation Status Flags --}}
                        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                <span class="text-sm font-semibold text-slate-700">Activate Plan Visibility</span>
                            </label>
                            <div class="ml-auto flex items-center gap-2">
                                <label class="block text-xs font-medium text-slate-500">Priority Weight</label>
                                <input wire:model="sort_order" type="number" min="0"
                                    class="w-16 rounded-xl border border-slate-200 px-2 py-1 text-sm text-center focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>

                    </div>

                    {{-- Bottom Footer Action Save Matrix Controls --}}
                    <div class="sticky bottom-0 bg-white border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3 flex-shrink-0">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                            {{ $editMode ? 'Update Operational Plan' : 'Deploy Plan Option' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
