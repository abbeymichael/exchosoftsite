<?php

use App\Models\LicenseActivation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Activations — ExchoLicense')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Deactivate a device (reversible)
     */
    public function deactivate(string $id): void
    {
        $activation = LicenseActivation::findOrFail($id);

        $activation->deactivate();

        session()->flash('success', "Device '{$activation->device_name}' has been deactivated. It can be reactivated later.");
    }

    /**
     * Reactivate a previously deactivated device
     */
    public function reactivate(string $id): void
    {
        $activation = LicenseActivation::findOrFail($id);

        // Check if reactivation is possible
        if (!$activation->canReactivate()) {
            $reason = $activation->getReactivationBlockReason();
            session()->flash('error', "Cannot reactivate: {$reason}");
            return;
        }

        if ($activation->reactivate()) {
            session()->flash('success', "Device '{$activation->device_name}' has been reactivated.");
        } else {
            session()->flash('error', 'Failed to reactivate device.');
        }
    }

    /**
     * Permanently revoke a device (cannot be undone)
     */
    public function revoke(string $id): void
    {
        $activation = LicenseActivation::findOrFail($id);

        $activation->revoke();

        session()->flash('success', "Activation for '{$activation->device_name}' has been permanently revoked.");
    }

    #[Computed]
    public function activations()
    {
        return LicenseActivation::query()
            ->with(['license.product', 'license.customer'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query
                        ->where('device_name', 'like', "%{$this->search}%")
                        ->orWhere('device_id', 'like', "%{$this->search}%")
                        ->orWhere('ip_address', 'like', "%{$this->search}%")
                        ->orWhereHas('license', function ($license) {
                            $license->where('license_key', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => LicenseActivation::count(),
            'active' => LicenseActivation::where('status', 'active')->count(),
            'deactivated' => LicenseActivation::where('status', 'deactivated')->count(),
            'revoked' => LicenseActivation::where('status', 'revoked')->count(),
        ];
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Activations</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
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
            <input type="text" wire:model.live="search" placeholder="Search device name, ID or IP…"
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
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Device</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                License</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Customer</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                IP Address</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Activated</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->activations as $activation)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $activation->device_name ?? 'Unknown Device' }}</p>
                                    <p class="text-xs text-slate-400 font-mono">
                                        {{ Str::limit($activation->device_id, 24) }}</p>
                                    @if ($activation->is_suspicious)
                                        <p class="text-xs text-red-600 font-semibold mt-1">⚠️ Suspicious:
                                            {{ $activation->suspicious_reason }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm font-mono text-slate-700">
                                    {{ $activation->license?->license_key ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-600">
                                    {{ $activation->license?->customer?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-sm font-mono text-slate-600">
                                    {{ $activation->ip_address ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-slate-500">
                                    {{ $activation->created_at->format('Y-m-d') }}
                                    @if ($activation->status === 'deactivated' && $activation->deactivated_at)
                                        <p class="text-xs text-slate-400 mt-1">
                                            Deactivated: {{ $activation->deactivated_at->format('Y-m-d') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ match ($activation->status) {
                                        'active' => 'bg-green-50 text-green-700',
                                        'deactivated' => 'bg-amber-50 text-amber-700',
                                        'revoked' => 'bg-red-50 text-red-700',
                                        default => 'bg-slate-100 text-slate-600',
                                    } }}">
                                        {{ ucfirst($activation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($activation->status === 'active')
                                            <button wire:click="deactivate('{{ $activation->id }}')"
                                                wire:confirm="Deactivate this device? It can be reactivated later."
                                                class="text-xs px-2.5 py-1.5 font-medium text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded transition-colors">
                                                Deactivate
                                            </button>
                                        @endif

                                        @if ($activation->status === 'deactivated')
                                            @if ($activation->canReactivate())
                                                <button wire:click="reactivate({{ $activation->id }})"
                                                    wire:confirm="Reactivate this device?"
                                                    class="text-xs px-2.5 py-1.5 font-medium text-green-600 hover:text-green-700 hover:bg-green-50 rounded transition-colors">
                                                    Reactivate
                                                </button>
                                            @else
                                                <span class="text-xs px-2.5 py-1.5 text-slate-400"
                                                    title="{{ $activation->getReactivationBlockReason() }}">
                                                    Cannot reactivate
                                                </span>
                                            @endif
                                        @endif

                                        @if ($activation->status !== 'revoked')
                                            <button wire:click="revoke({{ $activation->id }})"
                                                wire:confirm="Permanently revoke this activation? This cannot be undone."
                                                class="text-xs px-2.5 py-1.5 font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors">
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
