<?php

use App\Models\Product;
use App\Models\ProductPlan;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new #[Layout('layouts.admin')] #[Title('Products — ExchoSoft')] class extends Component {
    use WithPagination, WithFileUploads;

    // ── Filter / List ─────────────────────────────────────────────────────────
    public string $search         = '';
    public string $filterPlatform = '';
    public string $filterCategory = '';
    public string $filterStatus   = '';

    // ── UI State ──────────────────────────────────────────────────────────────
    public bool    $showForm  = false;
    public bool    $editMode  = false;
    public ?string $editId    = null;
    public int     $activeTab = 0; // 0=Info, 1=Plans, 2=Shop

    // ── Core Fields ───────────────────────────────────────────────────────────
    public string  $name            = '';
    public string  $slug            = '';
    public string  $product_code    = '';
    public string  $platform        = '';
    public string  $current_version = '';
    public string  $description     = '';
    public         $logo            = null;
    public ?string $existing_logo   = null;

    // ── Licensing (minimal) ───────────────────────────────────────────────────
    public string $app_identifier = '';
    public string $secret_key     = '';
    public string $support_email  = '';
    public string $webhook_url    = '';

    // ── Inline Plans ─────────────────────────────────────────────────────────
    // Each plan: [name, price, sale_price, currency, duration_days, duration_type, is_active, _id (edit)]
    public array $plans = [];

    // Preset plan options — name => duration_days (0 = lifetime)
    public array $planPresets = [
        'Monthly'   => 30,
        'Quarterly' => 90,
        'Yearly'    => 365,
        'Lifetime'  => 0,
        'Custom'    => null,
    ];

    // ── Shop Fields ───────────────────────────────────────────────────────────
    public string  $tagline              = '';
    public string  $full_description     = '';
    public string  $category             = '';
    public string  $product_type         = '';
    public string  $currency             = 'USD';
    public         $cover_image          = null;
    public ?string $existing_cover_image = null;
    public array   $gallery_files        = [];
    public array   $existing_gallery     = [];
    public array   $features             = [];
    public array   $tech_stack           = [];
    public array   $metadata             = [];
    public string  $demo_url             = '';
    public string  $documentation_url    = '';
    public string  $download_url         = '';
    public int     $sort_order           = 0;
    public bool    $is_active            = true;
    public bool    $is_published         = false;
    public bool    $is_featured          = false;

    // ─────────────────────────────────────────────────────────────────────────

    public function updatedName(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    // ── Plan helpers ──────────────────────────────────────────────────────────

    public function addPlan(): void
    {
        $this->plans[] = [
            'name'          => 'Monthly',
            'price'         => '0.00',
            'sale_price'    => '',
            'currency'      => $this->currency ?: 'USD',
            'duration_days' => 30,
            'duration_type' => 'Monthly',
            'is_active'     => true,
            '_id'           => null, // null = new, uuid string = existing
        ];
    }

    public function removePlan(int $i): void
    {
        array_splice($this->plans, $i, 1);
    }

    public function updatedPlans($value, $key): void
    {
        // When duration_type changes, set duration_days automatically
        if (str_ends_with($key, '.duration_type')) {
            $idx = (int) explode('.', $key)[0];
            $type = $this->plans[$idx]['duration_type'] ?? 'Monthly';
            $days = match ($type) {
                'Monthly'   => 30,
                'Quarterly' => 90,
                'Yearly'    => 365,
                'Lifetime'  => 0,
                default     => $this->plans[$idx]['duration_days'] ?? 30,
            };
            if ($type !== 'Custom') {
                $this->plans[$idx]['duration_days'] = $days;
                // Auto-set name if it's a known preset
                $this->plans[$idx]['name'] = $type;
            }
        }
    }

    // ── Open / Close ─────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm  = true;
        $this->editMode  = false;
        $this->activeTab = 0;
    }

    public function openEdit(string $id): void
    {
        $product = Product::with('plans')->findOrFail($id);
        $this->editId           = $id;
        $this->name             = $product->name;
        $this->slug             = $product->slug;
        $this->product_code     = $product->product_code ?? '';
        $this->platform         = $product->platform ?? '';
        $this->current_version  = $product->current_version ?? '';
        $this->description      = $product->description ?? '';
        $this->logo             = null;
        $this->existing_logo    = $product->logo ?? null;

        $this->app_identifier   = $product->app_identifier ?? '';
        $this->secret_key       = $product->secret_key ?? '';
        $this->support_email    = $product->support_email ?? '';
        $this->webhook_url      = $product->webhook_url ?? '';

        // Load existing plans into inline editor
        $this->plans = $product->plans->map(function ($plan) {
            $type = match (true) {
                $plan->duration_days === 0                                            => 'Lifetime',
                $plan->duration_days <= 31                                            => 'Monthly',
                $plan->duration_days <= 93                                            => 'Quarterly',
                $plan->duration_days >= 365 && $plan->duration_days <= 366            => 'Yearly',
                default                                                               => 'Custom',
            };
            return [
                'name'          => $plan->name,
                'price'         => (string) $plan->price,
                'sale_price'    => (string) ($plan->sale_price ?? ''),
                'currency'      => $plan->currency,
                'duration_days' => $plan->duration_days,
                'duration_type' => $type,
                'is_active'     => $plan->is_active,
                '_id'           => $plan->id,
            ];
        })->values()->toArray();

        $this->tagline              = $product->tagline ?? '';
        $this->full_description     = $product->full_description ?? '';
        $this->category             = $product->category ?? '';
        $this->product_type         = $product->product_type ?? '';
        $this->currency             = $product->currency ?? 'USD';
        $this->cover_image          = null;
        $this->existing_cover_image = $product->cover_image ?? null;
        $this->gallery_files        = [];
        $this->existing_gallery     = $product->gallery ?? [];
        $this->features             = array_values($product->features ?? []);
        $this->tech_stack           = array_values($product->tech_stack ?? []);
        $rawMeta                    = $product->metadata ?? [];
        $this->metadata             = collect($rawMeta)->map(fn($v, $k) => ['key' => $k, 'value' => $v])->values()->toArray();
        if (empty($this->metadata)) {
            $this->metadata = [['key' => '', 'value' => '']];
        }
        $this->demo_url           = $product->demo_url ?? '';
        $this->documentation_url  = $product->documentation_url ?? '';
        $this->download_url       = $product->download_url ?? '';
        $this->sort_order         = $product->sort_order ?? 0;
        $this->is_active          = $product->is_active;
        $this->is_published       = $product->is_published;
        $this->is_featured        = $product->is_featured;

        $this->showForm  = true;
        $this->editMode  = true;
        $this->activeTab = 0;
    }

    // ── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'name'                  => 'required|string|max:100',
            'slug'                  => 'required|string|max:100|unique:products,slug' . ($this->editMode ? ',' . $this->editId . ',id' : ''),
            'product_code'          => 'nullable|string|max:50',
            'platform'              => 'nullable|string|max:50',
            'current_version'       => 'nullable|string|max:50',
            'description'           => 'nullable|string|max:500',
            'logo'                  => 'nullable|image|max:2048',
            'app_identifier'        => 'nullable|string|max:100',
            'support_email'         => 'nullable|email|max:100',
            'tagline'               => 'nullable|string|max:200',
            'full_description'      => 'nullable|string',
            'category'              => 'nullable|string|max:50',
            'product_type'          => 'nullable|string|max:50',
            'currency'              => 'nullable|string|max:3',
            'cover_image'           => 'nullable|image|max:4096',
            'gallery_files.*'       => 'nullable|image|max:4096',
            'features.*'            => 'nullable|string|max:200',
            'tech_stack.*'          => 'nullable|string|max:100',
            'metadata.*.key'        => 'nullable|string|max:100',
            'metadata.*.value'      => 'nullable|string|max:500',
            'demo_url'              => 'nullable|url|max:255',
            'documentation_url'     => 'nullable|url|max:255',
            'download_url'          => 'nullable|url|max:255',
            'sort_order'            => 'nullable|integer|min:0',
            'plans.*.name'          => 'required|string|max:100',
            'plans.*.price'         => 'required|numeric|min:0',
            'plans.*.sale_price'    => 'nullable|numeric|min:0',
            'plans.*.currency'      => 'required|string|max:3',
            'plans.*.duration_days' => 'required|integer|min:0',
        ]);

        $data = [
            'name'              => $this->name,
            'slug'              => $this->slug,
            'product_code'      => $this->product_code ?: null,
            'platform'          => $this->platform ?: null,
            'current_version'   => $this->current_version ?: null,
            'description'       => $this->description ?: null,
            'logo'              => $this->logo
                                    ? $this->logo->store('products/logos', 'public')
                                    : ($this->existing_logo ?: null),
            'app_identifier'    => $this->app_identifier ?: null,
            'support_email'     => $this->support_email ?: null,
            'webhook_url'       => $this->webhook_url ?: null,
            'tagline'           => $this->tagline ?: null,
            'full_description'  => $this->full_description ?: null,
            'category'          => $this->category ?: null,
            'product_type'      => $this->product_type ?: null,
            'currency'          => $this->currency ?: 'USD',
            'cover_image'       => $this->cover_image
                                    ? $this->cover_image->store('products/covers', 'public')
                                    : ($this->existing_cover_image ?: null),
            'gallery'           => array_merge(
                                    $this->existing_gallery,
                                    collect($this->gallery_files)->map(fn($f) => $f->store('products/gallery', 'public'))->toArray()
                                   ),
            'features'          => array_values(array_filter(array_map('trim', $this->features))),
            'tech_stack'        => array_values(array_filter(array_map('trim', $this->tech_stack))),
            'demo_url'          => $this->demo_url ?: null,
            'documentation_url' => $this->documentation_url ?: null,
            'download_url'      => $this->download_url ?: null,
            'sort_order'        => $this->sort_order,
            'is_active'         => $this->is_active,
            'is_published'      => $this->is_published,
            'is_featured'       => $this->is_featured,
            'metadata'          => collect($this->metadata)
                                    ->filter(fn($r) => !empty($r['key']))
                                    ->mapWithKeys(fn($r) => [$r['key'] => $r['value']])
                                    ->toArray(),
        ];

        if ($this->editMode) {
            $product = Product::findOrFail($this->editId);
            $product->update($data);
        } else {
            $product = Product::create($data);
        }

        // ── Sync inline plans ─────────────────────────────────────────────────
        $keptIds = [];
        foreach ($this->plans as $i => $row) {
            $planData = [
                'product_id'    => $product->id,
                'name'          => trim($row['name']),
                'slug'          => str($row['name'])->slug()->toString(),
                'price'         => $row['price'],
                'sale_price'    => ($row['sale_price'] !== '' && $row['sale_price'] !== null) ? $row['sale_price'] : null,
                'currency'      => $row['currency'] ?: 'USD',
                'duration_days' => (int) $row['duration_days'],
                'is_active'     => (bool) ($row['is_active'] ?? true),
                'sort_order'    => $i,
            ];

            if (!empty($row['_id'])) {
                // Existing plan — update
                $plan = ProductPlan::find($row['_id']);
                if ($plan && $plan->product_id === $product->id) {
                    // Ensure slug unique per product (excluding self)
                    $slugExists = ProductPlan::where('product_id', $product->id)
                        ->where('slug', $planData['slug'])
                        ->where('id', '!=', $plan->id)
                        ->exists();
                    if ($slugExists) {
                        $planData['slug'] = $planData['slug'] . '-' . ($i + 1);
                    }
                    $plan->update($planData);
                    $keptIds[] = $plan->id;
                }
            } else {
                // New plan — ensure unique slug
                $base = $planData['slug'];
                $c    = 1;
                while (ProductPlan::where('product_id', $product->id)->where('slug', $planData['slug'])->exists()) {
                    $planData['slug'] = $base . '-' . $c++;
                }
                $plan      = ProductPlan::create($planData);
                $keptIds[] = $plan->id;
            }
        }

        // Delete plans removed from the inline editor
        if ($this->editMode) {
            ProductPlan::where('product_id', $product->id)
                ->whereNotIn('id', $keptIds)
                ->delete();
        }

        session()->flash('success', $this->editMode ? 'Product updated.' : 'Product created.');
        $this->showForm = false;
        $this->resetForm();
    }

    // ── CRUD helpers ──────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function toggleStatus(string $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
    }

    public function togglePublished(string $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_published' => !$product->is_published]);
    }

    // ── Feature / Tech / Meta helpers ─────────────────────────────────────────

    public function addFeature(): void    { $this->features[] = ''; }
    public function removeFeature(int $i): void { array_splice($this->features, $i, 1); }

    public function addTechStack(): void  { $this->tech_stack[] = ''; }
    public function removeTechStack(int $i): void { array_splice($this->tech_stack, $i, 1); }

    public function addMetadata(): void   { $this->metadata[] = ['key' => '', 'value' => '']; }
    public function removeMetadata(int $i): void { array_splice($this->metadata, $i, 1); }

    public function removeExistingGalleryImage(int $index): void
    {
        array_splice($this->existing_gallery, $index, 1);
    }

    // ── Reset ─────────────────────────────────────────────────────────────────

    public function resetForm(): void
    {
        $this->name             = '';
        $this->slug             = '';
        $this->product_code     = '';
        $this->platform         = '';
        $this->current_version  = '';
        $this->description      = '';
        $this->logo             = null;
        $this->existing_logo    = null;
        $this->app_identifier   = '';
        $this->secret_key       = '';
        $this->support_email    = '';
        $this->webhook_url      = '';
        $this->plans            = [];
        $this->tagline          = '';
        $this->full_description = '';
        $this->category         = '';
        $this->product_type     = '';
        $this->currency         = 'USD';
        $this->cover_image      = null;
        $this->existing_cover_image = null;
        $this->gallery_files    = [];
        $this->existing_gallery = [];
        $this->features         = [];
        $this->tech_stack       = [];
        $this->metadata         = [['key' => '', 'value' => '']];
        $this->demo_url         = '';
        $this->documentation_url = '';
        $this->download_url     = '';
        $this->sort_order       = 0;
        $this->is_active        = true;
        $this->is_published     = false;
        $this->is_featured      = false;
        $this->editId           = null;
        $this->activeTab        = 0;
        $this->resetValidation();
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function products()
    {
        return Product::with('plans')
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('product_code', 'like', '%' . $this->search . '%'))
            ->when($this->filterPlatform, fn($q) => $q->where('platform', $this->filterPlatform))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterStatus, fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <x-slot:heading>Products</x-slot:heading>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header / Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products…"
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterPlatform" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Platforms</option>
                    <option value="web">Web</option>
                    <option value="desktop">Desktop</option>
                    <option value="mobile">Mobile</option>
                </select>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="erp">ERP</option>
                    <option value="hrms">HRMS</option>
                    <option value="software">Software</option>
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
                New Product
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Platform</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Plans</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Published</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($product->logo)
                                            <img src="{{ Storage::url($product->logo) }}" alt="" class="h-8 w-8 rounded-lg object-cover border border-slate-200 flex-shrink-0">
                                        @else
                                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-cyan-100 to-cyan-200 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-cyan-600">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $product->product_code ?? $product->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $product->platform ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $product->category ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    @php $planCount = $product->plans->count(); @endphp
                                    @if($planCount > 0)
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center rounded-full bg-purple-100 text-purple-700 px-2.5 py-0.5 text-xs font-semibold">
                                                {{ $planCount }} {{ Str::plural('plan', $planCount) }}
                                            </span>
                                            @php $startPrice = $product->starting_price; @endphp
                                            @if($startPrice !== null)
                                                <span class="text-xs text-slate-500">from {{ $product->currency }} {{ number_format($startPrice, 2) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No plans</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <button wire:click="togglePublished('{{ $product->id }}')"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $product->is_published ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $product->is_published ? 'Published' : 'Draft' }}
                                    </button>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <button wire:click="toggleStatus('{{ $product->id }}')"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $product->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openEdit('{{ $product->id }}')"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete('{{ $product->id }}')"
                                            wire:confirm="Delete this product and all its plans?"
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
                                <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No products yet. Create your first one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->products->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $this->products->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ────────── Slide-over Form ────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showForm', false)"></div>
            <div class="relative ml-auto w-full max-w-3xl bg-white shadow-2xl flex flex-col h-full">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 flex-shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            {{ $editMode ? 'Edit Product' : 'New Product' }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Fill in product details and add pricing plans below</p>
                    </div>
                    <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Tab Nav --}}
                <div class="flex border-b border-slate-100 px-6 gap-0 flex-shrink-0 bg-slate-50/50">
                    @foreach(['Product Info', 'Pricing Plans', 'Shop & Media'] as $ti => $tabLabel)
                        <button type="button" wire:click="$set('activeTab', {{ $ti }})"
                            class="px-4 py-3 text-sm font-medium border-b-2 transition-colors -mb-px
                                   {{ $activeTab === $ti ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                            {{ $tabLabel }}
                            @if($ti === 1 && count($plans) > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full bg-cyan-100 text-cyan-700 text-[10px] font-bold">{{ count($plans) }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <form wire:submit="save" class="flex-1 overflow-y-auto">
                    <div class="px-6 py-5 space-y-5">

                        {{-- ══════════ TAB 0: Product Info ══════════ --}}
                        <div class="{{ $activeTab === 0 ? 'block' : 'hidden' }} space-y-4">

                            {{-- Name + Slug --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input wire:model.live="name" type="text" placeholder="My Awesome Software"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slug <span class="text-red-500">*</span></label>
                                    <input wire:model="slug" type="text" placeholder="my-awesome-software"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-xs">
                                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Code / Platform / Version --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Code</label>
                                    <input wire:model="product_code" type="text" placeholder="PRD-001"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Platform</label>
                                    <select wire:model="platform" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                        <option value="">Select…</option>
                                        <option value="web">Web</option>
                                        <option value="desktop">Desktop</option>
                                        <option value="mobile">Mobile</option>
                                        <option value="api">API</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Version</label>
                                    <input wire:model="current_version" type="text" placeholder="1.0.0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Short Description</label>
                                <textarea wire:model="description" rows="2" placeholder="Brief one-line description of the product…"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                            </div>

                            {{-- Logo --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Logo</label>
                                <div class="flex items-center gap-3">
                                    @if ($existing_logo)
                                        <img src="{{ Storage::url($existing_logo) }}" alt="Logo" class="h-12 w-12 rounded-xl object-cover border border-slate-200">
                                        <button type="button" wire:click="$set('existing_logo', null)" class="text-xs text-red-500 hover:underline">Remove</button>
                                    @elseif ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="h-12 w-12 rounded-xl object-cover border border-slate-200">
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center border border-dashed border-slate-300">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <input wire:model="logo" type="file" accept="image/*"
                                        class="text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                </div>
                                @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Licensing section --}}
                            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">License Settings</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">App Identifier</label>
                                        <input wire:model="app_identifier" type="text" placeholder="com.company.product"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-xs">
                                        <p class="mt-1 text-xs text-slate-400">Leave blank if not licensable</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Support Email</label>
                                        <input wire:model="support_email" type="email" placeholder="support@company.com"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Webhook URL</label>
                                    <input wire:model="webhook_url" type="url" placeholder="https://…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                @if($editMode && $secret_key)
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Secret Key <span class="font-normal text-slate-400">(read-only)</span></label>
                                        <input type="text" value="{{ $secret_key }}" readonly
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white font-mono text-slate-500">
                                    </div>
                                @endif
                            </div>

                            {{-- Status flags --}}
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_active" type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-sm text-slate-700">Active</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_published" type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-sm text-slate-700">Published</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_featured" type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-sm text-slate-700">Featured</span>
                                </label>
                            </div>
                        </div>

                        {{-- ══════════ TAB 1: Pricing Plans ══════════ --}}
                        <div class="{{ $activeTab === 1 ? 'block' : 'hidden' }} space-y-4">

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Pricing Plans</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Add one or more plans for this product. Each plan defines a price and billing period.</p>
                                </div>
                                <button type="button" wire:click="addPlan"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-700 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Plan
                                </button>
                            </div>

                            @if(count($plans) === 0)
                                <div class="rounded-2xl border-2 border-dashed border-slate-200 py-10 text-center">
                                    <div class="mx-auto h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">No plans yet</p>
                                    <p class="text-xs text-slate-400 mt-1">Click "Add Plan" to create your first pricing plan</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($plans as $i => $plan)
                                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                            {{-- Plan header --}}
                                            <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-100">
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $badgeColor = match($plan['duration_type'] ?? 'Monthly') {
                                                            'Lifetime'  => 'purple',
                                                            'Monthly'   => 'blue',
                                                            'Quarterly' => 'indigo',
                                                            'Yearly'    => 'teal',
                                                            default     => 'slate',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700">
                                                        {{ $plan['duration_type'] ?? 'Monthly' }}
                                                    </span>
                                                    <span class="text-sm font-semibold text-slate-800">{{ $plan['name'] ?: 'Untitled Plan' }}</span>
                                                    @if(!empty($plan['price']))
                                                        <span class="text-xs text-slate-500">· {{ $plan['currency'] ?? 'USD' }} {{ number_format((float)$plan['price'], 2) }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" wire:model="plans.{{ $i }}.is_active" class="rounded border-slate-300 text-cyan-600 h-3.5 w-3.5">
                                                        <span class="text-xs text-slate-500">Active</span>
                                                    </label>
                                                    <button type="button" wire:click="removePlan({{ $i }})"
                                                        class="rounded-lg p-1 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Plan body --}}
                                            <div class="p-4 grid grid-cols-2 gap-3">
                                                {{-- Billing period selector --}}
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Billing Period</label>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach(['Monthly' => '30d', 'Quarterly' => '90d', 'Yearly' => '365d', 'Lifetime' => '∞', 'Custom' => '?d'] as $preset => $hint)
                                                            <label class="flex items-center gap-1.5 rounded-lg border-2 cursor-pointer px-3 py-1.5 transition-colors
                                                                {{ ($plan['duration_type'] ?? 'Monthly') === $preset ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                                                <input type="radio" wire:model.live="plans.{{ $i }}.duration_type" value="{{ $preset }}" class="sr-only">
                                                                <span class="text-xs font-semibold {{ ($plan['duration_type'] ?? 'Monthly') === $preset ? 'text-cyan-700' : 'text-slate-600' }}">{{ $preset }}</span>
                                                                <span class="text-xs text-slate-400">{{ $hint }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @if(($plan['duration_type'] ?? '') === 'Custom')
                                                        <div class="mt-2">
                                                            <label class="block text-xs text-slate-500 mb-1">Duration in days</label>
                                                            <input wire:model="plans.{{ $i }}.duration_days" type="number" min="1" placeholder="e.g. 180"
                                                                class="w-32 rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:border-cyan-400">
                                                        </div>
                                                    @endif
                                                    @error("plans.{$i}.duration_days") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                {{-- Plan name --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Plan Name <span class="text-red-500">*</span></label>
                                                    <input wire:model="plans.{{ $i }}.name" type="text" placeholder="e.g. Monthly Pro"
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                                    @error("plans.{$i}.name") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                {{-- Currency --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                                                    <input wire:model="plans.{{ $i }}.currency" type="text" maxlength="3" placeholder="USD"
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono uppercase">
                                                </div>

                                                {{-- Price --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Price <span class="text-red-500">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                                                        <input wire:model="plans.{{ $i }}.price" type="number" step="0.01" min="0" placeholder="0.00"
                                                            class="w-full rounded-xl border border-slate-200 pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                                    </div>
                                                    @error("plans.{$i}.price") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                {{-- Sale Price --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sale Price <span class="text-slate-400 font-normal">(optional)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                                                        <input wire:model="plans.{{ $i }}.sale_price" type="number" step="0.01" min="0" placeholder="Leave blank"
                                                            class="w-full rounded-xl border border-slate-200 pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Quick-add preset buttons --}}
                            @if(count($plans) === 0)
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <p class="w-full text-xs text-slate-400">Quick add:</p>
                                    @foreach(['Monthly', 'Yearly', 'Lifetime'] as $preset)
                                        <button type="button" wire:click="$dispatch('add-preset-plan', { type: '{{ $preset }}' })"
                                            x-on:add-preset-plan.window="
                                                if ($event.detail.type === '{{ $preset }}') {
                                                    $wire.plans.push({
                                                        name: '{{ $preset }}',
                                                        price: '0.00',
                                                        sale_price: '',
                                                        currency: $wire.currency || 'USD',
                                                        duration_days: {{ match($preset) { 'Monthly' => 30, 'Yearly' => 365, default => 0 } }},
                                                        duration_type: '{{ $preset === 'Yearly' ? 'Yearly' : $preset }}',
                                                        is_active: true,
                                                        _id: null
                                                    });
                                                }
                                            "
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-cyan-400 hover:text-cyan-600 transition-colors">
                                            + {{ $preset }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- ══════════ TAB 2: Shop & Media ══════════ --}}
                        <div class="{{ $activeTab === 2 ? 'block' : 'hidden' }} space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tagline</label>
                                <input wire:model="tagline" type="text" placeholder="One-line marketing tagline"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Description</label>
                                <livewire:markdown-editor wire:model="full_description"
                                    placeholder="Detailed product description…" :rows="6"
                                    :show-toolbar="true" :show-upload="true" />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                                    <input wire:model="category" type="text" placeholder="erp, hrms, software…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Type</label>
                                    <input wire:model="product_type" type="text" placeholder="digital, physical…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>

                            {{-- Cover Image --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Cover Image</label>
                                @if ($existing_cover_image)
                                    <div class="mb-2 flex items-center gap-2">
                                        <img src="{{ Storage::url($existing_cover_image) }}" alt="Cover" class="h-16 w-24 rounded-lg object-cover border border-slate-200">
                                        <button type="button" wire:click="$set('existing_cover_image', null)" class="text-xs text-red-500 hover:underline">Remove</button>
                                    </div>
                                @elseif ($cover_image)
                                    <div class="mb-2">
                                        <img src="{{ $cover_image->temporaryUrl() }}" alt="Preview" class="h-16 w-24 rounded-lg object-cover border border-slate-200">
                                    </div>
                                @endif
                                <input wire:model="cover_image" type="file" accept="image/*"
                                    class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                @error('cover_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Gallery --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Gallery Images</label>
                                @if (count($existing_gallery))
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($existing_gallery as $gi => $url)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($url) }}" class="h-14 w-14 rounded-lg object-cover border border-slate-200">
                                                <button type="button" wire:click="removeExistingGalleryImage({{ $gi }})"
                                                    class="absolute -top-1 -right-1 hidden group-hover:flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white text-xs">✕</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <input wire:model="gallery_files" type="file" accept="image/*" multiple
                                    class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                            </div>

                            {{-- Features --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-slate-600">Features</label>
                                    <button type="button" wire:click="addFeature" class="text-xs text-cyan-600 hover:underline">+ Add</button>
                                </div>
                                <div class="space-y-1.5">
                                    @foreach ($features as $fi => $feature)
                                        <div class="flex gap-2">
                                            <input wire:model="features.{{ $fi }}" type="text" placeholder="Feature {{ $fi + 1 }}"
                                                class="flex-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:border-cyan-400">
                                            <button type="button" wire:click="removeFeature({{ $fi }})" class="text-slate-300 hover:text-red-500 px-1">✕</button>
                                        </div>
                                    @endforeach
                                    @if(count($features) === 0)
                                        <p class="text-xs text-slate-400 italic">No features added yet.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Tech Stack --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-slate-600">Tech Stack</label>
                                    <button type="button" wire:click="addTechStack" class="text-xs text-cyan-600 hover:underline">+ Add</button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($tech_stack as $ti => $tech)
                                        <div class="flex items-center gap-1 rounded-lg bg-slate-100 pl-2 pr-1 py-1">
                                            <input wire:model="tech_stack.{{ $ti }}" type="text" placeholder="Laravel"
                                                class="bg-transparent text-xs text-slate-700 w-20 focus:outline-none">
                                            <button type="button" wire:click="removeTechStack({{ $ti }})" class="text-slate-400 hover:text-red-500 text-xs">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- URLs --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Demo URL</label>
                                    <input wire:model="demo_url" type="url" placeholder="https://…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Docs URL</label>
                                    <input wire:model="documentation_url" type="url" placeholder="https://…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Download URL</label>
                                    <input wire:model="download_url" type="url" placeholder="https://…"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>

                            {{-- Sort Order --}}
                            <div class="w-32">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Sort Order</label>
                                <input wire:model="sort_order" type="number" min="0"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>

                    </div>

                    {{-- Sticky footer --}}
                    <div class="sticky bottom-0 bg-white border-t border-slate-100 px-6 py-4 flex items-center gap-3">
                        {{-- Tab nav arrows --}}
                        @if($activeTab > 0)
                            <button type="button" wire:click="$set('activeTab', {{ $activeTab - 1 }})"
                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                ← Back
                            </button>
                        @endif
                        @if($activeTab < 2)
                            <button type="button" wire:click="$set('activeTab', {{ $activeTab + 1 }})"
                                class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-medium text-cyan-700 hover:bg-cyan-100 transition-colors">
                                Next →
                            </button>
                        @endif
                        <div class="flex-1"></div>
                        <button type="button" wire:click="$set('showForm', false)"
                            class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                            {{ $editMode ? 'Update Product' : 'Create Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
