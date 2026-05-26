<?php

use App\Models\LicenseActivation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Activations — ExchoLicense')] class extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function deactivate(int $id): void
    {
        LicenseActivation::findOrFail($id)->update([
            'status'         => 'deactivated',
            'deactivated_at' => now(),
        ]);
        session()->flash('success', 'Device deactivated.');
    }

    public function revoke(int $id): void
    {
        LicenseActivation::findOrFail($id)->update(['status' => 'revoked']);
        session()->flash('success', 'Activation revoked.');
    }

    #[Computed]
    public function activations()
    {
        return LicenseActivation::query()
            ->with(['license.product', 'license.customer'])
            ->when($this->search, fn ($q) => $q
                ->where('device_name', 'like', "%{$this->search}%")
                ->orWhere('device_id', 'like', "%{$this->search}%")
                ->orWhere('ip_address', 'like', "%{$this->search}%"))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'       => LicenseActivation::count(),
            'active'      => LicenseActivation::where('status', 'active')->count(),
            'deactivated' => LicenseActivation::where('status', 'deactivated')->count(),
            'revoked'     => LicenseActivation::where('status', 'revoked')->count(),
        ];
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Activations</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-slate-900">{{ number_format($this->stats['total']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Total Activations</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-green-600">{{ number_format($this->stats['active']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Active</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-amber-600">{{ number_format($this->stats['deactivated']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Deactivated</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-red-600">{{ number_format($this->stats['revoked']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Revoked</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <input type="text"
                   wire:model.live="search"
                   placeholder="Search device name, ID or IP…"
                   class="w-full sm:w-80 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            <select wire:model.live="filterStatus"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="deactivated">Deactivated</option>
                <option value="revoked">Revoked</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Device</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">License</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Activated</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->activations as $activation)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $activation->device_name ?? 'Unknown Device' }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ Str::limit($activation->device_id, 24) }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm font-mono text-slate-700">
                                {{ $activation->license?->license_key ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-600">
                                {{ $activation->license?->customer?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm font-mono text-slate-600">{{ $activation->ip_address ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-slate-500">
                                {{ $activation->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ match($activation->status) {
                                        'active'      => 'bg-green-50 text-green-700',
                                        'deactivated' => 'bg-slate-100 text-slate-600',
                                        'revoked'     => 'bg-red-50 text-red-700',
                                        default       => 'bg-slate-100 text-slate-600',
                                    } }}">
                                    {{ ucfirst($activation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($activation->status === 'active')
                                        <button wire:click="deactivate({{ $activation->id }})"
                                                wire:confirm="Deactivate this device?"
                                                class="text-sm font-medium text-amber-600 hover:text-amber-700">
                                            Deactivate
                                        </button>
                                    @endif
                                    @if($activation->status !== 'revoked')
                                        <button wire:click="revoke({{ $activation->id }})"
                                                wire:confirm="Revoke this activation? This cannot be undone."
                                                class="text-sm font-medium text-red-600 hover:text-red-700">
                                            Revoke
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                No activations found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $this->activations->links() }}
        </div>

    </div>

</div>
