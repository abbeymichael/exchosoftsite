<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new #[Layout('layouts.admin')] #[Title('Products — ExchoSoft')] class extends Component {
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterPlatform = '';
    public string $filterCategory = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?string $editId = null;

    // Core Fields
    public string $name = '';
    public string $slug = '';
    public string $product_code = '';
    public string $platform = '';
    public string $current_version = '';
    public string $pricing_type = 'freemium';
    public string $description = '';
    public $logo = null; // TemporaryUploadedFile or existing path string
    public ?string $existing_logo = null;

    // Licensing Fields
    public string $app_identifier = '';
    public string $secret_key = '';
    public string $support_email = '';
    public string $webhook_url = '';
    public int $max_devices = 0;
    public int $default_duration_days = 365;
    public string $min_app_version = '';
    public string $max_app_version = '';
    public int $offline_ttl_hours = 24;
    public int $grace_period_days = 7;

    // Shop Fields
    public string $tagline = '';
    public string $full_description = '';
    public string $category = '';
    public string $product_type = '';
    public string $price = '0.00';
    public string $sale_price = '';
    public string $currency = 'USD';
    public $cover_image = null; // TemporaryUploadedFile or existing path string
    public ?string $existing_cover_image = null;
    public array $gallery_files = []; // TemporaryUploadedFile[]
    public array $existing_gallery = []; // already-saved URLs
    public string $gallery_text = ''; // kept for backward-compat / manual entry fallback
    public array $features = []; // ['Feature 1', 'Feature 2']
    public array $tech_stack = []; // ['Laravel', 'Vue']
    public array $metadata = []; // [['key' => '', 'value' => '']]
    public string $demo_url = '';
    public string $documentation_url = '';
    public string $download_url = '';
    public int $sort_order = 0;
    public bool $is_active = true;
    public bool $is_published = false;
    public bool $is_featured = false;

    public function updatedName(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $product = Product::findOrFail($id);
        $this->editId = $id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->product_code = $product->product_code ?? '';
        $this->platform = $product->platform ?? '';
        $this->current_version = $product->current_version ?? '';
        $this->pricing_type = $product->pricing_type ?? 'freemium';
        $this->description = $product->description ?? '';
        $this->logo = null;
        $this->existing_logo = $product->logo ?? null;

        $this->app_identifier = $product->app_identifier ?? '';
        $this->secret_key = $product->secret_key ?? '';
        $this->support_email = $product->support_email ?? '';
        $this->webhook_url = $product->webhook_url ?? '';
        $this->max_devices = $product->max_devices ?? 0;
        $this->default_duration_days = $product->default_duration_days ?? 365;
        $this->min_app_version = $product->min_app_version ?? '';
        $this->max_app_version = $product->max_app_version ?? '';
        $this->offline_ttl_hours = $product->offline_ttl_hours ?? 24;
        $this->grace_period_days = $product->grace_period_days ?? 7;

        $this->tagline = $product->tagline ?? '';
        $this->full_description = $product->full_description ?? '';
        $this->category = $product->category ?? '';
        $this->product_type = $product->product_type ?? '';
        $this->price = $product->price ?? '0.00';
        $this->sale_price = $product->sale_price ?? '';
        $this->currency = $product->currency ?? 'USD';
        $this->cover_image = null;
        $this->existing_cover_image = $product->cover_image ?? null;
        $this->gallery_files = [];
        $this->existing_gallery = $product->gallery ?? [];
        $this->gallery_text = '';
        $this->features = array_values($product->features ?? []);
        $this->tech_stack = array_values($product->tech_stack ?? []);
        $rawMeta = $product->metadata ?? [];
        $this->metadata = collect($rawMeta)->map(fn($v, $k) => ['key' => $k, 'value' => $v])->values()->toArray();
        if (empty($this->metadata)) {
            $this->metadata = [['key' => '', 'value' => '']];
        }
        $this->demo_url = $product->demo_url ?? '';
        $this->documentation_url = $product->documentation_url ?? '';
        $this->download_url = $product->download_url ?? '';
        $this->sort_order = $product->sort_order ?? 0;
        $this->is_active = $product->is_active;
        $this->is_published = $product->is_published;
        $this->is_featured = $product->is_featured;

        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:products,slug' . ($this->editMode ? ',' . $this->editId . ',id' : ''),
            'product_code' => 'nullable|string|max:50',
            'platform' => 'nullable|string|max:50',
            'current_version' => 'nullable|string|max:50',
            'pricing_type' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
            'app_identifier' => 'nullable|string|max:100',
            'support_email' => 'nullable|email|max:100',
            'max_devices' => 'nullable|integer|min:0',
            'default_duration_days' => 'nullable|integer|min:1',
            'offline_ttl_hours' => 'nullable|integer|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'tagline' => 'nullable|string|max:200',
            'full_description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'product_type' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'cover_image' => 'nullable|image|max:4096',
            'gallery_files.*' => 'nullable|image|max:4096',
            'features.*' => 'nullable|string|max:200',
            'tech_stack.*' => 'nullable|string|max:100',
            'metadata.*.key' => 'nullable|string|max:100',
            'metadata.*.value' => 'nullable|string|max:500',
            'demo_url' => 'nullable|url|max:255',
            'documentation_url' => 'nullable|url|max:255',
            'download_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'product_code' => $this->product_code ?: null,
            'platform' => $this->platform ?: null,
            'current_version' => $this->current_version ?: null,
            'pricing_type' => $this->pricing_type ?: null,
            'description' => $this->description ?: null,
            'logo' => $this->logo ? $this->logo->store('products/logos', 'public') : ($this->existing_logo ?: null),
            'app_identifier' => $this->app_identifier ?: null,
            'secret_key' => $this->secret_key ?: null,
            'support_email' => $this->support_email ?: null,
            'webhook_url' => $this->webhook_url ?: null,
            'max_devices' => $this->max_devices,
            'default_duration_days' => $this->default_duration_days,
            'min_app_version' => $this->min_app_version ?: null,
            'max_app_version' => $this->max_app_version ?: null,
            'offline_ttl_hours' => $this->offline_ttl_hours,
            'grace_period_days' => $this->grace_period_days,
            'tagline' => $this->tagline ?: null,
            'full_description' => $this->full_description ?: null,
            'category' => $this->category ?: null,
            'product_type' => $this->product_type ?: null,
            'price' => $this->price ?: null,
            'sale_price' => $this->sale_price ?: null,
            'currency' => $this->currency ?: 'USD',
            'cover_image' => $this->cover_image ? $this->cover_image->store('products/covers', 'public') : ($this->existing_cover_image ?: null),
            'gallery' => array_merge($this->existing_gallery, collect($this->gallery_files)->map(fn($f) => $f->store('products/gallery', 'public'))->toArray()),
            'features' => array_values(array_filter(array_map('trim', $this->features))),
            'tech_stack' => array_values(array_filter(array_map('trim', $this->tech_stack))),
            'demo_url' => $this->demo_url ?: null,
            'documentation_url' => $this->documentation_url ?: null,
            'download_url' => $this->download_url ?: null,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'metadata' => collect($this->metadata)->filter(fn($row) => !empty($row['key']))->mapWithKeys(fn($row) => [$row['key'] => $row['value']])->toArray(),
        ];

        if ($this->editMode) {
            Product::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Product updated.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

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

    private function parseArray(string $text): array
    {
        if (empty($text)) {
            return [];
        }
        $items = preg_split('/[\r\n,]+/', $text);
        return array_filter(array_map('trim', $items));
    }

    private function parseJson(string $text): array
    {
        if (empty($text)) {
            return [];
        }
        try {
            $decoded = json_decode($text, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addFeature(): void
    {
        $this->features[] = '';
    }
    public function removeFeature(int $i): void
    {
        array_splice($this->features, $i, 1);
    }

    public function addTechStack(): void
    {
        $this->tech_stack[] = '';
    }
    public function removeTechStack(int $i): void
    {
        array_splice($this->tech_stack, $i, 1);
    }

    public function addMetadata(): void
    {
        $this->metadata[] = ['key' => '', 'value' => ''];
    }
    public function removeMetadata(int $i): void
    {
        array_splice($this->metadata, $i, 1);
    }

    public function removeExistingGalleryImage(int $index): void
    {
        array_splice($this->existing_gallery, $index, 1);
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->slug = '';
        $this->product_code = '';
        $this->platform = '';
        $this->current_version = '';
        $this->pricing_type = 'freemium';
        $this->description = '';
        $this->logo = null;
        $this->existing_logo = null;
        $this->app_identifier = '';
        $this->secret_key = '';
        $this->support_email = '';
        $this->webhook_url = '';
        $this->max_devices = 0;
        $this->default_duration_days = 365;
        $this->min_app_version = '';
        $this->max_app_version = '';
        $this->offline_ttl_hours = 24;
        $this->grace_period_days = 7;
        $this->tagline = '';
        $this->full_description = '';
        $this->category = '';
        $this->product_type = '';
        $this->price = '0.00';
        $this->sale_price = '';
        $this->currency = 'USD';
        $this->cover_image = null;
        $this->existing_cover_image = null;
        $this->gallery_files = [];
        $this->existing_gallery = [];
        $this->gallery_text = '';
        $this->features = [];
        $this->tech_stack = [];
        $this->metadata = [['key' => '', 'value' => '']];
        $this->demo_url = '';
        $this->documentation_url = '';
        $this->download_url = '';
        $this->sort_order = 0;
        $this->is_active = true;
        $this->is_published = false;
        $this->is_featured = false;
        $this->metadata_text = '';
        $this->editId = null;
        $this->resetValidation();
    }

    #[Computed]
    public function products()
    {
        return Product::when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('product_code', 'like', '%' . $this->search . '%'))->when($this->filterPlatform, fn($q) => $q->where('platform', $this->filterPlatform))->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))->when($this->filterStatus, fn($q) => $q->where('is_active', $this->filterStatus === 'active'))->latest()->paginate(15);
    }
}; ?>

<div>
    <x-slot:heading>Site Notifications</x-slot:heading>

    {{-- Main content --}}
    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..."
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterPlatform"
                    class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Platforms</option>
                    <option value="web">Web</option>
                    <option value="desktop">Desktop</option>
                    <option value="mobile">Mobile</option>
                </select>
                <select wire:model.live="filterCategory"
                    class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="erp">ERP</option>
                    <option value="hrms">HRMS</option>
                    <option value="software">Software</option>
                </select>
                <select wire:model.live="filterStatus"
                    class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
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
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Name</th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Platform</th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Category</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Price</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Published</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status</th>
                            <th
                                class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $product->product_code ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-slate-600">{{ $product->platform ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-slate-600">{{ $product->category ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($product->price)
                                        <span class="text-slate-900 font-semibold">{{ $product->currency ?? 'USD' }}
                                            {{ number_format($product->effective_price, 2) }}</span>
                                        @if ($product->is_on_sale)
                                            <p class="text-xs text-red-600 line-through">
                                                {{ number_format($product->price, 2) }}</p>
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
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
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete('{{ $product->id }}')"
                                            wire:confirm="Delete this product?"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No products
                                    yet. Create your first one!</td>
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

    {{-- Slide-over Form --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex">
            <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
            <div class="relative ml-auto w-full max-w-3xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
                <div
                    class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ $editMode ? 'Edit Product' : 'New Product' }}
                    </h2>
                    <button wire:click="$set('showForm', false)"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="flex-1 px-6 py-5 space-y-6">
                    {{-- Core Fields --}}
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Core Information</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Name *</label>
                                <input wire:model="name" type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Slug *</label>
                                <input wire:model="slug" type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                @error('slug')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Code</label>
                                    <input wire:model="product_code" type="text"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Platform</label>
                                    <input wire:model="platform" type="text" placeholder="web, desktop, mobile"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Version</label>
                                    <input wire:model="current_version" type="text" placeholder="1.0.0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pricing Type</label>
                                    <select wire:model="pricing_type"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                        <option value="freemium">Freemium</option>
                                        <option value="paid">Paid</option>
                                        <option value="free">Free</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Logo</label>
                                    @if ($existing_logo)
                                        <div class="mb-2 flex items-center gap-2">
                                            <img src="{{ Storage::url($existing_logo) }}" alt="Logo"
                                                class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                            <button type="button" wire:click="$set('existing_logo', null)"
                                                class="text-xs text-red-500 hover:underline">Remove</button>
                                        </div>
                                    @endif
                                    @if ($logo)
                                        <div class="mb-2">
                                            <img src="{{ $logo->temporaryUrl() }}" alt="Preview"
                                                class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                        </div>
                                    @endif
                                    <input wire:model="logo" type="file" accept="image/*"
                                        class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                    @error('logo')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                                <textarea wire:model="description" rows="2"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Shop Fields --}}
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Shop Information</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tagline</label>
                                <input wire:model="tagline" type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Description</label>
                                <livewire:markdown-editor wire:model="full_description"
                                    placeholder="Write your blog post content with markdown..." :rows="8"
                                    :show-toolbar="true" :show-upload="true" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                                    <input wire:model="category" type="text" placeholder="erp, hrms, software"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Type</label>
                                    <input wire:model="product_type" type="text" placeholder="digital, physical"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Price</label>
                                    <input wire:model="price" type="number" step="0.01" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sale Price</label>
                                    <input wire:model="sale_price" type="number" step="0.01" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                                    <input wire:model="currency" type="text" placeholder="USD" maxlength="3"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Cover Image</label>
                                @if ($existing_cover_image)
                                    <div class="mb-2 flex items-center gap-2">
                                        <img src="{{ Storage::url($existing_cover_image) }}" alt="Cover"
                                            class="h-16 w-24 rounded-lg object-cover border border-slate-200">
                                        <button type="button" wire:click="$set('existing_cover_image', null)"
                                            class="text-xs text-red-500 hover:underline">Remove</button>
                                    </div>
                                @endif
                                @if ($cover_image)
                                    <div class="mb-2">
                                        <img src="{{ $cover_image->temporaryUrl() }}" alt="Preview"
                                            class="h-16 w-24 rounded-lg object-cover border border-slate-200">
                                    </div>
                                @endif
                                <input wire:model="cover_image" type="file" accept="image/*"
                                    class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                @error('cover_image')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Gallery Images</label>
                                @if (count($existing_gallery))
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($existing_gallery as $i => $url)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($url) }}"
                                                    class="h-14 w-14 rounded-lg object-cover border border-slate-200">
                                                <button type="button"
                                                    wire:click="removeExistingGalleryImage({{ $i }})"
                                                    class="absolute -top-1 -right-1 hidden group-hover:flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white text-xs">✕</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if (count($gallery_files))
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($gallery_files as $file)
                                            <img src="{{ $file->temporaryUrl() }}"
                                                class="h-14 w-14 rounded-lg object-cover border border-slate-200">
                                        @endforeach
                                    </div>
                                @endif
                                <input wire:model="gallery_files" type="file" accept="image/*" multiple
                                    class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                @error('gallery_files.*')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Features (one per
                                    line)</label>
                                <textarea wire:model="features_text" rows="2" placeholder="Feature 1&#10;Feature 2"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tech Stack (one per
                                    line)</label>
                                <textarea wire:model="tech_stack_text" rows="2" placeholder="Tech 1&#10;Tech 2"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Demo URL</label>
                                    <input wire:model="demo_url" type="url"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Documentation
                                        URL</label>
                                    <input wire:model="documentation_url" type="url"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Download URL</label>
                                    <input wire:model="download_url" type="url"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Sort Order</label>
                                <input wire:model="sort_order" type="number" min="0"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>
                    </div>

                    {{-- Licensing Fields --}}
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Licensing</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">App Identifier</label>
                                <input wire:model="app_identifier" type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                <p class="mt-1 text-xs text-slate-500">Leave empty if not licensable</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Secret Key</label>
                                <div class="flex items-center gap-2">
                                    <input wire:model="secret_key" type="text" readonly
                                        class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50">
                                    <p class="text-xs text-slate-500">(Auto-generated)</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Support
                                        Email</label>
                                    <input wire:model="support_email" type="email"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Webhook URL</label>
                                    <input wire:model="webhook_url" type="url"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Max Devices</label>
                                    <input wire:model="max_devices" type="number" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Default Duration
                                        (days)</label>
                                    <input wire:model="default_duration_days" type="number" min="1"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Offline TTL
                                        (hours)</label>
                                    <input wire:model="offline_ttl_hours" type="number" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Grace Period
                                        (days)</label>
                                    <input wire:model="grace_period_days" type="number" min="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Min App
                                        Version</label>
                                    <input wire:model="min_app_version" type="text" placeholder="1.0.0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Max App
                                        Version</label>
                                    <input wire:model="max_app_version" type="text" placeholder="2.0.0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Metadata & Status --}}
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Metadata & Status</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Metadata (JSON)</label>
                                <textarea wire:model="metadata_text" rows="2" placeholder='{"key": "value"}'
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none font-mono text-xs"></textarea>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_active" type="checkbox"
                                        class="rounded border-slate-300 text-cyan-600">
                                    <span class="text-sm text-slate-700">Active</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_published" type="checkbox"
                                        class="rounded border-slate-300 text-cyan-600">
                                    <span class="text-sm text-slate-700">Published</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="is_featured" type="checkbox"
                                        class="rounded border-slate-300 text-cyan-600">
                                    <span class="text-sm text-slate-700">Featured</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                        <button type="submit"
                            class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            {{ $editMode ? 'Update Product' : 'Create Product' }}
                        </button>
                        <button type="button" wire:click="$set('showForm', false)"
                            class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
