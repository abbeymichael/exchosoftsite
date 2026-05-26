<?php

use App\Models\ApiRequestLog;
use App\Models\Customer;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\ValidationLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Dashboard — ExchoLicense')] class extends Component {

    // Refresh window for API stats (hours)
    public int $statsWindow = 24;

    // ──────────────────────────────────────────────────────────────────────────
    // Core stats
    // ──────────────────────────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        return [
            'total_products'    => Product::count(),
            'total_licenses'    => License::count(),
            'active_licenses'   => License::where('status', 'active')->count(),
            'total_customers'   => Customer::count(),
            'total_activations' => LicenseActivation::where('status', 'active')->count(),
            'expiring_soon'     => License::where('status', 'active')
                ->whereNotNull('expires_at')
                ->whereBetween('expires_at', [now(), now()->addDays(30)])
                ->count(),
            'active_subs'       => Subscription::where('status', 'active')->count(),
            'monthly_revenue'   => Subscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->sum('amount'),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // API analytics
    // ──────────────────────────────────────────────────────────────────────────

    #[Computed]
    public function apiStats(): array
    {
        $window = $this->statsWindow;

        $total   = ApiRequestLog::where('created_at', '>=', now()->subHours($window))->count();
        $success = ApiRequestLog::where('created_at', '>=', now()->subHours($window))->where('success', true)->count();
        $failed  = $total - $success;

        return [
            'total'        => $total,
            'success'      => $success,
            'failed'       => $failed,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 1) : 100.0,
        ];
    }

    #[Computed]
    public function endpointStats(): array
    {
        $window    = $this->statsWindow;
        $endpoints = [
            'validate'   => 'Validate',
            'status'     => 'Status',
            'renew'      => 'Renew',
            'deactivate' => 'Deactivate',
            'internal'   => 'Internal (Provisioning)',
        ];

        $rows = [];
        foreach ($endpoints as $key => $label) {
            $total   = ApiRequestLog::where('endpoint', 'like', $key . '%')
                ->where('created_at', '>=', now()->subHours($window))
                ->count();
            $success = ApiRequestLog::where('endpoint', 'like', $key . '%')
                ->where('success', true)
                ->where('created_at', '>=', now()->subHours($window))
                ->count();
            $failed  = $total - $success;

            $rows[] = [
                'key'     => $key,
                'label'   => $label,
                'total'   => $total,
                'success' => $success,
                'failed'  => $failed,
                'rate'    => $total > 0 ? round(($success / $total) * 100, 1) : 100.0,
            ];
        }

        // Sort by total desc
        usort($rows, fn ($a, $b) => $b['total'] - $a['total']);
        return $rows;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Validation log summary
    // ──────────────────────────────────────────────────────────────────────────

    #[Computed]
    public function validationStats(): array
    {
        $window = $this->statsWindow;
        $base   = ValidationLog::where('created_at', '>=', now()->subHours($window));

        return [
            'validate_total'   => (clone $base)->where('action', 'validate')->count(),
            'validate_success' => (clone $base)->where('action', 'validate')->where('success', true)->count(),
            'validate_failed'  => (clone $base)->where('action', 'validate')->where('success', false)->count(),

            'status_total'     => (clone $base)->where('action', 'status')->count(),
            'deactivate_total' => (clone $base)->where('action', 'deactivate')->count(),

            // Top failure reasons
            'failure_reasons'  => (clone $base)
                ->where('success', false)
                ->whereNotNull('failure_reason')
                ->select('failure_reason', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('failure_reason')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Recent activity
    // ──────────────────────────────────────────────────────────────────────────

    #[Computed]
    public function recentLicenses()
    {
        return License::with(['product', 'customer'])
            ->latest()
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function expiringLicenses()
    {
        return License::with(['product', 'customer'])
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(30)])
            ->orderBy('expires_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recentActivations()
    {
        return LicenseActivation::with(['license.product', 'license.customer'])
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recentValidationLogs()
    {
        return ValidationLog::with('license.product')
            ->latest('created_at')
            ->limit(8)
            ->get();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // System health indicators
    // ──────────────────────────────────────────────────────────────────────────

    #[Computed]
    public function systemHealth(): array
    {
        $suspiciousCount = LicenseActivation::where('is_suspicious', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $failedAttempts = ValidationLog::where('success', false)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        return [
            'suspicious_activations' => $suspiciousCount,
            'failed_last_hour'       => $failedAttempts,
            'db_ok'                  => true, // If we got this far, DB is up
        ];
    }
}; ?>

<div>
    <x-slot:heading>Dashboard</x-slot:heading>

    <div class="space-y-6">

        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Hero banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-cyan-600 to-violet-600 p-6 text-white shadow-lg">
            <div class="relative z-10">
                <p class="text-sm font-medium text-cyan-200">Welcome back,</p>
                <h2 class="mt-1 text-2xl font-bold">{{ auth()->user()->name ?? 'Admin' }}</h2>
                <p class="mt-1 text-sm text-cyan-200">ExchoLicense · Licensing & Activation Platform</p>
                <div class="mt-3 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-medium">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                        System Online
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-medium">
                        {{ number_format($this->apiStats['total']) }} API calls (24h)
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-medium">
                        {{ $this->apiStats['success_rate'] }}% success rate
                    </span>
                </div>
            </div>
            <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/5"></div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 rounded-full bg-white/5"></div>
        </div>

        {{-- Core stats grid --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-cyan-50 p-2.5 inline-block">
                    <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <p class="mt-4 text-2xl font-bold text-slate-900">{{ number_format($this->stats['total_licenses']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Total Licenses</p>
                <p class="mt-2 text-xs font-medium text-green-600">{{ $this->stats['active_licenses'] }} active</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-emerald-50 p-2.5 inline-block">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="mt-4 text-2xl font-bold text-slate-900">{{ number_format($this->stats['total_customers']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Customers</p>
                <p class="mt-2 text-xs font-medium text-emerald-600">{{ $this->stats['total_products'] }} products</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-blue-50 p-2.5 inline-block">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                    </svg>
                </div>
                <p class="mt-4 text-2xl font-bold text-slate-900">{{ number_format($this->stats['total_activations']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Active Devices</p>
                <p class="mt-2 text-xs font-medium text-blue-600">{{ $this->stats['active_subs'] }} subscriptions</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-amber-50 p-2.5 inline-block">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="mt-4 text-2xl font-bold text-slate-900">{{ number_format($this->stats['expiring_soon']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Expiring (30d)</p>
                <p class="mt-2 text-xs font-medium text-amber-600">GHS {{ number_format($this->stats['monthly_revenue'], 2) }} MRR</p>
            </div>

        </div>

        {{-- ── API Analytics ──────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">API Endpoint Analytics</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Request counts and success rates in the last {{ $statsWindow }} hours</p>
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="statsWindow"
                            class="rounded-lg border border-slate-200 text-xs px-2 py-1.5 text-slate-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100 outline-none">
                        <option value="1">Last 1h</option>
                        <option value="6">Last 6h</option>
                        <option value="24">Last 24h</option>
                        <option value="168">Last 7d</option>
                    </select>
                </div>
            </div>

            {{-- Summary bar --}}
            <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($this->apiStats['total']) }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">Total Requests</p>
                </div>
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($this->apiStats['success']) }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">Successful</p>
                </div>
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold {{ $this->apiStats['failed'] > 0 ? 'text-red-600' : 'text-slate-400' }}">
                        {{ number_format($this->apiStats['failed']) }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">Failed</p>
                </div>
            </div>

            {{-- Per-endpoint table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Endpoint</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Success</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Failed</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Success Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 w-40">Rate Bar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->endpointStats as $ep)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-mono font-semibold
                                             {{ str_starts_with($ep['key'], 'internal') ? 'bg-violet-50 text-violet-700' : 'bg-cyan-50 text-cyan-700' }}">
                                    /api/v1/{{ $ep['key'] === 'internal' ? 'internal/…' : 'licenses/' . $ep['key'] }}
                                </span>
                                <span class="ml-2 text-xs text-slate-400">{{ $ep['label'] }}</span>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-slate-900">{{ number_format($ep['total']) }}</td>
                            <td class="px-6 py-3 text-right text-green-600 font-medium">{{ number_format($ep['success']) }}</td>
                            <td class="px-6 py-3 text-right {{ $ep['failed'] > 0 ? 'text-red-600 font-medium' : 'text-slate-400' }}">{{ number_format($ep['failed']) }}</td>
                            <td class="px-6 py-3 text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                             {{ $ep['rate'] >= 95 ? 'bg-green-50 text-green-700' : ($ep['rate'] >= 80 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                    {{ $ep['rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($ep['total'] > 0)
                                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $ep['rate'] >= 95 ? 'bg-green-500' : ($ep['rate'] >= 80 ? 'bg-amber-500' : 'bg-red-500') }} transition-all"
                                         style="width: {{ $ep['rate'] }}%"></div>
                                </div>
                                @else
                                <span class="text-xs text-slate-300">No requests</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400">No API requests in the selected window.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Validation Status Summary ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Validation Stats --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Validation Status ({{ $statsWindow }}h)</h3>
                </div>
                <div class="px-6 py-5 space-y-3">
                    @php $vs = $this->validationStats; @endphp

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Validate calls</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($vs['validate_total']) }}</span>
                            @if($vs['validate_total'] > 0)
                            <span class="text-xs text-green-600">{{ number_format($vs['validate_success']) }} ✓</span>
                            <span class="text-xs text-red-500">{{ number_format($vs['validate_failed']) }} ✗</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Status checks</span>
                        <span class="text-sm font-semibold text-slate-900">{{ number_format($vs['status_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Deactivations</span>
                        <span class="text-sm font-semibold text-slate-900">{{ number_format($vs['deactivate_total']) }}</span>
                    </div>

                    @if(count($vs['failure_reasons']) > 0)
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Top Failure Reasons</p>
                        @foreach($vs['failure_reasons'] as $reason)
                        <div class="flex items-center justify-between py-1">
                            <span class="text-xs font-mono text-slate-600">{{ $reason['failure_reason'] }}</span>
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">
                                {{ $reason['count'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- System Health --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">System Health</h3>
                </div>
                <div class="px-6 py-5 space-y-3">
                    @php $health = $this->systemHealth; @endphp

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            <span class="text-sm text-slate-600">Database</span>
                        </div>
                        <span class="text-xs font-medium text-green-600">Online</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $health['failed_last_hour'] > 10 ? 'bg-red-500' : 'bg-green-500' }}"></span>
                            <span class="text-sm text-slate-600">Failed validations (1h)</span>
                        </div>
                        <span class="text-xs font-medium {{ $health['failed_last_hour'] > 10 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $health['failed_last_hour'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $health['suspicious_activations'] > 0 ? 'bg-amber-500' : 'bg-green-500' }}"></span>
                            <span class="text-sm text-slate-600">Suspicious activations (24h)</span>
                        </div>
                        <span class="text-xs font-medium {{ $health['suspicious_activations'] > 0 ? 'text-amber-600' : 'text-green-600' }}">
                            {{ $health['suspicious_activations'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            <span class="text-sm text-slate-600">API Success Rate ({{ $statsWindow }}h)</span>
                        </div>
                        <span class="text-xs font-medium {{ $this->apiStats['success_rate'] >= 95 ? 'text-green-600' : ($this->apiStats['success_rate'] >= 80 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $this->apiStats['success_rate'] }}%
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            <span class="text-sm text-slate-600">Licenses expiring (30d)</span>
                        </div>
                        <span class="text-xs font-medium text-blue-600">{{ $this->stats['expiring_soon'] }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Recent Activity ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Recent Licenses --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Recent Licenses</h3>
                    <a href="{{ route('admin.licenses') }}" wire:navigate class="text-xs font-medium text-cyan-600 hover:text-cyan-700">View all →</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($this->recentLicenses as $license)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-mono font-medium text-slate-900">{{ $license->license_key }}</p>
                                <p class="text-xs text-slate-500">{{ $license->customer?->name ?? '—' }} · {{ $license->product?->name ?? '—' }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ match($license->status) {
                                    'active'  => 'bg-green-50 text-green-700',
                                    'expired' => 'bg-red-50 text-red-700',
                                    'revoked' => 'bg-slate-100 text-slate-600',
                                    default   => 'bg-slate-100 text-slate-600',
                                } }}">
                                {{ ucfirst($license->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-slate-400 text-center">No licenses yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Validation Logs --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Recent API Requests</h3>
                    <a href="{{ route('admin.audit-logs') }}" wire:navigate class="text-xs font-medium text-cyan-600 hover:text-cyan-700">View logs →</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($this->recentValidationLogs as $log)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-mono
                                                 {{ $log->success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                        {{ strtoupper($log->action) }}
                                    </span>
                                    <p class="text-xs font-mono text-slate-600 truncate">{{ $log->license_key ?? '—' }}</p>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    {{ $log->ip_address }} · {{ $log->platform ?? 'unknown' }}
                                    @if($log->failure_reason)
                                        · <span class="text-red-500">{{ $log->failure_reason }}</span>
                                    @endif
                                </p>
                            </div>
                            <p class="ml-3 text-xs text-slate-400 flex-shrink-0">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-slate-400 text-center">No validation logs yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Recent Activations --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Recent Device Activations</h3>
                <a href="{{ route('admin.activations') }}" wire:navigate class="text-xs font-medium text-cyan-600 hover:text-cyan-700">View all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($this->recentActivations as $activation)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="flex items-center gap-3">
                            {{-- App type icon --}}
                            <div class="flex-shrink-0">
                                @php $appType = $activation->app_type ?? 'desktop'; @endphp
                                @if($appType === 'web')
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-xs font-bold">W</span>
                                @elseif($appType === 'cloud')
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-purple-50 text-purple-600 text-xs font-bold">C</span>
                                @elseif($appType === 'hybrid')
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600 text-xs font-bold">H</span>
                                @else
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-500 text-xs font-bold">D</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $activation->device_name ?? $activation->device_id }}</p>
                                <p class="text-xs text-slate-500">{{ $activation->license?->customer?->name ?? '—' }} · {{ $activation->ip_address }} · {{ ucfirst($appType) }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ match($activation->status) {
                                'active'      => 'bg-green-50 text-green-700',
                                'deactivated' => 'bg-slate-100 text-slate-600',
                                'revoked'     => 'bg-red-50 text-red-700',
                                default       => 'bg-slate-100 text-slate-600',
                            } }}">
                            {{ ucfirst($activation->status) }}
                        </span>
                    </div>
                @empty
                    <p class="px-6 py-6 text-sm text-slate-400 text-center">No activations yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Expiring Soon --}}
        @if($this->expiringLicenses->isNotEmpty())
        <div class="rounded-2xl bg-amber-50 border border-amber-200 overflow-hidden">
            <div class="flex items-center gap-3 border-b border-amber-200 px-6 py-4">
                <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-sm font-semibold text-amber-800">Licenses expiring in the next 30 days</h3>
            </div>
            <div class="divide-y divide-amber-100">
                @foreach($this->expiringLicenses as $license)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-mono font-medium text-amber-900">{{ $license->license_key }}</p>
                            <p class="text-xs text-amber-700">{{ $license->customer?->name ?? '—' }} · {{ $license->product?->name ?? '—' }}</p>
                        </div>
                        <p class="text-sm font-semibold text-amber-800">{{ $license->expires_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>
