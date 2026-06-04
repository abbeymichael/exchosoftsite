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

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterProduct = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool    $showModal      = false;
    public bool    $editing        = false;
    public         $editingId      = null;
    public string  $modalMode      = 'create';  // create | edit | trial | convert

    // ── Create form ───────────────────────────────────────────────────────────
    public string $product_id      = '';
    public string $plan_id         = '';
    public string $customer_search = '';
    public string $customer_id     = '';
    public string $customer_label  = '';
    public bool   $showCustomerDropdown = false;
    public string $notes           = '';

    // ── Trial form ────────────────────────────────────────────────────────────
    public string $trial_product_id = '';
    public string $trial_plan_id    = '';     // optional — which plan the trial is for
    public int    $trial_days       = 14;
    public string $trial_customer_search = '';
    public string $trial_customer_id     = '';
    public string $trial_customer_label  = '';
    public bool   $showTrialCustomerDropdown = false;
    public string $trial_notes      = '';
    public int    $trial_max_act    = 1;

    // ── Edit-only fields ──────────────────────────────────────────────────────
    public string $status                = 'active';
    public string $expires_at            = '';
    public string $edit_customer_display = '';
    public string $license_key           = '';
    public int    $max_activations       = 1;
    public string $plan_changed_preview  = '';

    // ── Convert trial fields ──────────────────────────────────────────────────
    public ?string $convertLicenseId   = null;
    public string  $convert_plan_id    = '';
    public string  $convert_preview    = '';   // expiry preview
    public string  $convert_license_key = '';
    public string  $convert_product_id  = '';

    // ─────────────────────────────────────────────────────────────────────────

    public function updatingSearch(): void { $this->resetPage(); }

    // ── Product / Plan watchers ───────────────────────────────────────────────
    public function updatedProductId(): void
    {
        $this->plan_id          = '';
        $this->plan_changed_preview = '';
    }

    public function updatedTrialProductId(): void
    {
        $this->trial_plan_id = '';
        $this->trial_days    = 14;
    }

    public function updatedTrialPlanId(): void
    {
        if (! $this->trial_plan_id) {
            return;
        }
        $plan = ProductPlan::find($this->trial_plan_id);
        if ($plan && $plan->trial_days > 0) {
            $this->trial_days = $plan->trial_days;
        }
    }

    // ── When plan changes in EDIT mode, recalculate expiry from today ─────────
    public function updatedPlanId(): void
    {
        if (! $this->editing || ! $this->plan_id) {
            $this->plan_changed_preview = '';
            return;
        }

        $plan = ProductPlan::find($this->plan_id);
        if (! $plan) {
            $this->plan_changed_preview = '';
            return;
        }

        $this->max_activations = $plan->max_activations ?? $this->max_activations;

        if ($plan->is_lifetime) {
            $this->expires_at           = '';
            $this->plan_changed_preview = 'Lifetime (no expiry)';
        } else {
            $newExpiry                  = now()->addDays($plan->duration_days);
            $this->expires_at           = $newExpiry->format('Y-m-d');
            $this->plan_changed_preview = $newExpiry->format('M d, Y') . " ({$plan->duration_days} days from today)";
        }
    }

    // ── Convert plan watcher ─────────────────────────────────────────────────
    public function updatedConvertPlanId(): void
    {
        if (! $this->convert_plan_id) {
            $this->convert_preview = '';
            return;
        }
        $plan = ProductPlan::find($this->convert_plan_id);
        if (! $plan) {
            $this->convert_preview = '';
            return;
        }

        if ($plan->is_lifetime) {
            $this->convert_preview = 'Lifetime — key will never expire';
        } else {
            $exp = now()->addDays($plan->duration_days);
            $this->convert_preview = 'Expires ' . $exp->format('M d, Y') . " ({$plan->duration_days} days from today)";
        }
    }

    // ── Customer search (create) ──────────────────────────────────────────────
    public function updatedCustomerSearch(): void
    {
        $this->customer_id    = '';
        $this->customer_label = '';
        $this->showCustomerDropdown = strlen($this->customer_search) >= 2;
    }

    public function selectCustomer(string $id, string $label): void
    {
        $this->customer_id          = $id;
        $this->customer_label       = $label;
        $this->customer_search      = $label;
        $this->showCustomerDropdown = false;
    }

    public function clearCustomer(): void
    {
        $this->customer_id          = '';
        $this->customer_label       = '';
        $this->customer_search      = '';
        $this->showCustomerDropdown = false;
    }

    // ── Customer search (trial) ───────────────────────────────────────────────
    public function updatedTrialCustomerSearch(): void
    {
        $this->trial_customer_id    = '';
        $this->trial_customer_label = '';
        $this->showTrialCustomerDropdown = strlen($this->trial_customer_search) >= 2;
    }

    public function selectTrialCustomer(string $id, string $label): void
    {
        $this->trial_customer_id          = $id;
        $this->trial_customer_label       = $label;
        $this->trial_customer_search      = $label;
        $this->showTrialCustomerDropdown  = false;
    }

    public function clearTrialCustomer(): void
    {
        $this->trial_customer_id          = '';
        $this->trial_customer_label       = '';
        $this->trial_customer_search      = '';
        $this->showTrialCustomerDropdown  = false;
    }

    // ── Open / Close ──────────────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetAllForms();
        $this->modalMode = 'create';
        $this->editing   = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openTrial(): void
    {
        $this->resetAllForms();
        $this->modalMode = 'trial';
        $this->showModal = true;
    }

    public function openEdit($id): void
    {
        $license = License::with(['customer', 'plan', 'product'])->findOrFail($id);
        $this->resetAllForms();
        $this->editingId             = $id;
        $this->product_id            = (string) $license->product_id;
        $this->plan_id               = (string) ($license->plan_id ?? '');
        $this->license_key           = $license->license_key;
        $this->status                = $license->status;
        $this->expires_at            = $license->expires_at ? $license->expires_at->format('Y-m-d') : '';
        $this->notes                 = $license->notes ?? '';
        $this->edit_customer_display = $license->customer?->name ?? $license->customer?->email ?? '(no customer)';
        $this->max_activations       = $license->max_activations ?? 1;
        $this->plan_changed_preview  = '';
        $this->editing               = true;
        $this->modalMode             = 'edit';
        $this->showModal             = true;
    }

    public function openConvertTrial(string $id): void
    {
        $license = License::with(['product', 'plan'])->findOrFail($id);
        $this->resetAllForms();
        $this->convertLicenseId     = $id;
        $this->convert_license_key  = $license->license_key;
        $this->convert_product_id   = (string) $license->product_id;
        $this->convert_plan_id      = '';
        $this->convert_preview      = '';
        $this->modalMode            = 'convert';
        $this->showModal            = true;
    }

    // ── Save (create) ─────────────────────────────────────────────────────────
    public function save(): void
    {
        if ($this->modalMode === 'create') {
            $this->validate([
                'product_id' => 'required|exists:products,id',
                'plan_id'    => 'required|exists:product_plans,id',
                'notes'      => 'nullable|string|max:1000',
            ]);

            $plan    = ProductPlan::findOrFail($this->plan_id);
            $expires = $plan->expires_at_for_new_license;
            $maxAct  = $plan->max_activations ?? config('licensing.max_activations', 1);

            $planName = strtolower($plan->name);
            $edition  = 'standard';
            if (str_contains($planName, 'enterprise'))   $edition = 'enterprise';
            elseif (str_contains($planName, 'pro'))      $edition = 'professional';

            $type = match (true) {
                $plan->is_lifetime         => 'lifetime',
                $plan->duration_days <= 31 => 'monthly',
                $plan->duration_days <= 93 => 'monthly',
                $plan->duration_days >= 365=> 'annual',
                default                    => 'lifetime',
            };

            $data = [
                'product_id'      => $this->product_id,
                'plan_id'         => $this->plan_id,
                'edition'         => $edition,
                'type'            => $type,
                'max_activations' => $maxAct,
                'status'          => 'active',
                'expires_at'      => $expires,
                'notes'           => $this->notes ?: null,
                'issued_by'       => auth()->id(),
            ];

            if ($this->customer_id) {
                $data['customer_id'] = $this->customer_id;
            }

            License::create($data);
            session()->flash('success', 'License created successfully.');

        } elseif ($this->modalMode === 'edit') {
            $this->validate([
                'status'          => 'required|in:active,inactive,expired,suspended,revoked,trial',
                'expires_at'      => 'nullable|date',
                'max_activations' => 'required|integer|min:1|max:999',
                'notes'           => 'nullable|string|max:1000',
            ]);

            $data = [
                'status'          => $this->status,
                'expires_at'      => $this->expires_at ?: null,
                'max_activations' => $this->max_activations,
                'notes'           => $this->notes ?: null,
            ];

            if ($this->plan_id) {
                $plan            = ProductPlan::findOrFail($this->plan_id);
                $data['plan_id'] = $this->plan_id;
                $data['expires_at'] = $plan->is_lifetime
                    ? null
                    : now()->addDays($plan->duration_days)->toDateTimeString();
                if ($plan->max_activations && $this->max_activations === 1) {
                    $data['max_activations'] = $plan->max_activations;
                }
                $data['type'] = match (true) {
                    $plan->is_lifetime         => 'lifetime',
                    $plan->duration_days <= 31 => 'monthly',
                    $plan->duration_days <= 93 => 'monthly',
                    $plan->duration_days >= 365=> 'annual',
                    default                    => 'lifetime',
                };
            }

            License::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'License updated.');

        } elseif ($this->modalMode === 'trial') {
            $this->validate([
                'trial_product_id' => 'required|exists:products,id',
                'trial_days'       => 'required|integer|min:1|max:365',
                'trial_max_act'    => 'required|integer|min:1|max:99',
                'trial_notes'      => 'nullable|string|max:1000',
            ]);

            $product   = Product::findOrFail($this->trial_product_id);
            $prefix    = strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $product->product_code ?? $product->name), 0, 4)) . 'T';
            $expiresAt = now()->addDays($this->trial_days);

            // Derive edition from trial plan if selected
            $edition   = 'standard';
            $planId    = $this->trial_plan_id ?: null;
            if ($planId) {
                $plan     = ProductPlan::find($planId);
                $planName = strtolower($plan?->name ?? '');
                if (str_contains($planName, 'enterprise'))  $edition = 'enterprise';
                elseif (str_contains($planName, 'pro'))     $edition = 'professional';
            }

            $data = [
                'product_id'      => $this->trial_product_id,
                'plan_id'         => $planId,
                'customer_id'     => $this->trial_customer_id ?: null,
                'license_key'     => License::generateUniqueKey($prefix),
                'key_prefix'      => $prefix,
                'edition'         => $edition,
                'type'            => 'trial',
                'max_activations' => $this->trial_max_act,
                'status'          => 'trial',
                'expires_at'      => $expiresAt,
                'is_renewable'    => false,
                'notes'           => $this->trial_notes ?: "Trial license — {$this->trial_days} days",
                'issued_by'       => auth()->id(),
            ];

            License::create($data);
            session()->flash('success', "Trial license created — expires {$expiresAt->format('M d, Y')} ({$this->trial_days} days).");

        } elseif ($this->modalMode === 'convert') {
            $this->validate([
                'convert_plan_id' => 'required|exists:product_plans,id',
            ]);

            $license = License::findOrFail($this->convertLicenseId);
            $plan    = ProductPlan::findOrFail($this->convert_plan_id);

            $planName = strtolower($plan->name);
            $edition  = 'standard';
            if (str_contains($planName, 'enterprise'))   $edition = 'enterprise';
            elseif (str_contains($planName, 'pro'))      $edition = 'professional';

            $type = match (true) {
                $plan->is_lifetime         => 'lifetime',
                $plan->duration_days <= 31 => 'monthly',
                $plan->duration_days <= 93 => 'monthly',
                $plan->duration_days >= 365=> 'annual',
                default                    => 'lifetime',
            };

            $license->update([
                'plan_id'         => $this->convert_plan_id,
                'status'          => 'active',
                'type'            => $type,
                'edition'         => $edition,
                'max_activations' => $plan->max_activations ?? $license->max_activations,
                'expires_at'      => $plan->is_lifetime ? null : now()->addDays($plan->duration_days),
                'is_renewable'    => true,
                'notes'           => trim(($license->notes ?? '') . "\nConverted from trial to {$plan->name} on " . now()->format('Y-m-d')),
            ]);

            session()->flash('success', "Trial converted to \"{$plan->name}\" plan. " . ($plan->is_lifetime ? 'Lifetime access granted.' : 'Expires ' . now()->addDays($plan->duration_days)->format('M d, Y') . '.'));
        }

        $this->showModal = false;
        $this->resetAllForms();
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

    private function resetAllForms(): void
    {
        // Create
        $this->product_id            = '';
        $this->plan_id               = '';
        $this->customer_search       = '';
        $this->customer_id           = '';
        $this->customer_label        = '';
        $this->showCustomerDropdown  = false;
        $this->notes                 = '';
        // Edit
        $this->status                = 'active';
        $this->expires_at            = '';
        $this->license_key           = '';
        $this->edit_customer_display = '';
        $this->max_activations       = 1;
        $this->plan_changed_preview  = '';
        $this->editingId             = null;
        $this->editing               = false;
        // Trial
        $this->trial_product_id           = '';
        $this->trial_plan_id              = '';
        $this->trial_days                 = 14;
        $this->trial_customer_search      = '';
        $this->trial_customer_id          = '';
        $this->trial_customer_label       = '';
        $this->showTrialCustomerDropdown  = false;
        $this->trial_notes                = '';
        $this->trial_max_act              = 1;
        // Convert
        $this->convertLicenseId           = null;
        $this->convert_plan_id            = '';
        $this->convert_preview            = '';
        $this->convert_license_key        = '';
        $this->convert_product_id         = '';
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
        return Product::where('is_active', true)->orderBy('name')->get();
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

    #[Computed]
    public function plansForTrialProduct()
    {
        if (! $this->trial_product_id) return collect();
        return ProductPlan::where('product_id', $this->trial_product_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }

    #[Computed]
    public function plansForConvert()
    {
        if (! $this->convert_product_id) return collect();
        return ProductPlan::where('product_id', $this->convert_product_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
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

    #[Computed]
    public function trialCustomerSuggestions()
    {
        if (strlen($this->trial_customer_search) < 2) return collect();
        return Customer::where(function ($q) {
            $q->where('name', 'like', '%' . $this->trial_customer_search . '%')
              ->orWhere('email', 'like', '%' . $this->trial_customer_search . '%');
        })->limit(8)->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function trialCount(): int
    {
        return License::where('status', 'trial')->count();
    }

    #[Computed]
    public function expiringCount(): int
    {
        return License::where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(30))
            ->count();
    }
}; ?>

<div>
    <x-slot:heading>Licenses</x-slot:heading>

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

        {{-- Quick-info pills --}}
        @if($this->trialCount > 0 || $this->expiringCount > 0)
            <div class="flex flex-wrap gap-3">
                @if($this->trialCount > 0)
                    <button wire:click="$set('filterStatus', 'trial')"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-50 border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $this->trialCount }} Active Trial{{ $this->trialCount !== 1 ? 's' : '' }}
                    </button>
                @endif
                @if($this->expiringCount > 0)
                    <div class="inline-flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        {{ $this->expiringCount }} Expiring in 30 days
                    </div>
                @endif
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-1 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           placeholder="Search key or customer…"
                           class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-cyan-400 focus:outline-none w-52">
                </div>
                <select wire:model.live="filterStatus"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="trial">Trial</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                    <option value="suspended">Suspended</option>
                    <option value="revoked">Revoked</option>
                </select>
                <select wire:model.live="filterProduct"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
                    <option value="">All Products</option>
                    @foreach($this->products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                {{-- Issue Trial --}}
                <button wire:click="openTrial"
                        class="flex items-center gap-2 rounded-xl border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Issue Trial
                </button>
                {{-- New License --}}
                <button wire:click="openCreate"
                        class="flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    New License
                </button>
            </div>
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
                                    @elseif($license->type)
                                        <p class="text-xs text-slate-400 capitalize">{{ $license->type }} · {{ $license->edition }}</p>
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
                                    <span class="{{ ($license->getCurrentActivationsCount() ?? 0) >= $license->max_activations ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                        {{ $license->getCurrentActivationsCount() ?? 0 }} / {{ $license->max_activations }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs">
                                    @if(! $license->expires_at)
                                        <span class="text-slate-500">∞ Lifetime</span>
                                    @else
                                        @php
                                            $daysLeft = now()->diffInDays($license->expires_at, false);
                                        @endphp
                                        <span class="{{ $daysLeft < 0 ? 'text-red-600' : ($daysLeft <= 30 ? 'text-amber-600 font-semibold' : 'text-slate-600') }}">
                                            {{ $license->expires_at->format('M d, Y') }}
                                        </span>
                                        @if($daysLeft >= 0 && $daysLeft <= 30)
                                            <span class="text-amber-500 text-xs block">{{ $daysLeft }}d left</span>
                                        @endif
                                    @endif
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
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Convert trial → plan --}}
                                        @if($license->status === 'trial')
                                            <button wire:click="openConvertTrial('{{ $license->id }}')"
                                                    class="rounded-lg px-2 py-1 text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors" title="Convert to Plan">
                                                Upgrade
                                            </button>
                                        @endif
                                        {{-- Edit --}}
                                        <button wire:click="openEdit('{{ $license->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        {{-- Revoke --}}
                                        @if(! in_array($license->status, ['revoked']))
                                            <button wire:click="revoke('{{ $license->id }}')"
                                                    wire:confirm="Revoke this license? The customer will lose access."
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Revoke">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Delete --}}
                                        <button wire:click="deleteLicense('{{ $license->id }}')"
                                                wire:confirm="Permanently delete this license?"
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

    {{-- ═══════════════════════════════════════════════════════════════════
         Universal Modal — switches content based on $modalMode
         ═══════════════════════════════════════════════════════════════════ --}}
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
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100
                    @if($modalMode === 'trial') bg-gradient-to-r from-blue-50 to-slate-50
                    @elseif($modalMode === 'convert') bg-gradient-to-r from-purple-50 to-slate-50
                    @else bg-gradient-to-r from-cyan-50 to-slate-50 @endif">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl
                            @if($modalMode === 'trial') bg-blue-100
                            @elseif($modalMode === 'convert') bg-purple-100
                            @else bg-cyan-100 @endif">
                            @if($modalMode === 'trial')
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($modalMode === 'convert')
                                <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                @if($modalMode === 'create') Issue New License
                                @elseif($modalMode === 'edit') Edit License
                                @elseif($modalMode === 'trial') Issue Trial License
                                @elseif($modalMode === 'convert') Convert Trial → Plan
                                @endif
                            </h2>
                            <p class="text-xs text-slate-400">
                                @if($modalMode === 'create') Select product and plan to generate a license
                                @elseif($modalMode === 'edit') Update status, expiry or plan
                                @elseif($modalMode === 'trial') Provision a time-limited trial key
                                @elseif($modalMode === 'convert') Upgrade this trial to a paid plan
                                @endif
                            </p>
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

                    {{-- ════════════════════ CREATE ════════════════════ --}}
                    @if($modalMode === 'create')

                    {{-- Product --}}
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

                    {{-- Plan --}}
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
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-semibold text-slate-800">{{ $plan->name }}</span>
                                                <span class="text-xs text-slate-400">· {{ $plan->billing_label }}</span>
                                                @if($plan->trial_days > 0)
                                                    <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 text-xs px-2 py-0.5 font-medium">{{ $plan->trial_days }}d trial available</span>
                                                @endif
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

                    {{-- Customer (optional) --}}
                    <div x-data>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold mr-1.5">3</span>
                            Customer <span class="text-xs font-normal text-slate-400 ml-1">(optional)</span>
                        </label>
                        @if($customer_id)
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
                            <div class="relative">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input type="text" wire:model.live.debounce.200ms="customer_search"
                                           placeholder="Search by name or email…"
                                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-cyan-500 focus:outline-none"
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
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
                        <textarea wire:model="notes" rows="2"
                                  placeholder="Order reference, internal memo…"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none resize-none"></textarea>
                    </div>

                    {{-- ════════════════════ EDIT ════════════════════ --}}
                    @elseif($modalMode === 'edit')

                    {{-- License key (read-only) --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5">License Key</p>
                        <p class="font-mono text-sm font-semibold text-slate-800">{{ $license_key }}</p>
                    </div>

                    {{-- Customer (read-only) --}}
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-slate-400">Customer</p>
                            <p class="text-sm font-medium text-slate-800">{{ $edit_customer_display }}</p>
                        </div>
                    </div>

                    {{-- Plan change --}}
                    @if($product_id)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Change Plan</label>
                        <select wire:model.live="plan_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none">
                            <option value="">— Keep current plan —</option>
                            @foreach($this->plansForProduct as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} · {{ $plan->billing_label }} · {{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</option>
                            @endforeach
                        </select>
                        @if($plan_changed_preview)
                            <div class="mt-2 flex items-center gap-2 rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2">
                                <svg class="h-4 w-4 text-cyan-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-cyan-700">New expiry: <span class="font-semibold">{{ $plan_changed_preview }}</span></p>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <select wire:model="status"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="revoked">Revoked</option>
                            <option value="trial">Trial</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    {{-- Max Activations --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Activations (Seats)</label>
                        <div class="flex items-center gap-3">
                            <input type="number" wire:model="max_activations" min="1" max="999"
                                   class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:border-cyan-500 focus:outline-none">
                            <p class="text-xs text-slate-400">Simultaneous device activations allowed.</p>
                        </div>
                        @error('max_activations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expires At --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Expires At <span class="text-slate-400 font-normal text-xs">(leave blank = lifetime)</span>
                        </label>
                        <input type="date" wire:model="expires_at"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none">
                        <p class="mt-1 text-xs text-slate-400">Changing the plan above auto-fills this.</p>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none resize-none"></textarea>
                    </div>

                    {{-- ════════════════════ TRIAL ════════════════════ --}}
                    @elseif($modalMode === 'trial')

                    {{-- Info banner --}}
                    <div class="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-blue-700">Trial licenses have a fixed duration and <strong>cannot be renewed</strong>. Use <em>Upgrade</em> on the license to convert it to a paid plan when the customer is ready.</p>
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold mr-1.5">1</span>
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="trial_product_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none bg-white">
                            <option value="">— Select a product —</option>
                            @foreach($this->products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}{{ $p->product_code ? ' ('.$p->product_code.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('trial_product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Plan (optional — which plan is the trial leading to) --}}
                    @if($trial_product_id && $this->plansForTrialProduct->isNotEmpty())
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold mr-1.5">2</span>
                            Target Plan <span class="text-slate-400 font-normal text-xs ml-1">(optional — links trial to a plan)</span>
                        </label>
                        <select wire:model.live="trial_plan_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none bg-white">
                            <option value="">— General trial (no specific plan) —</option>
                            @foreach($this->plansForTrialProduct as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->name }} · {{ $plan->billing_label }}
                                    @if($plan->trial_days > 0) ({{ $plan->trial_days }}d trial) @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Linking a plan pre-fills the trial duration and makes upgrade seamless.</p>
                    </div>
                    @endif

                    {{-- Duration --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold mr-1.5">{{ $trial_product_id ? '3' : '2' }}</span>
                            Trial Duration <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" wire:model.live="trial_days" min="1" max="365"
                                   class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:border-blue-400 focus:outline-none">
                            <span class="text-sm text-slate-600">days</span>
                            @if($trial_days > 0)
                                <span class="text-xs text-blue-600 font-medium">
                                    Expires {{ now()->addDays($trial_days)->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 flex gap-2 flex-wrap">
                            @foreach([7, 14, 21, 30] as $d)
                                <button type="button" wire:click="$set('trial_days', {{ $d }})"
                                        class="rounded-lg px-3 py-1 text-xs font-medium border transition-colors
                                               {{ $trial_days == $d ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300' }}">
                                    {{ $d }}d
                                </button>
                            @endforeach
                        </div>
                        @error('trial_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Max activations --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Activations</label>
                        <input type="number" wire:model="trial_max_act" min="1" max="99"
                               class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:border-blue-400 focus:outline-none">
                    </div>

                    {{-- Customer (optional) --}}
                    <div x-data>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Assign to Customer <span class="text-slate-400 font-normal text-xs">(optional)</span>
                        </label>
                        @if($trial_customer_id)
                            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                                <div class="h-8 w-8 rounded-full bg-green-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-green-700">{{ strtoupper(substr($trial_customer_label, 0, 1)) }}</span>
                                </div>
                                <p class="text-sm font-medium text-green-800 flex-1 truncate">{{ $trial_customer_label }}</p>
                                <button type="button" wire:click="clearTrialCustomer" class="text-green-600 hover:text-red-500 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input type="text" wire:model.live.debounce.200ms="trial_customer_search"
                                       placeholder="Search by name or email…"
                                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-blue-400 focus:outline-none"
                                       autocomplete="off">
                                @if($showTrialCustomerDropdown && $this->trialCustomerSuggestions->isNotEmpty())
                                    <div class="absolute z-10 top-full left-0 right-0 mt-1 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
                                        @foreach($this->trialCustomerSuggestions as $cust)
                                            <button type="button"
                                                    wire:click="selectTrialCustomer('{{ $cust->id }}', '{{ addslashes($cust->name ?? $cust->email) }}')"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-blue-50 transition-colors">
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
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
                        <textarea wire:model="trial_notes" rows="2"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none resize-none"
                                  placeholder="e.g. Sales demo for Acme Corp…"></textarea>
                    </div>

                    {{-- ════════════════════ CONVERT ════════════════════ --}}
                    @elseif($modalMode === 'convert')

                    {{-- Current trial key --}}
                    <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3">
                        <p class="text-xs text-blue-600 mb-0.5 font-medium">Trial License</p>
                        <p class="font-mono text-sm font-semibold text-slate-800">{{ $convert_license_key }}</p>
                        <p class="text-xs text-blue-500 mt-1">This key will be converted to a full license on the selected plan.</p>
                    </div>

                    {{-- Plan selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Select Plan <span class="text-red-500">*</span>
                        </label>
                        @if($this->plansForConvert->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                No active plans found for this product.
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($this->plansForConvert as $plan)
                                    <label class="flex items-center gap-3 rounded-xl border-2 cursor-pointer px-4 py-3 transition-colors
                                                  {{ $convert_plan_id === (string)$plan->id ? 'border-purple-500 bg-purple-50' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                        <input type="radio" wire:model.live="convert_plan_id" value="{{ $plan->id }}" class="text-purple-600 focus:ring-purple-500">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-slate-800">{{ $plan->name }}</span>
                                                <span class="text-xs text-slate-400">· {{ $plan->billing_label }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if($plan->is_on_sale)
                                                    <span class="text-sm font-bold text-purple-700">{{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</span>
                                                    <span class="text-xs text-slate-400 line-through">{{ number_format($plan->price, 2) }}</span>
                                                @else
                                                    <span class="text-sm font-bold text-slate-700">{{ $plan->currency }} {{ number_format($plan->price, 2) }}</span>
                                                @endif
                                                @if($plan->max_activations)
                                                    <span class="text-xs text-slate-400">· {{ $plan->max_activations }} device(s)</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($convert_plan_id === (string)$plan->id)
                                            <svg class="h-5 w-5 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                            @error('convert_plan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- Expiry preview --}}
                    @if($convert_preview)
                        <div class="flex items-center gap-2 rounded-xl border border-purple-200 bg-purple-50 px-4 py-3">
                            <svg class="h-4 w-4 text-purple-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-purple-700 font-medium">{{ $convert_preview }}</p>
                        </div>
                    @endif

                    @endif

                    {{-- ════════════ Actions ════════════ --}}
                    <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors
                                    @if($modalMode === 'trial') bg-blue-600 hover:bg-blue-700
                                    @elseif($modalMode === 'convert') bg-purple-600 hover:bg-purple-700
                                    @else bg-cyan-600 hover:bg-cyan-700 @endif">
                            <span wire:loading.remove wire:target="save">
                                @if($modalMode === 'create') Issue License
                                @elseif($modalMode === 'edit') Save Changes
                                @elseif($modalMode === 'trial') Issue Trial
                                @elseif($modalMode === 'convert') Convert to Plan
                                @endif
                            </span>
                            <span wire:loading wire:target="save">Processing…</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
