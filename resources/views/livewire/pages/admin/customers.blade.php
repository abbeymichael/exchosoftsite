<?php

use App\Models\Customer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Customers — ExchoLicense')] class extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterType  = '';
    public bool   $showModal   = false;
    public bool   $editing     = false;
    public ?int   $editingId   = null;

    // Form fields
    public string $name      = '';
    public string $email     = '';
    public string $company   = '';
    public string $phone     = '';
    public string $type      = 'individual';
    public string $notes     = '';
    public bool   $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'email', 'company', 'phone', 'notes']);
        $this->type      = 'individual';
        $this->is_active = true;
        $this->editing   = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $customer        = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $customer->name;
        $this->email     = $customer->email;
        $this->company   = $customer->company ?? '';
        $this->phone     = $customer->phone ?? '';
        $this->type      = $customer->type;
        $this->notes     = $customer->notes ?? '';
        $this->is_active = $customer->is_active;
        $this->editing   = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . ($this->editingId ?? 'NULL'),
            'type'  => 'required|in:individual,company',
            'phone' => 'nullable|string|max:30',
        ]);

        $data = [
            'name'      => $this->name,
            'email'     => $this->email,
            'company'   => $this->company ?: null,
            'phone'     => $this->phone ?: null,
            'type'      => $this->type,
            'notes'     => $this->notes ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editing && $this->editingId) {
            Customer::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Customer updated successfully.');
        } else {
            Customer::create($data);
            session()->flash('success', 'Customer created successfully.');
        }

        $this->showModal = false;
    }

    public function deleteCustomer(int $id): void
    {
        Customer::findOrFail($id)->delete();
        session()->flash('success', 'Customer deleted.');
    }

    #[Computed]
    public function customers()
    {
        return Customer::query()
            ->withCount('licenses')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('company', 'like', "%{$this->search}%"))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(12);
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Customers</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <input type="text"
                       wire:model.live="search"
                       placeholder="Search name, email or company…"
                       class="w-full sm:w-72 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <select wire:model.live="filterType"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Types</option>
                    <option value="individual">Individual</option>
                    <option value="company">Company</option>
                </select>
            </div>
            <button wire:click="openCreate"
                    class="flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Customer
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Licenses</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->customers as $customer)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-700">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $customer->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-600">{{ $customer->email }}</td>
                            <td class="px-6 py-3 text-sm text-slate-600">{{ $customer->company ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $customer->type === 'company' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ucfirst($customer->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-900">{{ $customer->licenses_count }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $customer->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button wire:click="openEdit({{ $customer->id }})"
                                            class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Edit</button>
                                    <button wire:click="deleteCustomer({{ $customer->id }})"
                                            wire:confirm="Delete this customer? Their licenses will remain."
                                            class="text-sm font-medium text-red-600 hover:text-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                No customers found. <button wire:click="openCreate" class="text-cyan-600 hover:underline">Add your first customer</button>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $this->customers->links() }}
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
                        {{ $editing ? 'Edit Customer' : 'New Customer' }}
                    </h2>
                    <button wire:click="$set('showModal', false)"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                               placeholder="Jane Smith">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                               placeholder="jane@example.com">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Type <span class="text-red-500">*</span></label>
                            <select wire:model="type"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                            </select>
                            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input type="text" wire:model="phone"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                   placeholder="+1 555 000 0000">
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                        <input type="text" wire:model="company"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                               placeholder="Acme Corp (optional)">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                                  placeholder="Optional notes…"></textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="cust_is_active" wire:model="is_active"
                               class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <label for="cust_is_active" class="text-sm text-slate-700">Active customer</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            {{ $editing ? 'Save Changes' : 'Create Customer' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
