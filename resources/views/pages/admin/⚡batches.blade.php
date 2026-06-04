<?php

use App\Models\LicenseBatch;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\Reseller;
use App\Services\BatchExportService;
use App\Services\LicenseGeneratorService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('License Batches — ExchoLicense')] class extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterProduct = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool    $showModal   = false;
    public bool    $editing     = false;
    public ?string $editingId   = null;
    public bool    $showDetail  = false;
    public ?string $detailId    = null;

    // ── Shared form fields ────────────────────────────────────────────────────
    public string $product_id      = '';
    public string $plan_id         = '';
    public string $label           = '';
    public int    $quantity        = 10;
    public string $key_prefix      = '';
    public string $reseller_id     = '';
    public string $notes           = '';

    // ── Edit-only fields ──────────────────────────────────────────────────────
    public string $status          = 'active';
    public string $expires_at      = '';
    public int    $max_activations = 1;
    public string $plan_preview    = '';   // live expiry preview

    // ─────────────────────────────────────────────────────────────────────────

    public function updatingSearch(): void { $this->resetPage(); }

    public function updatedProductId(): void
    {
        $this->plan_id      = '';
        $this->plan_preview = '';
        $this->key_prefix   = '';
    }

    public function updatedPlanId(): void
    {
        if (! $this->plan_id) {
            $this->plan_preview = '';
            return;
        }

        $plan = ProductPlan::find($this->plan_id);
        if (! $plan) {
            $this->plan_preview = '';
            return;
        }

        if ($plan->is_lifetime) {
            $this->expires_at   = '';
            $this->plan_preview = 'Lifetime — keys never expire';
        } elseif ($plan->duration_days) {
            $exp                = now()->addDays($plan->duration_days);
            $this->expires_at   = $exp->format('Y-m-d');
            $this->plan_preview = $exp->format('M d, Y') . " ({$plan->duration_days} days from issue)";
        } else {
            $this->plan_preview = '';
        }
    }

    // ── Open / Close ──────────────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editing   = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $batch = LicenseBatch::findOrFail($id);

        $this->editingId       = $id;
        $this->product_id      = (string) $batch->product_id;
        $this->plan_id         = (string) ($batch->plan_id ?? '');
        $this->label           = $batch->label;
        $this->quantity        = $batch->quantity;
        $this->key_prefix      = $batch->key_prefix;
        $this->reseller_id     = (string) ($batch->reseller_id ?? '');
        $this->status          = $batch->status;
        $this->expires_at      = $batch->expires_at ? $batch->expires_at->format('Y-m-d') : '';
        $this->max_activations = $batch->max_activations ?? 1;
        $this->notes           = $batch->notes ?? '';
        $this->plan_preview    = '';
        $this->editing         = true;
        $this->showModal       = true;
    }

    public function openDetail(string $id): void
    {
        $this->detailId   = $id;
        $this->showDetail = true;
    }

    // ── Save (create or edit) ─────────────────────────────────────────────────
    public function save(): void
    {
        if (! $this->editing) {
            $this->validate([
                'product_id' => 'required|exists:products,id',
                'plan_id'    => 'required|exists:product_plans,id',
                'label'      => 'required|string|max:255',
                'quantity'   => 'required|integer|min:1|max:10000',
                'key_prefix' => 'nullable|string|max:8|alpha_num',
                'reseller_id'=> 'nullable|exists:resellers,id',
                'notes'      => 'nullable|string|max:1000',
            ]);

            $plan    = ProductPlan::findOrFail($this->plan_id);
            $product = Product::findOrFail($this->product_id);
            $prefix  = $this->key_prefix
                ? strtoupper($this->key_prefix)
                : strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $product->product_code ?? $product->name), 0, 6));

            // Derive type from plan duration
            $licenseType = match (true) {
                $plan->is_lifetime                  => 'lifetime',
                $plan->duration_days <= 31          => 'monthly',
                $plan->duration_days <= 93          => 'monthly',
                $plan->duration_days >= 365         => 'annual',
                $plan->trial_days > 0               => 'trial',
                default                             => 'lifetime',
            };

            // Derive edition from plan name
            $planName = strtolower($plan->name);
            $edition  = 'standard';
            if (str_contains($planName, 'enterprise'))   $edition = 'enterprise';
            elseif (str_contains($planName, 'pro'))      $edition = 'professional';
            elseif (str_contains($planName, 'trial'))    $edition = 'trial';

            $expiresAt    = $plan->is_lifetime ? null : now()->addDays($plan->duration_days);
            $durationDays = $plan->is_lifetime ? null : $plan->duration_days;

            $params = [
                'product_id'      => $this->product_id,
                'plan_id'         => $this->plan_id,
                'label'           => $this->label,
                'quantity'        => $this->quantity,
                'key_prefix'      => $prefix,
                'license_type'    => $licenseType,
                'edition'         => $edition,
                'max_activations' => $plan->max_activations ?? 1,
                'expires_at'      => $expiresAt?->format('Y-m-d'),
                'duration_days'   => $durationDays,
                'reseller_id'     => $this->reseller_id ?: null,
                'reseller_tag'    => $this->reseller_id ? Reseller::find($this->reseller_id)?->reseller_code : null,
                'notes'           => $this->notes ?: null,
            ];

            $batch = app(LicenseGeneratorService::class)->generateBatch($params, auth()->id());

            $this->showModal = false;
            $this->resetForm();
            session()->flash('success', "Batch \"{$batch->label}\" created — {$batch->total_generated} keys generated.");

        } else {
            $this->validate([
                'label'          => 'required|string|max:255',
                'status'         => 'required|in:active,expired,revoked,archived',
                'expires_at'     => 'nullable|date',
                'max_activations'=> 'required|integer|min:1|max:9999',
                'notes'          => 'nullable|string|max:1000',
            ]);

            $batch = LicenseBatch::findOrFail($this->editingId);

            // If plan changed, recalculate expiry
            $updateData = [
                'label'       => $this->label,
                'status'      => $this->status,
                'expires_at'  => $this->expires_at ?: null,
                'reseller_id' => $this->reseller_id ?: null,
                'notes'       => $this->notes ?: null,
            ];

            if ($this->plan_id && $this->plan_id !== (string)($batch->plan_id ?? '')) {
                $plan = ProductPlan::find($this->plan_id);
                if ($plan) {
                    $updateData['plan_id']       = $this->plan_id;
                    $updateData['expires_at']    = $plan->is_lifetime ? null : now()->addDays($plan->duration_days)->format('Y-m-d');
                    $updateData['duration_days'] = $plan->is_lifetime ? null : $plan->duration_days;
                }
            }

            $batch->update($updateData);

            // max_activations lives on each license, not on the batch or the plan.
            // Update all non-revoked licenses in this batch to the new value.
            $batch->licenses()->where('status', '!=', 'revoked')->update([
                'max_activations' => $this->max_activations,
            ]);

            $this->showModal = false;
            $this->resetForm();
            session()->flash('success', "Batch \"{$batch->label}\" updated.");
        }
    }

    public function exportCsv(string $batchId): void
    {
        $batch  = LicenseBatch::findOrFail($batchId);
        $export = app(BatchExportService::class)->exportCsv($batch, auth()->id());
        session()->flash('success', "CSV export ready: {$export->filename} ({$export->record_count} records).");
    }

    public function revokeBatch(string $batchId): void
    {
        $batch = LicenseBatch::findOrFail($batchId);
        $batch->update(['status' => 'revoked']);
        $batch->licenses()->where('status', '!=', 'revoked')->update(['status' => 'revoked']);
        session()->flash('success', "Batch \"{$batch->label}\" and all its licenses revoked.");
    }

    public function archiveBatch(string $batchId): void
    {
        $batch = LicenseBatch::findOrFail($batchId);
        $batch->update(['status' => 'archived']);
        session()->flash('success', "Batch \"{$batch->label}\" archived.");
    }

    public function resetForm(): void
    {
        $this->product_id      = '';
        $this->plan_id         = '';
        $this->label           = '';
        $this->quantity        = 10;
        $this->key_prefix      = '';
        $this->reseller_id     = '';
        $this->status          = 'active';
        $this->expires_at      = '';
        $this->max_activations = 1;
        $this->notes           = '';
        $this->plan_preview    = '';
        $this->editingId       = null;
        $this->resetValidation();
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function batches()
    {
        return LicenseBatch::query()
            ->with(['product', 'plan', 'reseller', 'createdBy'])
            ->when($this->search, fn ($q) =>
                $q->where('label', 'like', "%{$this->search}%")
                  ->orWhere('batch_code', 'like', "%{$this->search}%"))
            ->when($this->filterStatus,  fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterProduct, fn ($q) => $q->where('product_id', $this->filterProduct))
            ->latest()
            ->paginate(12);
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
    public function resellers()
    {
        return Reseller::where('status', 'active')
            ->whereIn('type', ['wholesale', 'both'])
            ->orderBy('company_name')
            ->get();
    }

    #[Computed]
    public function detailBatch(): ?LicenseBatch
    {
        if (! $this->detailId) return null;
        return LicenseBatch::with(['product', 'plan', 'reseller', 'licenses' => fn($q) => $q->limit(10), 'exports'])->find($this->detailId);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_batches'   => LicenseBatch::count(),
            'total_generated' => LicenseBatch::sum('total_generated'),
            'total_used'      => LicenseBatch::sum('total_used'),
            'active_batches'  => LicenseBatch::where('status', 'active')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>License Batches</x-slot:heading>

    <div class="space-y-6">

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
                ['label' => 'Total Batches',  'value' => $this->stats['total_batches'],   'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'cyan'],
                ['label' => 'Keys Generated', 'value' => $this->stats['total_generated'], 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', 'color' => 'violet'],
                ['label' => 'Keys Assigned',  'value' => $this->stats['total_used'],      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
                ['label' => 'Active Batches', 'value' => $this->stats['active_batches'],  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'blue'],
            ] as $stat)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 flex items-center gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-{{ $stat['color'] }}-50">
                        <svg class="h-5 w-5 text-{{ $stat['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-1 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search label or code…"
                           class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400 w-52">
                </div>
                <select wire:model.live="filterStatus"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="revoked">Revoked</option>
                    <option value="archived">Archived</option>
                </select>
                <select wire:model.live="filterProduct"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
                    <option value="">All Products</option>
                    @foreach($this->products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="openCreate"
                    class="flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Generate Batch
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Batch</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product / Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Reseller</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Keys</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usage</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Expires</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->batches as $batch)
                            @php
                                $usedPct = $batch->total_generated > 0
                                    ? round(($batch->total_used / $batch->total_generated) * 100)
                                    : 0;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-slate-900">{{ $batch->label }}</p>
                                    <p class="text-xs font-mono text-slate-400">{{ $batch->batch_code }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800">{{ $batch->product?->name ?? '—' }}</p>
                                    @if($batch->plan)
                                        <p class="text-xs text-slate-400">{{ $batch->plan->name }} · {{ $batch->plan->billing_label }}</p>
                                    @else
                                        <p class="text-xs text-slate-400 capitalize">{{ $batch->license_type }} · {{ $batch->edition }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($batch->reseller)
                                        <p class="text-sm text-slate-700">{{ $batch->reseller->display_name }}</p>
                                        <p class="text-xs font-mono text-slate-400">{{ $batch->reseller->reseller_code }}</p>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Direct</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-700">
                                    {{ number_format($batch->total_generated) }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full transition-all {{ $usedPct > 80 ? 'bg-amber-500' : 'bg-cyan-500' }}"
                                                 style="width: {{ $usedPct }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 tabular-nums">{{ $usedPct }}%</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $batch->total_used }} / {{ $batch->total_generated }}</p>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-600">
                                    {{ $batch->expires_at ? $batch->expires_at->format('M d, Y') : '∞ Lifetime' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ match($batch->status) {
                                            'active'   => 'bg-green-50 text-green-700 ring-1 ring-green-600/20',
                                            'expired'  => 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
                                            'revoked'  => 'bg-slate-100 text-slate-500',
                                            'archived' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                                            default    => 'bg-slate-100 text-slate-500',
                                        } }}">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <button wire:click="openDetail('{{ $batch->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="View Details">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        {{-- Edit --}}
                                        @if($batch->status !== 'revoked')
                                            <button wire:click="openEdit('{{ $batch->id }}')"
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Export CSV --}}
                                        <button wire:click="exportCsv('{{ $batch->id }}')"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Export CSV">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </button>
                                        {{-- Archive --}}
                                        @if($batch->status === 'active')
                                            <button wire:click="archiveBatch('{{ $batch->id }}')"
                                                    wire:confirm="Archive this batch?"
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Archive">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Revoke --}}
                                        @if(! in_array($batch->status, ['revoked']))
                                            <button wire:click="revokeBatch('{{ $batch->id }}')"
                                                    wire:confirm="Revoke ALL {{ $batch->total_generated }} licenses in this batch? This cannot be undone."
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Revoke All">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No batches yet.</p>
                                        <button wire:click="openCreate" class="text-sm font-semibold text-cyan-600 hover:underline">Generate your first batch →</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div>{{ $this->batches->links() }}</div>

    </div>

    {{-- ═══════════════ Create / Edit Modal ═══════════════ --}}
    <div
        x-data
        x-show="$wire.showModal"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        style="display:none; position:fixed; inset:0; z-index:200; overflow-y:auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div style="position:fixed;inset:0;background:rgba(15,23,42,.65);backdrop-filter:blur(3px);"
                 x-on:click="$wire.set('showModal', false)"></div>

            <div class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl overflow-hidden" x-on:click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-cyan-50 to-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100">
                            <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $editing ? 'Edit Batch' : 'Generate License Batch' }}</h2>
                            <p class="text-xs text-slate-400">{{ $editing ? 'Update batch settings and expiry' : 'Select a product and plan to generate keys in bulk' }}</p>
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
                    {{-- ══════════ CREATE FORM ══════════ --}}

                    {{-- Step 1: Label --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">1</span>
                            Batch Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="label" placeholder="e.g. Q2 2025 Reseller Pack"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Step 2: Product --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">2</span>
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

                    {{-- Step 3: Plan --}}
                    @if($product_id)
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">3</span>
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
                                                @if($plan->trial_days > 0)
                                                    <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 text-xs px-2 py-0.5 font-medium">{{ $plan->trial_days }}d trial</span>
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
                                                    <span class="text-xs text-slate-400">· {{ $plan->max_activations }} seat(s)</span>
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

                    {{-- Expiry Preview --}}
                    @if($plan_preview)
                        <div class="flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3">
                            <svg class="h-4 w-4 text-cyan-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-cyan-700">Keys will expire: <span class="font-semibold">{{ $plan_preview }}</span></p>
                        </div>
                    @endif

                    {{-- Step 4: Quantity & Prefix --}}
                    @if($plan_id)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold mr-1.5">4</span>
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="quantity" min="1" max="10000"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Key Prefix <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
                            <input type="text" wire:model="key_prefix" maxlength="8" placeholder="e.g. EXCL"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-mono uppercase focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <p class="mt-1 text-xs text-slate-400">PREFIX-XXXX-XXXX-XXXX</p>
                        </div>
                    </div>

                    {{-- Step 5: Reseller (optional) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Assign to Reseller <span class="text-slate-400 font-normal text-xs">(optional — wholesale only)</span>
                        </label>
                        @if($this->resellers->isEmpty())
                            <p class="text-xs text-slate-400 italic">No active wholesale resellers found.</p>
                        @else
                            <select wire:model="reseller_id"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white">
                                <option value="">— Direct / No reseller —</option>
                                @foreach($this->resellers as $r)
                                    <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->reseller_code }}) — {{ $r->discount_rate }}% disc.</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 resize-none"
                                  placeholder="Internal memo for this batch…"></textarea>
                    </div>
                    @endif

                    @else
                    {{-- ══════════ EDIT FORM ══════════ --}}

                    {{-- Read-only batch code --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400">Batch Code</p>
                            <p class="font-mono text-sm font-semibold text-slate-800">
                                {{ LicenseBatch::find($editingId)?->batch_code ?? '—' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400">Total Keys</p>
                            <p class="text-sm font-bold text-slate-700">{{ number_format(LicenseBatch::find($editingId)?->total_generated ?? 0) }}</p>
                        </div>
                    </div>

                    {{-- Label --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Batch Label <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="label"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Plan Change --}}
                    @if($product_id)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Change Plan</label>
                        <select wire:model.live="plan_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="">— Keep current plan / expiry —</option>
                            @foreach($this->plansForProduct as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} · {{ $plan->billing_label }} · {{ $plan->currency }} {{ number_format($plan->effective_price, 2) }}</option>
                            @endforeach
                        </select>
                        @if($plan_preview)
                            <div class="mt-2 flex items-center gap-2 rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2">
                                <svg class="h-4 w-4 text-cyan-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-cyan-700">New expiry: <span class="font-semibold">{{ $plan_preview }}</span></p>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <select wire:model="status"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="archived">Archived</option>
                            <option value="revoked">Revoked</option>
                        </select>
                    </div>

                    {{-- Max Activations --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Activations per Key</label>
                        <input type="number" wire:model="max_activations" min="1" max="9999"
                               class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm text-center font-semibold focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        <p class="mt-1 text-xs text-slate-400">Applied to all active licenses in this batch. The plan informs price &amp; duration only.</p>
                        @error('max_activations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expiry Override --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Expires At <span class="text-slate-400 font-normal text-xs">(leave blank = lifetime)</span>
                        </label>
                        <input type="date" wire:model="expires_at"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    </div>

                    {{-- Reseller --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Reseller</label>
                        <select wire:model="reseller_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="">— Direct / No reseller —</option>
                            @foreach($this->resellers as $r)
                                <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->reseller_code }})</option>
                            @endforeach
                        </select>
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
                            <span wire:loading.remove wire:target="save">
                                {{ $editing ? 'Save Changes' : 'Generate Batch' }}
                            </span>
                            <span wire:loading wire:target="save">{{ $editing ? 'Saving…' : 'Generating…' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════ Detail Panel ═══════════════ --}}
    <div
        x-data
        x-show="$wire.showDetail"
        x-on:keydown.escape.window="$wire.set('showDetail', false)"
        style="display:none; position:fixed; inset:0; z-index:200; overflow-y:auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div style="position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(2px);"
                 x-on:click="$wire.set('showDetail', false)"></div>

            @if($this->detailBatch)
                @php $b = $this->detailBatch; $usedPct = $b->total_generated > 0 ? round(($b->total_used / $b->total_generated) * 100) : 0; @endphp
                <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="mb-5 flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $b->label }}</h2>
                            <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $b->batch_code }}</p>
                        </div>
                        <button wire:click="$set('showDetail', false)"
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Info grid --}}
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Product</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $b->product?->name ?? '—' }}</p>
                            @if($b->plan)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $b->plan->name }} · {{ $b->plan->billing_label }}</p>
                            @endif
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Reseller</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $b->reseller?->display_name ?? 'Direct' }}</p>
                            @if($b->reseller)
                                <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $b->reseller->reseller_code }}</p>
                            @endif
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Keys</p>
                            <p class="text-sm font-semibold text-slate-800">{{ number_format($b->total_generated) }} generated</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $b->total_used }} used · {{ $b->total_revoked }} revoked</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Expiry</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $b->expires_at ? $b->expires_at->format('M d, Y') : 'Lifetime' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Max {{ $b->max_activations }} activation(s) per key</p>
                        </div>
                    </div>

                    {{-- Usage bar --}}
                    <div class="mb-5">
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Usage</span>
                            <span>{{ $usedPct }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $usedPct > 80 ? 'bg-amber-500' : 'bg-cyan-500' }}"
                                 style="width: {{ $usedPct }}%"></div>
                        </div>
                    </div>

                    {{-- Recent keys --}}
                    @if($b->licenses->count())
                        <div class="mb-5">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Recent Keys ({{ $b->licenses->count() > 10 ? '10 of '.$b->total_generated : $b->licenses->count() }})</p>
                            <div class="space-y-1">
                                @foreach($b->licenses->take(8) as $lic)
                                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5">
                                        <code class="text-xs font-mono text-slate-700">{{ $lic->license_key }}</code>
                                        <div class="flex items-center gap-2">
                                            @if($lic->customer_id)
                                                <span class="text-xs text-slate-400">Assigned</span>
                                            @endif
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ match($lic->status) {
                                                    'active'  => 'bg-green-50 text-green-700',
                                                    'trial'   => 'bg-blue-50 text-blue-700',
                                                    'revoked' => 'bg-red-50 text-red-600',
                                                    default   => 'bg-slate-100 text-slate-500',
                                                } }}">{{ ucfirst($lic->status) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button wire:click="exportCsv('{{ $b->id }}')"
                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Export CSV
                        </button>
                        @if($b->status !== 'revoked')
                            <button wire:click="openEdit('{{ $b->id }}')" wire:click="$set('showDetail', false)"
                                    class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                                Edit Batch
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
