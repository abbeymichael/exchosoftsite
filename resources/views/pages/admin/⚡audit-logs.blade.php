<?php

use App\Models\AuditLog;
use App\Models\ValidationLog;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Audit Logs — ExchoLicense')] class extends Component
{
    use WithPagination;

    public string $tab          = 'audit';
    public string $search       = '';
    public string $filterEvent  = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingTab(): void    { $this->resetPage(); }

    #[Computed]
    public function auditLogs()
    {
        return AuditLog::query()
            ->with('user')
            ->when($this->search, fn ($q) => $q->where('event', 'like', "%{$this->search}%")
                ->orWhere('ip_address', 'like', "%{$this->search}%")
                ->orWhere('actor_label', 'like', "%{$this->search}%"))
            ->when($this->filterEvent, fn ($q) => $q->where('event', 'like', "%{$this->filterEvent}%"))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function validationLogs()
    {
        return ValidationLog::query()
            ->with('license')
            ->when($this->search, fn ($q) => $q->where('license_key', 'like', "%{$this->search}%")
                ->orWhere('ip_address', 'like', "%{$this->search}%")
                ->orWhere('device_id', 'like', "%{$this->search}%"))
            ->when($this->filterEvent === 'failed', fn ($q) => $q->where('success', false))
            ->when($this->filterEvent === 'success', fn ($q) => $q->where('success', true))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_audit'         => AuditLog::count(),
            'total_validations'   => ValidationLog::count(),
            'failed_validations'  => ValidationLog::where('success', false)->count(),
            'security_events'     => AuditLog::where('event', 'like', 'security.%')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>Audit & Validation Logs</x-slot:heading>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Audit Events',       'value' => $this->stats['total_audit'],        'color' => 'cyan'],
                ['label' => 'Validation Attempts', 'value' => $this->stats['total_validations'],  'color' => 'blue'],
                ['label' => 'Failed Validations',  'value' => $this->stats['failed_validations'], 'color' => 'red'],
                ['label' => 'Security Events',     'value' => $this->stats['security_events'],    'color' => 'amber'],
            ] as $stat)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 rounded-xl bg-slate-100 p-1 w-fit">
            <button wire:click="$set('tab', 'audit')"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all
                           {{ $tab === 'audit' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                Audit Log
            </button>
            <button wire:click="$set('tab', 'validation')"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all
                           {{ $tab === 'validation' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                Validation Log
            </button>
        </div>

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 flex-wrap">
            <input type="text"
                   wire:model.live="search"
                   placeholder="{{ $tab === 'audit' ? 'Search event, IP, actor…' : 'Search license key, IP, device…' }}"
                   class="w-full sm:w-72 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">

            @if($tab === 'audit')
                <select wire:model.live="filterEvent"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Events</option>
                    <option value="license.">License Events</option>
                    <option value="batch.">Batch Events</option>
                    <option value="security.">Security Events</option>
                </select>
            @else
                <select wire:model.live="filterEvent"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Results</option>
                    <option value="success">Successful Only</option>
                    <option value="failed">Failed Only</option>
                </select>
            @endif
        </div>

        {{-- Audit Log Table --}}
        @if($tab === 'audit')
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Actor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">IP</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">When</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($this->auditLogs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                                            {{ str_starts_with($log->event, 'security.') ? 'bg-red-50 text-red-700' :
                                               (str_starts_with($log->event, 'batch.') ? 'bg-violet-50 text-violet-700' : 'bg-cyan-50 text-cyan-700') }}">
                                            {{ $log->event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-slate-600">
                                        {{ $log->user?->name ?? $log->actor_label ?? 'System' }}
                                        <span class="text-xs text-slate-400 block">{{ $log->actor_type }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-slate-500">
                                        @if($log->auditable_type)
                                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm font-mono text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-500">
                                        {{ $log->created_at->diffForHumans() }}
                                        <span class="text-xs text-slate-400 block">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">No audit events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-center">{{ $this->auditLogs->links() }}</div>
        @endif

        {{-- Validation Log Table --}}
        @if($tab === 'validation')
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">License Key</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Result</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Device / Platform</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">IP</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">When</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($this->validationLogs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3 text-sm font-mono text-slate-700">{{ $log->license_key ?? '—' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-xs font-medium text-slate-600 uppercase">{{ $log->action }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($log->success)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700">✓ Success</span>
                                        @else
                                            <div>
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-50 text-red-700">✗ Failed</span>
                                                @if($log->failure_reason)
                                                    <p class="text-xs text-slate-400 mt-0.5">{{ str_replace('_', ' ', $log->failure_reason) }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-slate-500">
                                        {{ $log->device_id ? substr($log->device_id, 0, 16) . '…' : '—' }}
                                        <span class="text-xs text-slate-400 block">{{ $log->platform ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm font-mono text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-500">
                                        {{ $log->created_at->diffForHumans() }}
                                        <span class="text-xs text-slate-400 block">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">No validation logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-center">{{ $this->validationLogs->links() }}</div>
        @endif

    </div>
</div>
