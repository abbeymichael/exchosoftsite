<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

new #[Layout('layouts.admin')] #[Title('Products — ExchoLicense')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $platform = '';
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $slug = '';
    public string $product_code = '';
    public string $selectedPlatform = 'desktop';
    public string $current_version = '1.0.0';
    public string $pricing_type = 'lifetime';
    public string $description = '';
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'product_code', 'description', 'current_version']);
        $this->selectedPlatform = 'desktop';
        $this->pricing_type = 'lifetime';
        $this->is_active = true;
        $this->editing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editingId = $id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->product_code = $product->product_code;
        $this->selectedPlatform = $product->platform;
        $this->current_version = $product->current_version;
        $this->pricing_type = $product->pricing_type;
        $this->description = $product->description ?? '';
        $this->is_active = $product->is_active;
        $this->editing = true;
        $this->showModal = true;
    }

    public function updatedName(): void
    {
        if (!$this->editing) {
            $this->slug = Str::slug($this->name);
            $this->product_code = strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $this->name), 0, 6));
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'required|string|max:255|alpha_dash|unique:products,slug,' . ($this->editingId ?? 'NULL'),
            'product_code'     => 'required|string|max:10|unique:products,product_code,' . ($this->editingId ?? 'NULL'),
            'selectedPlatform' => 'required|in:desktop,saas,hybrid,offline-first',
            'current_version'  => 'required|string|max:20',
            'pricing_type'     => 'required|in:lifetime,subscription,trial,free',
        ]);

        $data = [
            'name'            => $this->name,
            'slug'            => $this->slug,
            'product_code'    => strtoupper($this->product_code),
            'platform'        => $this->selectedPlatform,
            'current_version' => $this->current_version,
            'pricing_type'    => $this->pricing_type,
            'description'     => $this->description ?: null,
            'is_active'       => $this->is_active,
        ];

        if ($this->editing && $this->editingId) {
            Product::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->showModal = false;
        $this->reset(['name', 'slug', 'product_code', 'description']);
        unset($this->products);
    }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        unset($this->products);
    }

    public function deleteProduct(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
        unset($this->products);
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('product_code', 'like', "%{$this->search}%"))
            ->when($this->platform, fn($q) => $q->where('platform', $this->platform))
            ->withCount('licenses')
            ->latest()
            ->paginate(10);
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Products</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3 flex-1">
                <input type="text" wire:model.live="search" placeholder="Search products…"
                    class="w-full sm:w-72 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <select wire:model.live="platform"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Platforms</option>
                    <option value="desktop">Desktop</option>
                    <option value="saas">SaaS</option>
                    <option value="hybrid">Hybrid</option>
                    <option value="offline-first">Offline-first</option>
                </select>
            </div>
            <button wire:click="openCreate"
                class="flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Product
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Platform</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Licenses</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->products as $product)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                        @if ($product->description)
                                            <p class="text-xs text-slate-400 truncate max-w-xs">{{ $product->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm font-mono text-slate-700">{{ $product->product_code }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ match ($product->platform) {
                                            'desktop'       => 'bg-blue-50 text-blue-700',
                                            'saas'          => 'bg-green-50 text-green-700',
                                            'hybrid'        => 'bg-violet-50 text-violet-700',
                                            'offline-first' => 'bg-orange-50 text-orange-700',
                                            default         => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ ucfirst($product->platform) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-600">{{ $product->current_version }}</td>
                                <td class="px-6 py-3 text-sm text-slate-900 font-medium">{{ $product->licenses_count }}</td>
                                <td class="px-6 py-3">
                                    <button wire:click="toggleActive({{ $product->id }})"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors
                                            {{ $product->is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button wire:click="openEdit({{ $product->id }})"
                                            class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Edit</button>
                                        <button wire:click="deleteProduct({{ $product->id }})"
                                            wire:confirm="Are you sure you want to delete this product?"
                                            class="text-sm font-medium text-red-600 hover:text-red-700">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                    No products found. <button wire:click="openCreate"
                                        class="text-cyan-600 hover:underline">Add your first product</button>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $this->products->links() }}
        </div>

    </div>

    {{-- Modal — kept inside the single root div, shown/hidden via x-show to avoid @if root-element issues --}}
    <div
        x-data
        x-show="$wire.showModal"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        style="display: none; position: fixed; inset: 0; z-index: 200; overflow-y: auto;"
        aria-modal="true"
        role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">

            {{-- Backdrop --}}
            <div
                style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(2px);"
                x-on:click="$wire.set('showModal', false)"
            ></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">

                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $editing ? 'Edit Product' : 'New Product' }}
                    </h2>
                    <button wire:click="$set('showModal', false)"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">

                    {{-- Product Name --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live="name"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                            placeholder="My Awesome App">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug + Product Code --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="slug"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                placeholder="my-awesome-app">
                            @error('slug')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Product Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="product_code"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono uppercase focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                placeholder="MYAPP">
                            @error('product_code')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Platform + Version --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Platform <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="selectedPlatform"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="desktop">Desktop</option>
                                <option value="saas">SaaS</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="offline-first">Offline-first</option>
                            </select>
                            @error('selectedPlatform')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Version <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="current_version"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                placeholder="1.0.0">
                            @error('current_version')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Pricing Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Pricing Type <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="pricing_type"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="lifetime">Lifetime</option>
                            <option value="subscription">Subscription</option>
                            <option value="trial">Trial</option>
                            <option value="free">Free</option>
                        </select>
                        @error('pricing_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="2"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                            placeholder="Optional product description…"></textarea>
                    </div>

                    {{-- Active toggle --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" wire:model="is_active"
                            class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <label for="is_active" class="text-sm text-slate-700">
                            Active (visible for license assignment)
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            {{ $editing ? 'Save Changes' : 'Create Product' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
