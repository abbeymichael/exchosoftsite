<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('API Documentation — ExchoLicense')] class extends Component {
    public string $activeSection = 'overview';
    public string $baseUrl       = '';

    public function mount(): void
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
    }
}; ?>

<div>
    <x-slot:heading>API Documentation</x-slot:heading>

    <div class="flex gap-6 items-start" x-data="{ section: 'overview' }">

        {{-- ─── Sidebar nav ─────────────────────────────────────────────── --}}
        <aside class="hidden lg:block w-64 flex-shrink-0 sticky top-20 max-h-[calc(100vh-5rem)] overflow-y-auto pr-1">
            <nav class="space-y-0.5 text-sm pb-6">

                @php
                    $nav = [
                        'overview'       => ['Overview',            'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'auth'           => ['Authentication',      'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                        'offline'        => ['Offline Validation',  'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        'validate'       => ['Validate / Activate', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0'],
                        'status'         => ['Check Status',        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        'deactivate'     => ['Deactivate Device',   'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                        'renew'          => ['Renew License',       'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        'create'         => ['Create License',      'M12 4v16m8-8H4'],
                        'bulk'           => ['Bulk Create',         'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        'trial'          => ['Create Trial',        'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0'],
                        'extend'         => ['Extend License',      'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        'revoke'         => ['Revoke License',      'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                        'suspend'        => ['Suspend / Resume',    'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0'],
                        'reset-devices'  => ['Reset Devices',       'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        'regenerate'     => ['Regenerate Key',      'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                        'notes'          => ['Attach Notes',        'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        'lookup'         => ['Look Up License',     'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0'],
                        'responses'      => ['Response Schema',     'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                        'errors'         => ['Error Codes',         'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        'workflow'       => ['License Workflow',    'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2'],
                    ];
                    $groups = [
                        'Overview'       => ['overview', 'auth', 'offline', 'workflow'],
                        'Public Endpoints' => ['validate', 'status', 'deactivate', 'renew'],
                        'Provisioning API' => ['create', 'bulk', 'trial', 'extend', 'revoke', 'suspend', 'reset-devices', 'regenerate', 'notes', 'lookup'],
                        'Reference'      => ['responses', 'errors'],
                    ];
                @endphp

                @foreach ($groups as $groupLabel => $keys)
                    <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-slate-400">
                        {{ $groupLabel }}
                    </p>
                    @foreach ($keys as $key)
                        <button @click="section = '{{ $key }}'"
                            :class="section === '{{ $key }}' ? 'bg-cyan-50 text-cyan-700 font-semibold border-l-2 border-cyan-500' :
                                'text-slate-600 hover:bg-slate-50 border-l-2 border-transparent'"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-r-lg text-left transition-all text-xs">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nav[$key][1] }}" />
                            </svg>
                            {{ $nav[$key][0] }}
                        </button>
                    @endforeach
                @endforeach
            </nav>
        </aside>

        {{-- ─── Main content ─────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-1">

            @php
                $base = $baseUrl;
                $pub  = $base . '/api/v1/licenses';
                $int  = $base . '/api/v1/internal/licenses';

                // Code block helper colours
                $k  = 'text-green-400';   // JSON key
                $s  = 'text-amber-300';   // string value
                $b  = 'text-blue-400';    // bool / null
                $n  = 'text-purple-400';  // number
                $c  = 'text-slate-500';   // comment
            @endphp

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- OVERVIEW                                                     --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'overview'" x-cloak class="space-y-5">

                <div class="rounded-2xl bg-gradient-to-br from-cyan-600 to-violet-600 p-6 text-white">
                    <div class="flex items-center gap-3 mb-3">
                        @if(file_exists(public_path('assets/images/logo.png')) && filesize(public_path('assets/images/logo.png')) > 0)
                        <img src="{{ asset('assets/images/logo.png') }}" alt="ExchoLicense" class="h-8 w-auto">
                        @endif
                        <div>
                            <h2 class="text-xl font-bold">ExchoLicense API</h2>
                            <p class="text-cyan-200 text-sm">Version 1 &mdash; REST / JSON &mdash; TLS Required</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 font-mono text-sm backdrop-blur">
                        <span class="text-cyan-200">Base URL</span>
                        <span class="text-white font-semibold ml-2">{{ $base }}/api/v1</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Public --}}
                    <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-green-100">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                </svg>
                            </span>
                            <h3 class="font-semibold text-slate-900">Public Endpoints</h3>
                        </div>
                        <p class="text-xs text-slate-600 mb-3">No authentication required. Called by your client app. Rate limited to <strong>60 req/min</strong>.</p>
                        <ul class="space-y-1 text-xs text-slate-600">
                            @foreach([
                                ['POST', 'bg-blue-100 text-blue-700',    '/licenses/validate',   'Validate & activate'],
                                ['GET',  'bg-emerald-100 text-emerald-700', '/licenses/status',  'Read-only status check'],
                                ['POST', 'bg-blue-100 text-blue-700',    '/licenses/deactivate', 'Release a device slot'],
                                ['POST', 'bg-blue-100 text-blue-700',    '/licenses/renew',      'Request renewal'],
                            ] as [$m, $cls, $path, $desc])
                            <li class="flex items-center gap-2">
                                <span class="inline-block w-11 text-center rounded text-[10px] font-bold py-0.5 {{ $cls }}">{{ $m }}</span>
                                <code class="text-slate-700">{{ $path }}</code>
                                <span class="text-slate-400">— {{ $desc }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Internal --}}
                    <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100">
                                <svg class="h-4 w-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <h3 class="font-semibold text-slate-900">Provisioning API</h3>
                        </div>
                        <p class="text-xs text-slate-600 mb-3">Requires Bearer token. Used by your store, backend, or scripts. Rate limited to <strong>120 req/min</strong>.</p>
                        <ul class="space-y-1 text-xs text-slate-600">
                            @foreach([
                                ['POST', '/internal/licenses/create'],
                                ['POST', '/internal/licenses/bulk-create'],
                                ['POST', '/internal/licenses/create-trial'],
                                ['POST', '/internal/licenses/extend'],
                                ['POST', '/internal/licenses/revoke'],
                                ['POST', '/internal/licenses/suspend & /unsuspend'],
                                ['POST', '/internal/licenses/reset-devices'],
                                ['POST', '/internal/licenses/regenerate-key'],
                                ['POST', '/internal/licenses/attach-notes'],
                                ['GET',  '/internal/licenses/{key}'],
                            ] as [$m, $path])
                            <li class="flex items-center gap-2">
                                <span class="inline-block w-11 text-center rounded text-[10px] font-bold py-0.5 {{ $m === 'GET' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">{{ $m }}</span>
                                <code class="text-slate-700 text-[10px]">{{ $path }}</code>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Common headers card --}}
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-900 mb-3">Common Request Headers</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Header</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Value</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Endpoints</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['Content-Type',  'application/json',              'All POST requests'],
                                    ['Accept',        'application/json',              'All requests'],
                                    ['Authorization', 'Bearer {your_api_token}',       'Internal Provisioning API only'],
                                    ['X-Request-ID',  '{uuid}',                        'Optional — for request tracing'],
                                ] as [$h, $v, $ep])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-slate-900">{{ $h }}</td>
                                    <td class="px-4 py-2.5 font-mono text-cyan-600">{{ $v }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $ep }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- AUTHENTICATION                                               --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'auth'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-1">Authentication</h2>
                    <p class="text-sm text-slate-600 mb-5">The Internal Provisioning API uses <strong>Bearer token</strong> authentication (Laravel Sanctum). Public license endpoints require no authentication.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers — Internal API</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        <span class="{{ $c }}"># Required headers for every Provisioning API call</span><br>
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN_HERE</span>
                    </div>

                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex gap-3 mb-5">
                        <svg class="h-5 w-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="text-sm text-amber-800">
                            <strong>Keep tokens secret.</strong> Manage tokens in
                            <a href="{{ route('admin.api-tokens') }}" wire:navigate class="underline font-semibold">API Tokens</a>.
                            Each token is shown only once. Tokens can be scoped and revoked at any time.
                        </div>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">401 Unauthorized Response</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">false</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Unauthenticated."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span><br>
                        }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- OFFLINE VALIDATION                                           --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'offline'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-100">
                            <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Offline License Validation</h2>
                            <p class="text-xs text-slate-500">Architecture & Implementation Guide</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-violet-50 border border-violet-200 px-4 py-3 mb-5 text-sm text-violet-900">
                        <strong>Design principle:</strong> The first validation call is always online. All subsequent launches can be offline. The signed payload is the offline proof.
                    </div>

                    {{-- How it works --}}
                    <h3 class="font-semibold text-slate-900 mb-3 text-sm">How Offline Validation Works</h3>
                    <div class="space-y-3 mb-6">
                        @foreach([
                            ['1', 'cyan', 'First Launch (Online Required)', 'App calls POST /api/v1/licenses/validate with license_key + device_id + hardware_id. Server validates, activates the device, and returns a cryptographically signed payload.'],
                            ['2', 'blue',   'Store Signed Payload Locally',    'App encrypts and stores the entire license object (payload + signature) on disk. Use AES-256 with a key derived from the hardware_id. Never store in plain text.'],
                            ['3', 'green',  'Subsequent Launches (Offline OK)',  'App loads the local license file, decrypts it, verifies the HMAC signature, and checks offline_valid_until. If all pass — grant access. No internet required.'],
                            ['4', 'amber',  'Periodic Online Check-in',          'When internet is available, call validate again. Server refreshes offline_valid_until and issues a new signed payload. Client replaces the stored file.'],
                            ['5', 'red',    'Offline Window Expires',             'If offline_valid_until passes and the app cannot reach the server, deny access and prompt the user to connect. The offline_ttl_hours product setting controls this window.'],
                        ] as [$step, $color, $title, $desc])
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 font-bold text-xs mt-0.5">{{ $step }}</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $desc }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Key fields --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Offline-Relevant Payload Fields</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Client Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['offline_valid_until',  'ISO 8601 timestamp. Offline validity window end.',   'Deny access after this time if no online check-in'],
                                    ['offline_ttl_hours',    'Hours the offline window spans (e.g. 168 = 7 days).','Inform UX — show "X days remaining offline"'],
                                    ['signature',            'HMAC-SHA256 of the payload, base64-encoded.',        'Verify on every launch — reject if tampered'],
                                    ['revocation_checksum',  'SHA-256 hash of license status + timestamps.',       'Compare against CRL on next online check-in'],
                                    ['response_nonce',       'Server-generated nonce. Submit as next request nonce.','Prevents replay of stored offline responses'],
                                    ['expires_at',           'License expiry date. null means lifetime.',           'Enforce even offline — reject if past this date'],
                                    ['features',             'Array of feature flags granted to this license.',    'Gate app features client-side from this list'],
                                ] as [$f, $d, $a])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-violet-700 font-semibold">{{ $f }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                    <td class="px-4 py-2.5 text-slate-500 italic">{{ $a }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Signature verification --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Client-Side Signature Verification</h3>
                    <p class="text-xs text-slate-600 mb-2">The signing key is your <strong>product's secret_key</strong> (or app key as fallback). Embed it at build time (obfuscated). Never transmit it over the network.</p>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        <span class="{{ $c }}">// Python pseudocode — adapt to your language</span><br>
                        <span class="{{ $k }}">import</span> hmac, hashlib, base64, json<br><br>
                        <span class="{{ $k }}">def</span> verify_license(stored_payload, stored_signature, secret_key):<br>
                        &nbsp;&nbsp;<span class="{{ $c }}">  # Re-encode payload exactly as server did</span><br>
                        &nbsp;&nbsp;payload_json = json.dumps(stored_payload, separators=(<span class="{{ $s }}">','</span>, <span class="{{ $s }}">':'</span>))<br>
                        &nbsp;&nbsp;expected_sig = base64.b64encode(<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;hmac.new(secret_key.encode(), payload_json.encode(), hashlib.sha256).digest()<br>
                        &nbsp;&nbsp;).decode()<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">return</span> hmac.compare_digest(expected_sig, stored_signature)
                    </div>

                    {{-- CRL --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Revocation Checking (Planned)</h3>
                    <p class="text-xs text-slate-600">When online, the client should check the <code class="bg-slate-100 px-1 rounded">revocation_checksum</code> against a lightweight CRL (Certificate Revocation List) endpoint. If the checksum no longer matches, the license was revoked or suspended server-side — the client must invalidate the local file and refuse access until re-validated.</p>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- VALIDATE / ACTIVATE                                          --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'validate'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">

                    {{-- Endpoint header --}}
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $pub }}/validate</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Public — No Auth</span>
                    </div>

                    <p class="text-sm text-slate-600 mb-2">
                        Validates a license key and activates the calling device.
                        <strong>Idempotent:</strong> calling this multiple times with the same <code class="bg-slate-100 px-1 rounded text-xs">license_key</code> + <code class="bg-slate-100 px-1 rounded text-xs">device_id</code> pair never creates duplicate activations, never changes <code class="bg-slate-100 px-1 rounded text-xs">activated_at</code>, and never changes the expiry date.
                        Only <code class="bg-slate-100 px-1 rounded text-xs">last_seen_at</code> and <code class="bg-slate-100 px-1 rounded text-xs">app_version</code> are refreshed on subsequent calls.
                    </p>

                    {{-- Headers --}}
                    <h3 class="font-semibold text-slate-900 mt-5 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span>
                    </div>

                    {{-- Request body table --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['license_key', 'string',  true,  'The license key. Format: XXXX-XXXX-XXXX-XXXX'],
                                    ['device_id',   'string',  true,  'Stable unique identifier for this device (UUID, hardware hash, etc.)'],
                                    ['product',     'string',  false, 'Product slug, code, or app_identifier. Recommended for security.'],
                                    ['device_name', 'string',  false, 'Human-readable label shown in Activations dashboard'],
                                    ['hardware_id', 'string',  false, 'Hardware fingerprint (MAC, motherboard serial, etc.) for enhanced security'],
                                    ['platform',    'string',  false, 'Platform: windows | macos | linux | web | android | ios'],
                                    ['os',          'string',  false, 'OS name + version, e.g. "Windows 11 23H2"'],
                                    ['app_version', 'string',  false, 'Current app version, e.g. "2.4.1". Used for version gating.'],
                                    ['timestamp',   'integer', false, 'Unix timestamp (seconds). Enables replay-attack prevention.'],
                                    ['nonce',       'string',  false, 'Unique request nonce (max 64 chars). Use response_nonce from prior response.'],
                                ] as [$f, $t, $req, $d])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-slate-900">{{ $f }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $t }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($req)
                                            <span class="inline-block rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span>
                                        @else
                                            <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Example request --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        <span class="{{ $c }}">curl -X POST {{ $pub }}/validate \</span><br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Accept: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'</span>{<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"device_id"</span>: <span class="{{ $s }}">"550e8400-e29b-41d4-a716-446655440000"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"product"</span>: <span class="{{ $s }}">"coreops"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"device_name"</span>: <span class="{{ $s }}">"John's MacBook Pro"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"hardware_id"</span>: <span class="{{ $s }}">"a3f2b1c9d8e7f6a5b4c3d2e1f0a9b8c7"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"platform"</span>: <span class="{{ $s }}">"macos"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"os"</span>: <span class="{{ $s }}">"macOS 14.5"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"app_version"</span>: <span class="{{ $s }}">"2.4.1"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $n }}">1748000000</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"nonce"</span>: <span class="{{ $s }}">"abc123xyz"</span><br>
                        &nbsp;&nbsp;}<span class="{{ $s }}">'"</span>
                    </div>

                    {{-- Success response --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">true</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License activated successfully on this device."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"payload"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_id"</span>: <span class="{{ $s }}">"550e8400-e29b-41d4-a716-446655440000"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"product_id"</span>: <span class="{{ $s }}">"prod-uuid-here"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"product"</span>: <span class="{{ $s }}">"COREOPS"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"edition"</span>: <span class="{{ $s }}">"professional"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"type"</span>: <span class="{{ $s }}">"annual"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"status"</span>: <span class="{{ $s }}">"active"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"expires_at"</span>: <span class="{{ $s }}">"2027-05-22"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"device_id"</span>: <span class="{{ $s }}">"550e8400-e29b-41d4-a716-446655440000"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"activated_at"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,  <span class="{{ $c }}">// NEVER changes</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"issued_at"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_devices"</span>: <span class="{{ $n }}">3</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"activations_used"</span>: <span class="{{ $n }}">1</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"is_new_activation"</span>: <span class="{{ $b }}">true</span>,  <span class="{{ $c }}">// false on repeat calls</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"support_tier"</span>: <span class="{{ $s }}">"standard"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"features"</span>: [<span class="{{ $s }}">"reports"</span>, <span class="{{ $s }}">"api_access"</span>, <span class="{{ $s }}">"multi_branch"</span>],<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"min_app_version"</span>: <span class="{{ $s }}">"2.0.0"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_app_version"</span>: <span class="{{ $b }}">null</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"grace_period_days"</span>: <span class="{{ $n }}">7</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"offline_valid_until"</span>: <span class="{{ $s }}">"2026-05-29T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"offline_ttl_hours"</span>: <span class="{{ $n }}">168</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"response_nonce"</span>: <span class="{{ $s }}">"Kp3mNxQv7wYzLjRsTbUeHaFo"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"revocation_checksum"</span>: <span class="{{ $s }}">"sha256hex..."</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"validation_source"</span>: <span class="{{ $s }}">"online"</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;},<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"signature"</span>: <span class="{{ $s }}">"base64-encoded-hmac-sha256-signature"</span><br>
                        &nbsp;&nbsp;}<br>
                        }
                    </div>

                    {{-- Error responses --}}
                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Error Responses</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">HTTP</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">error_code</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Cause</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['422', 'validation_failed',        'Missing required fields'],
                                    ['403', 'timestamp_skew',           'Request timestamp > 5 min skew'],
                                    ['403', 'replay_attack',            'Nonce was already used'],
                                    ['404', 'license_not_found',        'Key does not exist or product mismatch'],
                                    ['403', 'license_revoked',          'License permanently revoked'],
                                    ['403', 'license_suspended',        'License temporarily suspended'],
                                    ['403', 'license_expired',          'Past expiry + grace period'],
                                    ['403', 'version_not_allowed',      'app_version outside allowed range'],
                                    ['403', 'activation_limit_reached', 'All device slots are occupied'],
                                ] as [$code, $ec, $cause])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono font-bold text-red-700">{{ $code }}</td>
                                    <td class="px-4 py-2.5 font-mono text-slate-700">{{ $ec }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $cause }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- STATUS                                                       --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'status'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">GET</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $pub }}/status</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Public — No Auth</span>
                    </div>

                    <p class="text-sm text-slate-600 mb-5">Read-only status check. <strong>No device is activated.</strong> Safe to call any number of times — no side effects. Use this to show license info to users without triggering activation.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Query Parameters</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Parameter</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">license_key</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">The license key to look up</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">product</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">Product slug or code to scope the lookup</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        <span class="{{ $c }}">curl -G {{ $pub }}/status \</span><br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Accept: application/json"</span> \<br>
                        &nbsp;&nbsp;--data-urlencode <span class="{{ $s }}">"license_key=EXCL-A1B2-C3D4-E5F6"</span> \<br>
                        &nbsp;&nbsp;--data-urlencode <span class="{{ $s }}">"product=coreops"</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">true</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License is valid."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"payload"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_id"</span>: <span class="{{ $s }}">"uuid"</span>, &nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-..."</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"edition"</span>: <span class="{{ $s }}">"professional"</span>, &nbsp;<span class="{{ $k }}">"type"</span>: <span class="{{ $s }}">"annual"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"status"</span>: <span class="{{ $s }}">"active"</span>, &nbsp;<span class="{{ $k }}">"expires_at"</span>: <span class="{{ $s }}">"2027-05-22"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_activations"</span>: <span class="{{ $n }}">3</span>, &nbsp;<span class="{{ $k }}">"current_activations"</span>: <span class="{{ $n }}">1</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"in_grace_period"</span>: <span class="{{ $b }}">false</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"features"</span>: [<span class="{{ $s }}">"reports"</span>, <span class="{{ $s }}">"api_access"</span>],<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"revocation_checksum"</span>: <span class="{{ $s }}">"sha256hex..."</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"validation_source"</span>: <span class="{{ $s }}">"online"</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                        &nbsp;&nbsp;}<br>
                        }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- DEACTIVATE                                                   --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'deactivate'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $pub }}/deactivate</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Public — No Auth</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Deactivates a specific device, freeing its activation slot. The historical activation record is preserved for audit. The device may be re-activated later.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">license_key</td>
                                    <td class="px-4 py-2.5 text-slate-500">string</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">The license key</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">device_id</td>
                                    <td class="px-4 py-2.5 text-slate-500">string</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">The device ID to deactivate</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        curl -X POST <span class="{{ $s }}">{{ $pub }}/deactivate</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Accept: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"license_key":"EXCL-A1B2-C3D4-E5F6","device_id":"550e8400-..."}'</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">true</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Device deactivated successfully. The activation slot has been freed."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"payload"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_id"</span>: <span class="{{ $s }}">"uuid"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"activation_id"</span>: <span class="{{ $s }}">"activation-uuid"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"device_id"</span>: <span class="{{ $s }}">"550e8400-..."</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"deactivated_at"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"current_activations"</span>: <span class="{{ $n }}">0</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_activations"</span>: <span class="{{ $n }}">3</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                        &nbsp;&nbsp;}<br>
                        }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- RENEW                                                        --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'renew'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $pub }}/renew</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Public — No Auth</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Initiates a renewal request. Does <strong>not</strong> extend the expiry directly — actual extension is done by the Provisioning API after payment confirmation.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">license_key</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">The license key to renew</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">product</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">Product slug or code</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">true</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Renewal request received. Please complete payment to activate the extension."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license"</span>: { <span class="{{ $k }}">"payload"</span>: { <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-..."</span>, <span class="{{ $k }}">"expires_at"</span>: <span class="{{ $s }}">"2026-05-22"</span> ... } }<br>
                        }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- CREATE LICENSE                                               --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'create'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/create</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Create a single license for a customer. Customer is auto-created from email if not found. License starts as <strong>inactive</strong> — it activates on first device validation.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['product_id',      'integer', true,  'Product ID'],
                                    ['edition',         'string',  true,  'standard | professional | enterprise'],
                                    ['license_type',    'string',  true,  'lifetime | annual | monthly | trial'],
                                    ['max_activations', 'integer', false, 'Max device slots. Default: 1'],
                                    ['duration_days',   'integer', false, 'License duration. Omit for lifetime.'],
                                    ['expires_at',      'string',  false, 'Explicit expiry date YYYY-MM-DD (overrides duration_days)'],
                                    ['customer_email',  'string',  false, 'Customer email — auto-creates customer'],
                                    ['customer_id',     'integer', false, 'Existing customer ID (alternative to email)'],
                                    ['customer_name',   'string',  false, 'Customer name if creating new customer'],
                                    ['key_prefix',      'string',  false, 'Custom key prefix, e.g. "CORP". Default: "EXCL"'],
                                    ['features',        'array',   false, 'Feature flag array, e.g. ["reports","api_access"]'],
                                    ['support_tier',    'string',  false, 'basic | standard | premium | enterprise'],
                                    ['grace_period_days','integer',false, 'Days after expiry during which license still validates'],
                                    ['notes',           'string',  false, 'Internal notes. Max 2000 chars.'],
                                    ['order_id',        'string',  false, 'Your e-commerce order reference'],
                                    ['is_renewable',    'boolean', false, 'Whether customer can request renewal. Default: true'],
                                ] as [$f, $t, $req, $d])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-slate-900">{{ $f }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $t }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($req)
                                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        curl -X POST <span class="{{ $s }}">{{ $int }}/create</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Authorization: Bearer YOUR_TOKEN"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Accept: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"product_id"</span>: <span class="{{ $n }}">1</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"edition"</span>: <span class="{{ $s }}">"professional"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"license_type"</span>: <span class="{{ $s }}">"annual"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_activations"</span>: <span class="{{ $n }}">3</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"duration_days"</span>: <span class="{{ $n }}">365</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"customer_email"</span>: <span class="{{ $s }}">"john@example.com"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"customer_name"</span>: <span class="{{ $s }}">"John Doe"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"features"</span>: [<span class="{{ $s }}">"reports"</span>, <span class="{{ $s }}">"api_access"</span>],<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"order_id"</span>: <span class="{{ $s }}">"ORD-2026-001"</span><br>
                        &nbsp;&nbsp;<span class="{{ $s }}">'}</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 201</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License created successfully."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"data"</span>: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"id"</span>: <span class="{{ $s }}">"uuid"</span>, &nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"edition"</span>: <span class="{{ $s }}">"professional"</span>, &nbsp;<span class="{{ $k }}">"type"</span>: <span class="{{ $s }}">"annual"</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"status"</span>: <span class="{{ $s }}">"inactive"</span>, &nbsp;<span class="{{ $k }}">"expires_at"</span>: <span class="{{ $b }}">null</span>,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"max_activations"</span>: <span class="{{ $n }}">3</span>, &nbsp;<span class="{{ $k }}">"features"</span>: [<span class="{{ $s }}">"reports"</span>]<br>
                        &nbsp;&nbsp;}<br>
                        }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- BULK CREATE                                                  --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'bulk'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/bulk-create</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Generate a batch of license keys (max 1,000 per call). Keys are inactive — activation dates and expiry are set on first device validation.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['product_id',    'integer', true,  'Product ID'],
                                    ['quantity',      'integer', true,  'Number of keys to generate (1–1000)'],
                                    ['label',         'string',  true,  'Batch name, e.g. "Black Friday 2026"'],
                                    ['edition',       'string',  false, 'standard | professional | enterprise. Default: standard'],
                                    ['license_type',  'string',  false, 'lifetime | annual | monthly. Default: lifetime'],
                                    ['duration_days', 'integer', false, 'License duration. Omit for lifetime.'],
                                    ['max_activations','integer',false, 'Max devices per key. Default: 1'],
                                    ['key_prefix',    'string',  false, 'Key prefix, e.g. "BF26"'],
                                    ['features',      'array',   false, 'Feature flags applied to all keys in batch'],
                                ] as [$f, $t, $req, $d])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">{{ $f }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $t }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($req)
                                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example — 500 retail keys</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        curl -X POST <span class="{{ $s }}">{{ $int }}/bulk-create</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Authorization: Bearer YOUR_TOKEN"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"product_id":1,"label":"Black Friday 2026","quantity":500,"edition":"standard","license_type":"annual","duration_days":365,"key_prefix":"BF26"}'</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- CREATE TRIAL                                                 --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'trial'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/create-trial</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Create a time-limited trial license. Default: 14 days. Call this when a new user signs up.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['product_id',     true,  'Product ID'],
                                    ['customer_email', false, 'Auto-creates customer if new'],
                                    ['trial_days',     false, 'Duration in days. Default: 14'],
                                    ['edition',        false, 'standard | professional | enterprise'],
                                    ['features',       false, 'Feature flags to enable during trial'],
                                ] as [$f, $req, $d])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">{{ $f }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($req)
                                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        curl -X POST <span class="{{ $s }}">{{ $int }}/create-trial</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Authorization: Bearer YOUR_TOKEN"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"product_id":1,"customer_email":"new@example.com","trial_days":30,"edition":"professional"}'</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- EXTEND                                                       --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'extend'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/extend</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Extend the expiry of an existing license. Pass <code class="bg-slate-100 px-1 rounded text-xs">days</code> to add onto current expiry, or <code class="bg-slate-100 px-1 rounded text-xs">expires_at</code> to set an exact date.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">license_key</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">License key to extend</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">days</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700">Required*</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">Days to add to current expiry</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono">expires_at</td>
                                    <td class="px-4 py-2.5"><span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700">Required*</span></td>
                                    <td class="px-4 py-2.5 text-slate-600">Explicit new expiry date YYYY-MM-DD (overrides days)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">* Provide at least one of <code class="bg-slate-100 px-1 rounded">days</code> or <code class="bg-slate-100 px-1 rounded">expires_at</code>.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Examples</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        <span class="{{ $c }}"># Add 365 days to existing expiry</span><br>
                        curl -X POST <span class="{{ $s }}">{{ $int }}/extend</span> -H <span class="{{ $s }}">"Authorization: Bearer TOKEN"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"license_key":"EXCL-A1B2-C3D4-E5F6","days":365}'</span><br><br>
                        <span class="{{ $c }}"># Set specific expiry date</span><br>
                        curl -X POST <span class="{{ $s }}">{{ $int }}/extend</span> -H <span class="{{ $s }}">"Authorization: Bearer TOKEN"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"license_key":"EXCL-A1B2-C3D4-E5F6","expires_at":"2028-01-01"}'</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        { <span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>, <span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License expiry extended."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span>, <span class="{{ $k }}">"expires_at"</span>: <span class="{{ $s }}">"2028-01-01"</span> }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- REVOKE                                                       --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'revoke'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/revoke</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-800 mb-5">
                        <strong>⚠ Irreversible.</strong> Revoking permanently invalidates the license and deactivates all devices. The customer immediately loses access. Use <strong>suspend</strong> for a reversible block.
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200"><tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-4 py-2.5 font-mono">license_key</td><td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td><td class="px-4 py-2.5 text-slate-600">License key to revoke</td></tr>
                                <tr><td class="px-4 py-2.5 font-mono">reason</td><td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span></td><td class="px-4 py-2.5 text-slate-600">Reason recorded in audit log (e.g. "Chargeback", "Fraud")</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        curl -X POST <span class="{{ $s }}">{{ $int }}/revoke</span> -H <span class="{{ $s }}">"Authorization: Bearer TOKEN"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Content-Type: application/json"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"license_key":"EXCL-A1B2-C3D4-E5F6","reason":"Chargeback"}'</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- SUSPEND                                                      --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'suspend'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 mb-4">Suspend & Unsuspend</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">POST</span>
                                <code class="text-xs font-mono text-slate-700">{{ $int }}/suspend</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-3">Temporarily block access. Reversible.</p>
                            <h4 class="text-xs font-semibold text-slate-900 mb-1">Headers</h4>
                            <div class="rounded bg-white-900 p-2 font-mono text-[10px] overflow-x-auto mb-2">
                                <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer TOKEN</span><br>
                                <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-900 mb-1">Body</h4>
                            <div class="rounded bg-white-900 p-2 font-mono text-[10px] overflow-x-auto">
                                { <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"..."</span>, <span class="{{ $k }}">"reason"</span>: <span class="{{ $s }}">"Payment overdue"</span> }
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">POST</span>
                                <code class="text-xs font-mono text-slate-700">{{ $int }}/unsuspend</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-3">Restore access immediately.</p>
                            <h4 class="text-xs font-semibold text-slate-900 mb-1">Headers</h4>
                            <div class="rounded bg-white-900 p-2 font-mono text-[10px] overflow-x-auto mb-2">
                                <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer TOKEN</span><br>
                                <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-900 mb-1">Body</h4>
                            <div class="rounded bg-white-900 p-2 font-mono text-[10px] overflow-x-auto">
                                { <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-..."</span> }
                            </div>
                        </div>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response (both endpoints) <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        { <span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>, <span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License suspended."</span>, <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-..."</span> }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- RESET DEVICES                                                --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'reset-devices'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/reset-devices</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Deactivates all active device activations for a license, resetting the slot count to 0. Useful when a customer replaces hardware or requests a clean slate. Historical records are preserved.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        { <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-A1B2-C3D4-E5F6"</span> }
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        { <span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>, <span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Reset 2 activation(s)."</span>, <span class="{{ $k }}">"deactivated_count"</span>: <span class="{{ $n }}">2</span> }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- REGENERATE KEY                                               --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'regenerate'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/regenerate-key</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-3">Generates a new license key. The old key is immediately invalidated. All other license properties (expiry, activations, customer) are preserved.</p>
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-800 mb-5">
                        <strong>⚠ The customer must update their app with the new key.</strong> Old key stops working immediately.
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        { <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-OLD-KEY-HERE"</span> }
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        { <span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>, <span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"License key regenerated."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"old_license_key"</span>: <span class="{{ $s }}">"EXCL-OLD-KEY-HERE"</span>, <span class="{{ $k }}">"new_license_key"</span>: <span class="{{ $s }}">"EXCL-NEW-XXXX-XXXX"</span> }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- ATTACH NOTES                                                 --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'notes'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">POST</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/attach-notes</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Append or replace internal notes on a license. Notes are visible in the admin dashboard only — not exposed in public API responses.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Content-Type</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Body</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200"><tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Required</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-4 py-2.5 font-mono">license_key</td><td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td><td class="px-4 py-2.5 text-slate-600">License key</td></tr>
                                <tr><td class="px-4 py-2.5 font-mono">notes</td><td class="px-4 py-2.5"><span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-600">Required</span></td><td class="px-4 py-2.5 text-slate-600">Note text. Max 2000 chars.</td></tr>
                                <tr><td class="px-4 py-2.5 font-mono">append</td><td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Optional</span></td><td class="px-4 py-2.5 text-slate-600">true = append to existing notes. false (default) = replace.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        curl -X POST <span class="{{ $s }}">{{ $int }}/attach-notes</span> -H <span class="{{ $s }}">"Authorization: Bearer TOKEN"</span> \<br>
                        &nbsp;&nbsp;-d <span class="{{ $s }}">'{"license_key":"EXCL-...","notes":"Spoke with customer re: upgrade","append":true}'</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- LOOK UP                                                      --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'lookup'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">GET</span>
                        <code class="text-sm font-mono text-slate-800 break-all">{{ $int }}/{'{key}'}</code>
                        <span class="ml-auto inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 ring-1 ring-inset ring-cyan-600/20">🔐 Auth Required</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-5">Retrieve full details for a single license including customer, product, activations, and batch info.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Request Headers</h3>
                    <div class="rounded-xl bg-white-900 p-3 font-mono text-xs overflow-x-auto mb-4">
                        <span class="{{ $k }}">Accept</span>: <span class="{{ $s }}">application/json</span><br>
                        <span class="{{ $k }}">Authorization</span>: <span class="{{ $s }}">Bearer YOUR_API_TOKEN</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Path Parameter</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-5">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200"><tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Parameter</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                            </tr></thead>
                            <tbody>
                                <tr><td class="px-4 py-2.5 font-mono">{key}</td><td class="px-4 py-2.5 text-slate-600">The license key, e.g. EXCL-A1B2-C3D4-E5F6</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Example Request</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        curl <span class="{{ $s }}">{{ $int }}/EXCL-A1B2-C3D4-E5F6</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Accept: application/json"</span> \<br>
                        &nbsp;&nbsp;-H <span class="{{ $s }}">"Authorization: Bearer YOUR_TOKEN"</span>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Success Response <span class="text-xs font-normal text-slate-500">HTTP 200</span></h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto">
                        { <span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span>, <span class="{{ $k }}">"data"</span>: {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"id"</span>: <span class="{{ $s }}">"uuid"</span>, <span class="{{ $k }}">"license_key"</span>: <span class="{{ $s }}">"EXCL-..."</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"edition"</span>: <span class="{{ $s }}">"professional"</span>, <span class="{{ $k }}">"status"</span>: <span class="{{ $s }}">"active"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"customer"</span>: { <span class="{{ $k }}">"name"</span>: <span class="{{ $s }}">"John Doe"</span>, <span class="{{ $k }}">"email"</span>: <span class="{{ $s }}">"john@example.com"</span> },<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"product"</span>: { <span class="{{ $k }}">"name"</span>: <span class="{{ $s }}">"CoreOps"</span>, <span class="{{ $k }}">"product_code"</span>: <span class="{{ $s }}">"COREOPS"</span> },<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"activations"</span>: [ { <span class="{{ $k }}">"device_id"</span>: <span class="{{ $s }}">"..."</span>, <span class="{{ $k }}">"status"</span>: <span class="{{ $s }}">"active"</span> } ]<br>
                        } }
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- RESPONSE SCHEMA                                              --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'responses'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Response Schema Reference</h2>

                    <p class="text-sm text-slate-600 mb-5">All API responses share a consistent envelope. Public endpoints use <code class="bg-slate-100 px-1 rounded text-xs">valid</code>; Provisioning endpoints use <code class="bg-slate-100 px-1 rounded text-xs">success</code>.</p>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Public Endpoint Envelope</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"valid"</span>: <span class="{{ $b }}">true</span> <span class="{{ $c }}">| false</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Human-readable result message"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"timestamp"</span>: <span class="{{ $s }}">"2026-05-22T10:00:00.000000Z"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"license"</span>: {                    <span class="{{ $c }}">// present on success</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"payload"</span>: { ... },  <span class="{{ $c }}">// signed entitlement data</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="{{ $k }}">"signature"</span>: <span class="{{ $s }}">"base64-hmac-sha256"</span><br>
                        &nbsp;&nbsp;},<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"error_code"</span>: <span class="{{ $s }}">"machine_readable_code"</span>,  <span class="{{ $c }}">// present on error</span><br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"errors"</span>: { ... }                <span class="{{ $c }}">// present on validation failure</span><br>
                        }
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Provisioning Endpoint Envelope</h3>
                    <div class="rounded-xl bg-white-900 p-4 font-mono text-xs overflow-x-auto mb-5">
                        {<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"success"</span>: <span class="{{ $b }}">true</span> <span class="{{ $c }}">| false</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"message"</span>: <span class="{{ $s }}">"Human-readable result message"</span>,<br>
                        &nbsp;&nbsp;<span class="{{ $k }}">"data"</span>: { ... }  <span class="{{ $c }}">// resource object on success</span><br>
                        }
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-2 text-sm">Payload Field Reference</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Field</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['license_id',         'UUID',      'Stable license UUID — never changes'],
                                    ['license_key',        'string',    'The license key string'],
                                    ['product_id',         'UUID',      'Product UUID'],
                                    ['product',            'string',    'Product code (e.g. "COREOPS")'],
                                    ['edition',            'string',    'standard | professional | enterprise'],
                                    ['type',               'string',    'lifetime | annual | monthly | trial'],
                                    ['status',             'string',    'active | inactive | expired | suspended | revoked | trial'],
                                    ['expires_at',         'date|null', 'License expiry date. null = lifetime.'],
                                    ['device_id',          'string',    'Device that made the request'],
                                    ['activated_at',       'ISO 8601',  'First activation timestamp — NEVER changes after first set'],
                                    ['issued_at',          'ISO 8601',  'Timestamp this response was generated'],
                                    ['max_devices',        'integer',   'Maximum allowed device activations'],
                                    ['activations_used',   'integer',   'Current active device count'],
                                    ['is_new_activation',  'boolean',   'true only on the very first activation of this device'],
                                    ['features',           'array',     'Feature flags granted. Gate app features from this list.'],
                                    ['grace_period_days',  'integer',   'Extra days after expiry during which license still validates'],
                                    ['offline_valid_until','ISO 8601',  'Offline validity window end. Store and enforce client-side.'],
                                    ['offline_ttl_hours',  'integer',   'How many hours the offline window spans'],
                                    ['response_nonce',     'string',    'Submit as request nonce on next call to close replay loop'],
                                    ['revocation_checksum','string',    'SHA-256 checksum. Compare against CRL when online.'],
                                    ['validation_source',  'string',    'online | grace_period | cached (client-set)'],
                                ] as [$f, $t, $d])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-violet-700">{{ $f }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $t }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $d }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- ERROR CODES                                                  --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'errors'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-5">HTTP Status Codes</h2>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-6">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">HTTP</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Name</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Typical Cause</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['200', 'OK',                   'green', 'Request succeeded'],
                                    ['201', 'Created',              'green', 'License or batch successfully created'],
                                    ['401', 'Unauthorized',         'red',   'Missing or invalid Bearer token on Internal API'],
                                    ['403', 'Forbidden',            'red',   'License revoked, suspended, expired, or activation limit reached'],
                                    ['404', 'Not Found',            'red',   'License key does not exist or product mismatch'],
                                    ['422', 'Unprocessable',        'amber', 'Validation failed — see "errors" in response body'],
                                    ['429', 'Too Many Requests',    'amber', 'Rate limit exceeded. Wait and retry with exponential backoff.'],
                                    ['500', 'Internal Server Error','red',   'Unexpected server error — contact support with timestamp'],
                                ] as [$code, $name, $color, $cause])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono font-bold text-{{ $color }}-700">{{ $code }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-900 text-xs">{{ $name }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $cause }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-3 text-sm">Machine-Readable Error Codes</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 mb-6">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">error_code</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">HTTP</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Meaning</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach([
                                    ['license_not_found',        '404', 'Key not found or product mismatch'],
                                    ['license_revoked',          '403', 'License permanently revoked'],
                                    ['license_suspended',        '403', 'License temporarily suspended'],
                                    ['license_expired',          '403', 'Past expiry and grace period'],
                                    ['version_not_allowed',      '403', 'app_version outside allowed range'],
                                    ['activation_limit_reached', '403', 'All device slots occupied'],
                                    ['timestamp_skew',           '403', 'Request timestamp > 5 min skew'],
                                    ['replay_attack',            '403', 'Nonce was already seen'],
                                    ['not_renewable',            '422', 'License type is not renewable'],
                                    ['activation_not_found',     '404', 'Device has no active activation'],
                                ] as [$ec, $code, $meaning])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 font-mono text-slate-900">{{ $ec }}</td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-red-700">{{ $code }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $meaning }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="font-semibold text-slate-900 mb-3 text-sm">License Status Values</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['active',    'bg-green-50 text-green-700',  'License is valid and in use'],
                            ['inactive',  'bg-slate-100 text-slate-600', 'Created but never activated'],
                            ['trial',     'bg-blue-50 text-blue-700',    'Time-limited trial license'],
                            ['expired',   'bg-red-50 text-red-700',      'Expiry date has passed'],
                            ['suspended', 'bg-amber-50 text-amber-700',  'Temporarily blocked — reversible'],
                            ['revoked',   'bg-slate-200 text-slate-600', 'Permanently invalidated'],
                        ] as [$status, $cls, $desc])
                        <div class="rounded-xl border border-slate-200 p-3">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">{{ $status }}</span>
                            <p class="mt-2 text-xs text-slate-600">{{ $desc }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- LICENSE WORKFLOW                                             --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div x-show="section === 'workflow'" x-cloak class="space-y-4">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-1">License Workflow</h2>
                    <p class="text-sm text-slate-600 mb-5">Complete lifecycle from creation to offline validation.</p>

                    {{-- Lifecycle diagram --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 mb-5">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-3">License Lifecycle States</h3>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            @foreach([
                                ['Created (inactive)', 'bg-slate-100 text-slate-600'],
                                ['→', ''],
                                ['First Validation', 'bg-blue-100 text-blue-700'],
                                ['→', ''],
                                ['Active', 'bg-green-100 text-green-700'],
                                ['→', ''],
                                ['Extend / Renew', 'bg-cyan-100 text-cyan-700'],
                                ['→', ''],
                                ['Expiring Soon', 'bg-amber-100 text-amber-700'],
                                ['→', ''],
                                ['Grace Period', 'bg-orange-100 text-orange-700'],
                                ['→', ''],
                                ['Expired', 'bg-red-100 text-red-700'],
                            ] as [$label, $cls])
                                @if($cls)
                                    <span class="rounded-full px-2.5 py-1 font-semibold {{ $cls }}">{{ $label }}</span>
                                @else
                                    <span class="text-slate-400 font-bold">{{ $label }}</span>
                                @endif
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-slate-500">At any active state, a license may be <span class="font-semibold text-amber-700">suspended</span> (reversible) or <span class="font-semibold text-red-700">revoked</span> (permanent) by an admin.</p>
                    </div>

                    {{-- Activation flow --}}
                    <h3 class="font-semibold text-slate-900 mb-3 text-sm">Standard Desktop Activation Flow</h3>
                    <div class="space-y-0 relative">
                        @php
                            $steps = [
                                ['bg-cyan-100 text-cyan-700', 'Store', "Your store/backend calls POST /api/v1/internal/licenses/create with Auth Bearer token. License created as inactive. License key delivered to customer via email or download page."],
                                ['bg-blue-100 text-blue-700', 'Client App', "User enters license key. App collects: device_id (stable UUID), hardware_id, platform, os, app_version. App calls POST /api/v1/licenses/validate — no auth required."],
                                ['bg-green-100 text-green-700', 'Server', "Server validates key, checks status/expiry/activation limits. If device_id is new → creates activation record, increments counter. If device_id exists → updates last_seen_at only. Returns signed JSON payload."],
                                ['bg-violet-100 text-violet-700', 'Client App', "App verifies HMAC signature using embedded product secret. Encrypts and stores license object to disk (AES-256 keyed by hardware_id). Grants access based on features[], edition, expires_at."],
                                ['bg-emerald-100 text-emerald-700', 'Offline', "On subsequent launches (online or offline): Load encrypted local file → decrypt → verify signature → check offline_valid_until and expires_at → gate features from features[]. No server call needed."],
                                ['bg-amber-100 text-amber-700', 'Check-in', "When internet available: call validate again. Server refreshes offline_valid_until + issues new signed payload. Client replaces stored file. Submit prior response_nonce as request nonce."],
                            ];
                        @endphp
                        @foreach($steps as $i => [$cls, $actor, $desc])
                        <div class="flex gap-3 mb-3">
                            <div class="flex-shrink-0 text-center w-24">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[10px] font-bold {{ $cls }}">{{ $actor }}</span>
                            </div>
                            <div class="flex-1 text-xs text-slate-600 pt-1 border-l border-slate-200 pl-3">{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Key invariants --}}
                    <h3 class="font-semibold text-slate-900 mt-5 mb-3 text-sm">Key Design Invariants</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach([
                            ['🔒', 'Idempotent Activation', 'Calling validate 100× with the same license_key + device_id produces exactly the same result as calling it once. activated_at is frozen after first set.'],
                            ['📅', 'Stable Dates', 'expires_at is never computed from activation_date. It is set at license creation time (or set to null for lifetime). Validation calls cannot change expiry.'],
                            ['✍️', 'Signed Payloads', 'Every response includes an HMAC-SHA256 signature over the payload. Clients must verify before trusting. Tampering is detectable offline.'],
                            ['📴', 'Offline-First Ready', 'offline_valid_until allows the app to run without internet. On expiry of the offline window, the app prompts for reconnect but retains user data.'],
                            ['🔄', 'Re-activation', 'A deactivated device (slot freed) may be re-activated later. Historical record is preserved for audit. Original activated_at is retained.'],
                            ['🚫', 'Replay Prevention', 'Nonce + timestamp fields prevent replayed requests. response_nonce ties server responses to client identity across sessions.'],
                        ] as [$icon, $title, $desc])
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="text-sm font-semibold text-slate-900 mb-1">{{ $icon }} {{ $title }}</p>
                            <p class="text-xs text-slate-600">{{ $desc }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Rate limits --}}
                    <h3 class="font-semibold text-slate-900 mt-5 mb-3 text-sm">Rate Limits</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">API Group</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Limit</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Scope</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase">Retry Strategy</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="px-4 py-2.5 font-semibold text-slate-900">Public Endpoints</td>
                                    <td class="px-4 py-2.5">60 req / min</td>
                                    <td class="px-4 py-2.5 text-slate-600">Per IP address</td>
                                    <td class="px-4 py-2.5 text-slate-600">Exponential backoff from 1s</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2.5 font-semibold text-slate-900">Provisioning API</td>
                                    <td class="px-4 py-2.5">120 req / min</td>
                                    <td class="px-4 py-2.5 text-slate-600">Per API token</td>
                                    <td class="px-4 py-2.5 text-slate-600">Exponential backoff from 1s</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
