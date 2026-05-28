<?php

use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Shop Products — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';

    // Form
    public bool $showForm = false;
    public bool $editMode = false;
    public ?int $editId = null;

    public string $name = '';
    public string $slug = '';
    public string $tagline = '';
    public string $description = '';
    public string $category = 'software';
    public string $product_type = 'digital';
    public string $price = '0';
    public string $sale_price = '';
    public string $version = '';
    public string $platform = '';
    public string $demo_url = '';
    public string $documentation_url = '';
    public string $download_url = '';
    public string $linked_product_code = '';
    public string $features_text = '';    // comma or newline separated
    public string $tech_stack_text = '';  // comma or newline separated
    public string $full_description = '';
    public bool $is_published = false;
    public bool $is_featured = false;
    public bool $requires_license = true;

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

    public function openEdit(int $id): void
    {
        $product = ShopProduct::findOrFail($id);
        $this->editId = $id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->tagline = $product->tagline ?? '';
        $this->description = $product->description ?? '';
        $this->category = $product->category;
        $this->product_type = $product->product_type;
        $this->price = (string) $product->price;
        $this->sale_price = $product->sale_price ? (string) $product->sale_price : '';
        $this->version = $product->version ?? '';
        $this->platform = $product->platform ?? '';
        $this->demo_url = $product->demo_url ?? '';
        $this->documentation_url = $product->documentation_url ?? '';
        $this->download_url = $product->download_url ?? '';
        $this->linked_product_code = $product->linked_product_code ?? '';
        $this->features_text = $product->features ? implode("\n", $product->features) : '';
        $this->tech_stack_text = $product->tech_stack ? implode(', ', $product->tech_stack) : '';
        $this->full_description = $product->full_description ?? '';
        $this->is_published = $product->is_published;
        $this->is_featured = $product->is_featured;
        $this->requires_license = $product->requires_license;
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'         => 'required|string|max:200',
            'slug'         => 'required|string|max:200',
            'price'        => 'required|numeric|min:0',
            'sale_price'   => 'nullable|numeric|min:0',
            'category'     => 'required|string',
            'product_type' => 'required|string',
        ]);

        $data = [
            'name'                 => $this->name,
            'slug'                 => $this->slug,
            'tagline'              => $this->tagline,
            'description'          => $this->description,
            'category'             => $this->category,
            'product_type'         => $this->product_type,
            'price'                => $this->price,
            'sale_price'           => $this->sale_price ?: null,
            'version'              => $this->version,
            'platform'             => $this->platform,
            'demo_url'             => $this->demo_url,
            'documentation_url'    => $this->documentation_url,
            'download_url'         => $this->download_url,
            'linked_product_code'  => $this->linked_product_code,
            'full_description'     => $this->full_description,
            'features'             => $this->features_text
                ? array_filter(array_map('trim', preg_split('/[\r\n]+/', $this->features_text)))
                : null,
            'tech_stack'           => $this->tech_stack_text
                ? array_filter(array_map('trim', explode(',', $this->tech_stack_text)))
                : null,
            'is_published'         => $this->is_published,
            'is_featured'          => $this->is_featured,
            'requires_license'     => $this->requires_license,
        ];

        if ($this->editMode) {
            ShopProduct::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            ShopProduct::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function togglePublished(int $id): void
    {
        $product = ShopProduct::findOrFail($id);
        $product->update(['is_published' => !$product->is_published]);
    }

    public function delete(int $id): void
    {
        ShopProduct::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function resetForm(): void
    {
        $this->name = $this->slug = $this->tagline = $this->description = '';
        $this->category = 'software';
        $this->product_type = 'digital';
        $this->price = '0';
        $this->sale_price = $this->version = $this->platform = '';
        $this->demo_url = $this->documentation_url = $this->download_url = $this->linked_product_code = '';
        $this->features_text = $this->tech_stack_text = $this->full_description = '';
        $this->is_published = $this->is_featured = false;
        $this->requires_license = true;
        $this->editId = null;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $products = ShopProduct::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('slug', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterStatus === 'published', fn($q) => $q->where('is_published', true))
            ->when($this->filterStatus === 'draft', fn($q) => $q->where('is_published', false))
            ->latest()
            ->paginate(15);

        return view('pages.admin.shop-products', compact('products'));
    }
}; ?>

<div>
    <x-slot:heading>Shop Products</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100 w-56">
                </div>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="software">Software</option>
                    <option value="template">Template</option>
                    <option value="course">Course</option>
                    <option value="service">Service</option>
                </select>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Product
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Price</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Sales</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Featured</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $product->slug }}</p>
                                @if($product->tagline)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $product->tagline }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $product->category }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <p class="font-semibold text-slate-900">GHS {{ number_format($product->price, 2) }}</p>
                                @if($product->sale_price)
                                    <p class="text-xs text-green-600">Sale: GHS {{ number_format($product->sale_price, 2) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-semibold text-slate-900">{{ number_format($product->sales_count) }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <button wire:click="togglePublished({{ $product->id }})"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $product->is_published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    {{ $product->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($product->is_featured)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">⭐ Featured</span>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit({{ $product->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No products found. Create your first product.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $products->links() }}</div>
            @endif
        </div>

    </div>

    {{-- Slide-over Form --}}
    @if($showForm)
    {{-- EasyMDE for markdown editing --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Product' : 'New Product' }}</h2>
                <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="flex-1 px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Product Name *</label>
                        <input wire:model.live="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Slug *</label>
                        <input wire:model="slug" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-mono focus:outline-none focus:border-cyan-400">
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tagline</label>
                        <input wire:model="tagline" type="text" placeholder="Short catchy description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Short Description</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                        <select wire:model="category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="software">Software</option>
                            <option value="template">Template</option>
                            <option value="course">Course</option>
                            <option value="service">Service</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Product Type</label>
                        <select wire:model="product_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="digital">Digital</option>
                            <option value="physical">Physical</option>
                            <option value="service">Service</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Price (GHS) *</label>
                        <input wire:model="price" type="number" step="0.01" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Sale Price (GHS)</label>
                        <input wire:model="sale_price" type="number" step="0.01" min="0" placeholder="Optional" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Version</label>
                        <input wire:model="version" type="text" placeholder="e.g. 1.0.0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Platform</label>
                        <input wire:model="platform" type="text" placeholder="Windows, Web, Cross-platform" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Demo URL</label>
                        <input wire:model="demo_url" type="url" placeholder="https://" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Documentation URL</label>
                        <input wire:model="documentation_url" type="url" placeholder="https://" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Linked Product Code
                            <span class="text-slate-400 font-normal ml-1">(groups on site: washops, churchops, etc.)</span>
                        </label>
                        <input wire:model="linked_product_code" type="text" placeholder="e.g. washops, churchops" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Key Features
                            <span class="text-slate-400 font-normal ml-1">(one per line)</span>
                        </label>
                        <textarea wire:model="features_text" rows="4" placeholder="Revenue tracking and forecasting&#10;Order volume analytics&#10;Staff performance metrics" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-y font-mono text-xs"></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Tech Stack
                            <span class="text-slate-400 font-normal ml-1">(comma separated)</span>
                        </label>
                        <input wire:model="tech_stack_text" type="text" placeholder="C#, .NET, SQLite, WPF, AWS" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Download URL</label>
                        <input wire:model="download_url" type="url" placeholder="https://" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Full Description
                            <span class="text-slate-400 font-normal ml-1">(Markdown supported)</span>
                        </label>
                        <textarea id="product-full-desc-editor" wire:model="full_description" rows="8"
                            placeholder="## Product Overview&#10;&#10;Write a detailed description with **bold**, _italic_, ## headings, - lists..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-y font-mono text-xs"></textarea>
                        <p class="text-xs text-slate-400 mt-1">Markdown will be rendered on the product detail page.</p>
                    </div>
                    <div class="col-span-2 flex flex-wrap gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_published" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Published</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_featured" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Featured on Homepage</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="requires_license" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Requires License Key</span>
                        </label>
                    </div>
                </div>
                <script>
                    document.addEventListener('livewire:initialized', function() {
                        initMDE();
                    });
                    document.addEventListener('livewire:navigated', function() {
                        initMDE();
                    });
                    function initMDE() {
                        const el = document.getElementById('product-full-desc-editor');
                        if (el && !el._mde) {
                            el._mde = new EasyMDE({
                                element: el,
                                spellChecker: false,
                                autosave: false,
                                minHeight: '160px',
                                toolbar: ['bold','italic','heading','|','quote','unordered-list','ordered-list','|','link','image','|','preview','guide'],
                            });
                            el._mde.codemirror.on('change', function() {
                                @this.set('full_description', el._mde.value());
                            });
                        }
                    }
                    setTimeout(initMDE, 200);
                </script>
                <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                        {{ $editMode ? 'Update Product' : 'Create Product' }}
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
