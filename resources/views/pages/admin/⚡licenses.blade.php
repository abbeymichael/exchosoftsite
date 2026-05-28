<?php

use App\Models\Customer;
use App\Models\License;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Licenses — ExchoLicense')] class extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterProduct = '';
    public bool   $showModal     = false;
    public bool   $editing       = false;
    public ?int   $editingId     = null;

    // Form fields — customer is fully optional
    public int    $product_id        = 0;
    public string $customer_email    = '';   // type email → auto-create customer
    public string $customer_name     = '';
    public string $license_key       = '';
    public string $edition           = 'standard';
    public string $type              = 'lifetime';
    public int    $max_activations   = 1;
    public string $status            = 'active';
    public string $expires_at        = '';
    public string $duration_mode     = 'lifetime'; // lifetime | days | date
    public int    $duration_days     = 365;
    public string $notes             = '';

    // Edit-only: when editing we show the raw customer name (read-only)
    public string $edit_customer_display = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset([
            'product_id', 'customer_email', 'customer_name',
            'license_key', 'notes', 'expires_at', 'duration_days',
        ]);
        $this->edition         = 'standard';
        $this->type            = 'lifetime';
        $this->max_activations = 1;
        $this->status          = 'active';
        $this->duration_mode   = 'lifetime';
        $this->duration_days   = 365;
        $this->license_key     = License::generateKey();
        $this->editing         = false;
        $this->editingId       = null;
        $this->showModal       = true;
    }

    public function generateKey(): void
    {
        $product           = $this->product_id ? Product::find($this->product_id) : null;
        $prefix            = $product ? strtoupper(substr($product->product_code ?? 'EXCL', 0, 4)) : 'EXCL';
        $this->license_key = License::generateKey($prefix);
    }

    public function openEdit(int $id): void
    {
        $license                     = License::with('customer')->findOrFail($id);
        $this->editingId             = $id;
        $this->product_id            = $license->product_id;
        $this->license_key           = $license->license_key;
        $this->edition               = $license->edition;
        $this->type                  = $license->type;
        $this->max_activations       = $license->max_activations;
        $this->status                = $license->status;
        $this->expires_at            = $license->expires_at ? $license->expires_at->format('Y-m-d') : '';
        $this->notes                 = $license->notes ?? '';
        $this->edit_customer_display = $license->customer?->name ?? $license->customer?->email ?? '(no customer)';
        $this->duration_mode         = $license->expires_at ? 'date' : 'lifetime';
        $this->editing               = true;
        $this->showModal             = true;
    }

    public function save(): void
    {
        $rules = [
            'product_id'      => 'required|exists:products,id',
            'license_key'     => 'required|string|max:60|unique:licenses,license_key,' . ($this->editingId ?? 'NULL'),
            'edition'         => 'required|in:standard,professional,enterprise,trial',
            'type'            => 'required|in:lifetime,monthly,annual,trial,floating,multi-device',
            'max_activations' => 'required|integer|min:1|max:999',
            'status'          => 'required|in:active,inactive,expired,suspended,revoked,trial',
            'duration_mode'   => 'required|in:lifetime,days,date',
        ];

        if (! $this->editing) {
            $rules['customer_email'] = 'nullable|email|max:255';
            $rules['customer_name']  = 'nullable|string|max:255';
            $rules['duration_days']  = 'required_if:duration_mode,days|nullable|integer|min:1|max:36500';
            $rules['expires_at']     = 'required_if:duration_mode,date|nullable|date';
        } else {
            $rules['expires_at'] = 'nullable|date';
        }

        $this->validate($rules);

        // Resolve expiry
        $expiresAt = null;
        if (! $this->editing) {
            if ($this->duration_mode === 'days') {
                $expiresAt = now()->addDays($this->duration_days)->format('Y-m-d');
            } elseif ($this->duration_mode === 'date' && $this->expires_at) {
                $expiresAt = $this->expires_at;
            }
        } else {
            $expiresAt = $this->expires_at ?: null;
        }

        $data = [
            'product_id'      => $this->product_id,
            'license_key'     => strtoupper($this->license_key),
            'edition'         => $this->edition,
            'type'            => $this->type,
            'max_activations' => $this->max_activations,
            'status'          => $this->status,
            'expires_at'      => $expiresAt,
            'notes'           => $this->notes ?: null,
        ];

        if ($this->editing && $this->editingId) {
            License::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'License updated successfully.');
        } else {
            // Auto-resolve or create customer from email (optional)
            if ($this->customer_email) {
                $customer = Customer::firstOrCreate(
                    ['email' => strtolower(trim($this->customer_email))],
                    [
                        'name' => $this->customer_name ?: $this->customer_email,
                        'type' => 'individual',
                        'is_active' => true,
                    ]
                );
                $data['customer_id'] = $customer->id;
            }

            License::create($data);
            session()->flash('success', 'License created successfully.');
        }

        $this->showModal = false;
    }

    public function revoke(int $id): void
    {
        License::findOrFail($id)->update(['status' => 'revoked', 'revoked_at' => now()]);
        session()->flash('success', 'License revoked.');
    }

    public function deleteLicense(int $id): void
    {
        License::findOrFail($id)->delete();
        session()->flash('success', 'License deleted.');
    }

    #[Computed]
    public function licenses()
    {
        return License::query()
            ->with(['product', 'customer'])
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
}; ?>

<div>
    <x-slot:heading>Licenses</x-slot:heading>

    <div class="space-y-5">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search key, customer, email…"
                           class="pl-9 pr-4 py-2 w-72 rounded-lg border border-slate-300 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                </div>

                <select wire:model.live="filterStatus"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive (pre-activation)</option>
                    <option value="expired">Expired</option>
                    <option value="suspended">Suspended</option>
                    <option value="revoked">Revoked</option>
                    <option value="trial">Trial</option>
                </select>

                <select wire:model.live="filterProduct"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Products</option>
                    @foreach($this->products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate"
                    class="flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New License
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">License Key</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Edition / Type</th>
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
                                <td class="px-5 py-3 text-slate-600">{{ $license->product?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-600">
                                    @if($license->customer)
                                        <div class="font-medium text-slate-900">{{ $license->customer->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $license->customer->email }}</div>
                                    @else
                                        <span class="text-slate-400 italic">No customer</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-600 capitalize">
                                    {{ $license->edition }}<br>
                                    <span class="text-xs text-slate-400">{{ $license->type }}</span>
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    <span class="{{ ($license->current_activations ?? 0) >= $license->max_activations ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $license->current_activations ?? 0 }} / {{ $license->max_activations }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    {{ $license->expires_at ? $license->expires_at->format('M d, Y') : '∞ Lifetime' }}
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
                                    <div class="flex items-center justify-end gap-3">
                                        <button wire:click="openEdit({{ $license->id }})"
                                                class="text-xs font-medium text-cyan-600 hover:text-cyan-800 transition-colors">Edit</button>
                                        @if(! in_array($license->status, ['revoked']))
                                            <button wire:click="revoke({{ $license->id }})"
                                                    wire:confirm="Revoke this license? The customer will lose access."
                                                    class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">Revoke</button>
                                        @endif
                                        <button wire:click="deleteLicense({{ $license->id }})"
                                                wire:confirm="Permanently delete this license? This cannot be undone."
                                                class="text-xs font-medium text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
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

        {{-- Pagination --}}
        <div>{{ $this->licenses->links() }}</div>

    </div>

    {{-- ────────────────────── Create / Edit Modal ────────────────────── --}}
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
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden"
                 x-on:click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100">
                            <svg class="h-4 w-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-slate-900">
                            {{ $editing ? 'Edit License' : 'Create New License' }}
                        </h2>
                    </div>
                    <button wire:click="$set('showModal', false)"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-5">

                    {{-- ① Product (required) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="product_id"
                                wire:change="generateKey"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <option value="0">— Select a product —</option>
                            @foreach($this->products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @if(! $editing)
                    {{-- ② Customer (optional) — email auto-creates customer --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Customer — Optional
                        </p>
                        <p class="text-xs text-slate-500">
                            Enter an email to attach this license to a customer. If the customer doesn't exist yet, they'll be created automatically. Leave blank to create an unassigned license.
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                                <input type="email" wire:model="customer_email"
                                       placeholder="customer@example.com"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                @error('customer_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Name (optional)</label>
                                <input type="text" wire:model="customer_name"
                                       placeholder="John Smith"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Edit: show customer as read-only --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-slate-500">Customer</p>
                            <p class="text-sm font-medium text-slate-800">{{ $edit_customer_display }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- ③ License Key --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            License Key <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="license_key"
                                   class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono uppercase focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                   placeholder="EXCL-XXXX-XXXX-XXXX">
                            <button type="button" wire:click="generateKey"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-cyan-400 transition-colors">
                                ↻ Regenerate
                            </button>
                        </div>
                        @error('license_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- ④ Edition + Type --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Edition</label>
                            <select wire:model="edition"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="standard">Standard</option>
                                <option value="professional">Professional</option>
                                <option value="enterprise">Enterprise</option>
                                <option value="trial">Trial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">License Type</label>
                            <select wire:model="type"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="lifetime">Lifetime</option>
                                <option value="annual">Annual</option>
                                <option value="monthly">Monthly</option>
                                <option value="trial">Trial</option>
                                <option value="floating">Floating</option>
                                <option value="multi-device">Multi-device</option>
                            </select>
                        </div>
                    </div>

                    {{-- ⑤ Expiry --}}
                    @if(! $editing)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry</label>
                        <div class="flex gap-2 mb-2">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" wire:model.live="duration_mode" value="lifetime" class="text-cyan-600">
                                <span class="text-sm text-slate-700">Lifetime</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer ml-4">
                                <input type="radio" wire:model.live="duration_mode" value="days" class="text-cyan-600">
                                <span class="text-sm text-slate-700">Days from now</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer ml-4">
                                <input type="radio" wire:model.live="duration_mode" value="date" class="text-cyan-600">
                                <span class="text-sm text-slate-700">Specific date</span>
                            </label>
                        </div>
                        @if($duration_mode === 'days')
                            <input type="number" wire:model="duration_days" min="1" max="36500"
                                   placeholder="e.g. 365"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('duration_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @elseif($duration_mode === 'date')
                            <input type="date" wire:model="expires_at"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('expires_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @else
                            <p class="text-xs text-slate-400 italic">No expiry — license is valid forever.</p>
                        @endif
                    </div>
                    @else
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Expires At <span class="text-slate-400 font-normal text-xs">(leave blank for lifetime)</span>
                        </label>
                        <input type="date" wire:model="expires_at"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    </div>
                    @endif

                    {{-- ⑥ Max Activations + Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Activations</label>
                            <input type="number" wire:model="max_activations" min="1" max="999"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            @error('max_activations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                            <select wire:model="status"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive (pre-activation)</option>
                                <option value="suspended">Suspended</option>
                                <option value="revoked">Revoked</option>
                                <option value="trial">Trial</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- ⑦ Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes
                            <span class="text-slate-400 font-normal text-xs">(optional)</span>
                        </label>
                        <textarea wire:model="notes" rows="2"
                                  placeholder="Order ID, customer reference, internal memo…"
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 resize-none"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 shadow-sm transition-colors">
                            {{ $editing ? 'Save Changes' : 'Create License' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
