<?php

use App\Models\LicenseBatch;
use App\Models\Product;
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

    public string $search       = '';
    public string $filterStatus = '';
    public bool   $showModal    = false;
    public bool   $showDetail   = false;
    public ?int   $detailId     = null;

    // Batch generation form
    public int    $product_id      = 0;
    public string $label           = '';
    public int    $quantity        = 10;
    public string $key_prefix      = '';
    public string $license_type    = 'lifetime';
    public string $edition         = 'standard';
    public int    $max_activations = 1;
    public string $expires_at      = '';
    public int    $duration_days   = 0;
    public string $reseller_tag    = '';
    public string $notes           = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function openGenerate(): void
    {
        $this->reset([
            'product_id', 'label', 'quantity', 'key_prefix', 'license_type',
            'edition', 'max_activations', 'expires_at', 'duration_days',
            'reseller_tag', 'notes',
        ]);
        $this->quantity        = 10;
        $this->license_type    = 'lifetime';
        $this->edition         = 'standard';
        $this->max_activations = 1;
        $this->showModal       = true;
    }

    public function openDetail(int $id): void
    {
        $this->detailId    = $id;
        $this->showDetail  = true;
    }

    public function generate(): void
    {
        $this->validate([
            'product_id'      => 'required|integer|exists:products,id',
            'label'           => 'required|string|max:255',
            'quantity'        => 'required|integer|min:1|max:10000',
            'key_prefix'      => 'nullable|string|max:8|alpha_num',
            'license_type'    => 'required|in:lifetime,monthly,annual,yearly,trial,custom',
            'edition'         => 'required|in:standard,professional,enterprise,trial',
            'max_activations' => 'required|integer|min:1|max:9999',
        ]);

        $product = Product::find($this->product_id);
        $prefix  = $this->key_prefix ?: strtoupper(substr($product->product_code, 0, 4));

        $params = [
            'product_id'      => $this->product_id,
            'label'           => $this->label,
            'quantity'        => $this->quantity,
            'key_prefix'      => $prefix,
            'license_type'    => $this->license_type,
            'edition'         => $this->edition,
            'max_activations' => $this->max_activations,
            'expires_at'      => $this->expires_at ?: null,
            'duration_days'   => $this->duration_days ?: null,
            'reseller_tag'    => $this->reseller_tag ?: null,
            'notes'           => $this->notes ?: null,
        ];

        $batch = app(LicenseGeneratorService::class)->generateBatch($params, auth()->id());

        $this->showModal = false;
        session()->flash('success', "Batch \"{$batch->label}\" created with {$batch->total_generated} licenses.");
    }

    public function exportCsv(int $batchId): void
    {
        $batch  = LicenseBatch::findOrFail($batchId);
        $export = app(BatchExportService::class)->exportCsv($batch, auth()->id());
        session()->flash('success', "CSV export ready: {$export->filename} ({$export->record_count} records).");
    }

    public function revokeBatch(int $batchId): void
    {
        $batch = LicenseBatch::findOrFail($batchId);
        $batch->update(['status' => 'revoked']);
        $batch->licenses()->where('status', '!=', 'revoked')->update(['status' => 'revoked']);
        session()->flash('success', "Batch \"{$batch->label}\" revoked.");
    }

    #[Computed]
    public function batches()
    {
        return LicenseBatch::query()
            ->with(['product', 'createdBy'])
            ->when($this->search, fn ($q) => $q->where('label', 'like', "%{$this->search}%")
                ->orWhere('batch_code', 'like', "%{$this->search}%"))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function products()
    {
        return Product::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function detailBatch(): ?LicenseBatch
    {
        return $this->detailId ? LicenseBatch::with(['product', 'licenses', 'exports'])->find($this->detailId) : null;
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_batches'    => LicenseBatch::count(),
            'total_generated'  => LicenseBatch::sum('total_generated'),
            'total_used'       => LicenseBatch::sum('total_used'),
            'active_batches'   => LicenseBatch::where('status', 'active')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>License Batches</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total Batches',   'value' => $this->stats['total_batches'],   'color' => 'cyan'],
                ['label' => 'Keys Generated',  'value' => $this->stats['total_generated'], 'color' => 'violet'],
                ['label' => 'Keys Assigned',   'value' => $this->stats['total_used'],      'color' => 'green'],
                ['label' => 'Active Batches',  'value' => $this->stats['active_batches'],  'color' => 'blue'],
            ] as $stat)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <input type="text"
                       wire:model.live="search"
                       placeholder="Search batch label or code…"
                       class="w-full sm:w-64 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <select wire:model.live="filterStatus"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="revoked">Revoked</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <button wire:click="openGenerate"
                    class="flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Generate Batch
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type / Edition</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Keys</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Expires</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->batches as $batch)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <p class="text-sm font-semibold text-slate-900">{{ $batch->label }}</p>
                                    <p class="text-xs font-mono text-slate-400">{{ $batch->batch_code }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-600">{{ $batch->product?->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-sm text-slate-700 capitalize">{{ $batch->license_type }}</span>
                                    <span class="text-xs text-slate-400 block capitalize">{{ $batch->edition }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-700">
                                    {{ number_format($batch->total_generated) }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full {{ $batch->usage_percent > 80 ? 'bg-amber-500' : 'bg-cyan-500' }} transition-all"
                                                 style="width: {{ $batch->usage_percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 w-10 text-right">{{ $batch->usage_percent }}%</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $batch->total_used }} / {{ $batch->total_generated }} used</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-600">
                                    {{ $batch->expires_at ? $batch->expires_at->format('Y-m-d') : '∞ Lifetime' }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ match($batch->status) {
                                            'active'   => 'bg-green-50 text-green-700',
                                            'expired'  => 'bg-red-50 text-red-700',
                                            'revoked'  => 'bg-slate-100 text-slate-500',
                                            'archived' => 'bg-amber-50 text-amber-700',
                                            default    => 'bg-slate-100 text-slate-500',
                                        } }}">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button wire:click="openDetail({{ $batch->id }})"
                                                class="text-sm font-medium text-cyan-600 hover:text-cyan-700">View</button>
                                        <button wire:click="exportCsv({{ $batch->id }})"
                                                class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Export CSV</button>
                                        @if($batch->status !== 'revoked')
                                            <button wire:click="revokeBatch({{ $batch->id }})"
                                                    wire:confirm="Revoke ALL licenses in this batch?"
                                                    class="text-sm font-medium text-red-600 hover:text-red-700">Revoke</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-400">
                                    No batches yet.
                                    <button wire:click="openGenerate" class="text-cyan-600 hover:underline">Generate your first batch</button>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $this->batches->links() }}
        </div>

    </div>

    {{-- Generate Batch Modal --}}
    <div
        x-data
        x-show="$wire.showModal"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        style="display: none; position: fixed; inset: 0; z-index: 200; overflow-y: auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(2px);"
                 x-on:click="$wire.set('showModal', false)"></div>

            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Generate License Batch</h2>
                    <button wire:click="$set('showModal', false)"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="generate" class="space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Batch Label <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="label" placeholder="e.g. Q2 2025 Reseller Pack"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Product <span class="text-red-500">*</span></label>
                            <select wire:model="product_id"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="0">Select product…</option>
                                @foreach($this->products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="quantity" min="1" max="10000"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">License Type <span class="text-red-500">*</span></label>
                            <select wire:model.live="license_type"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="lifetime">Lifetime</option>
                                <option value="monthly">Monthly</option>
                                <option value="annual">Annual</option>
                                <option value="yearly">Yearly</option>
                                <option value="trial">Trial</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Edition <span class="text-red-500">*</span></label>
                            <select wire:model="edition"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="standard">Standard</option>
                                <option value="professional">Professional</option>
                                <option value="enterprise">Enterprise</option>
                                <option value="trial">Trial</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Max Activations</label>
                            <input type="number" wire:model="max_activations" min="1" max="9999"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Key Prefix</label>
                            <input type="text" wire:model="key_prefix" maxlength="8" placeholder="e.g. EXCL"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono uppercase focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <p class="mt-1 text-xs text-slate-400">Result: PREFIX-XXXX-XXXX-XXXX</p>
                        </div>

                        @if($license_type !== 'lifetime')
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Expires At</label>
                                <input type="date" wire:model="expires_at"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <p class="mt-1 text-xs text-slate-400">Or set duration days below</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Duration (days)</label>
                                <input type="number" wire:model="duration_days" min="0"
                                       placeholder="e.g. 365"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Reseller Tag</label>
                            <input type="text" wire:model="reseller_tag" placeholder="Optional reseller code"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                            <textarea wire:model="notes" rows="2"
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                      placeholder="Internal notes for this batch…"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            <span wire:loading.remove wire:target="generate">Generate Batch</span>
                            <span wire:loading wire:target="generate">Generating…</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Detail Side Panel --}}
    <div
        x-data
        x-show="$wire.showDetail"
        x-on:keydown.escape.window="$wire.set('showDetail', false)"
        style="display: none; position: fixed; inset: 0; z-index: 200; overflow-y: auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(2px);"
                 x-on:click="$wire.set('showDetail', false)"></div>

            @if($this->detailBatch)
                <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $this->detailBatch->label }}</h2>
                            <p class="text-xs font-mono text-slate-400">{{ $this->detailBatch->batch_code }}</p>
                        </div>
                        <button wire:click="$set('showDetail', false)"
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="rounded-xl bg-cyan-50 p-4 text-center">
                            <p class="text-2xl font-bold text-cyan-700">{{ number_format($this->detailBatch->total_generated) }}</p>
                            <p class="text-xs text-cyan-500 mt-1">Generated</p>
                        </div>
                        <div class="rounded-xl bg-green-50 p-4 text-center">
                            <p class="text-2xl font-bold text-green-700">{{ number_format($this->detailBatch->total_used) }}</p>
                            <p class="text-xs text-green-500 mt-1">Assigned</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl font-bold text-slate-700">{{ number_format($this->detailBatch->total_generated - $this->detailBatch->total_used) }}</p>
                            <p class="text-xs text-slate-500 mt-1">Unused</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-slate-500">Product</dt><dd class="font-medium text-slate-900">{{ $this->detailBatch->product?->name }}</dd></div>
                        <div><dt class="text-slate-500">License Type</dt><dd class="font-medium text-slate-900 capitalize">{{ $this->detailBatch->license_type }}</dd></div>
                        <div><dt class="text-slate-500">Edition</dt><dd class="font-medium text-slate-900 capitalize">{{ $this->detailBatch->edition }}</dd></div>
                        <div><dt class="text-slate-500">Max Activations</dt><dd class="font-medium text-slate-900">{{ $this->detailBatch->max_activations }}</dd></div>
                        <div><dt class="text-slate-500">Key Prefix</dt><dd class="font-mono font-medium text-slate-900">{{ $this->detailBatch->key_prefix }}</dd></div>
                        <div><dt class="text-slate-500">Expires At</dt><dd class="font-medium text-slate-900">{{ $this->detailBatch->expires_at?->format('Y-m-d') ?? '∞ Lifetime' }}</dd></div>
                        <div><dt class="text-slate-500">Reseller Tag</dt><dd class="font-medium text-slate-900">{{ $this->detailBatch->reseller_tag ?? '—' }}</dd></div>
                        <div><dt class="text-slate-500">Created By</dt><dd class="font-medium text-slate-900">{{ $this->detailBatch->createdBy?->name ?? '—' }}</dd></div>
                    </dl>

                    @if($this->detailBatch->notes)
                        <div class="mt-4 rounded-lg bg-slate-50 p-3">
                            <p class="text-xs font-medium text-slate-500 mb-1">Notes</p>
                            <p class="text-sm text-slate-700">{{ $this->detailBatch->notes }}</p>
                        </div>
                    @endif

                    @if($this->detailBatch->exports->isNotEmpty())
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Export History</p>
                            <div class="space-y-2">
                                @foreach($this->detailBatch->exports as $export)
                                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                                        <span class="font-mono text-slate-700 truncate">{{ $export->filename }}</span>
                                        <span class="text-slate-400 ml-2">{{ $export->record_count }} records · {{ $export->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="exportCsv({{ $this->detailBatch->id }})"
                                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                            Export CSV
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
