<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('API Tokens — ExchoLicense')] class extends Component
{
    public bool   $showModal      = false;
    public string $token_name     = '';
    public string $newTokenValue  = '';
    public bool   $showNewToken   = false;

    public function createToken(): void
    {
        $this->validate([
            'token_name' => 'required|string|max:100',
        ]);

        $token = auth()->user()->createToken($this->token_name);

        $this->newTokenValue = $token->plainTextToken;
        $this->showNewToken  = true;
        $this->showModal     = false;
        $this->token_name    = '';
    }

    public function deleteToken(int $tokenId): void
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();
        session()->flash('success', 'API token revoked.');
    }

    #[Computed]
    public function tokens()
    {
        return auth()->user()->tokens()->latest()->get();
    }
}; ?>

<div>
    <x-slot:heading>API Tokens</x-slot:heading>

    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- New Token Alert --}}
        @if ($showNewToken)
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-5 py-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-amber-800">Token created — copy it now!</p>
                        <p class="text-xs text-amber-700 mt-1">This token will NOT be shown again. Store it somewhere safe.</p>
                        <div class="mt-3 flex items-center gap-2">
                            <code class="flex-1 block rounded-lg bg-white border border-amber-200 px-3 py-2 text-sm font-mono text-slate-800 break-all">{{ $newTokenValue }}</code>
                            <button
                                x-data
                                x-on:click="navigator.clipboard.writeText('{{ $newTokenValue }}').then(() => { $el.innerText = 'Copied!'; setTimeout(() => $el.innerText = 'Copy', 2000); })"
                                class="flex-shrink-0 rounded-lg border border-amber-300 bg-amber-100 px-3 py-2 text-xs font-medium text-amber-700 hover:bg-amber-200 transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>
                    <button wire:click="$set('showNewToken', false)" class="text-amber-400 hover:text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Info Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Internal Provisioning API</h3>
                    <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                        API tokens allow external systems (your e-commerce store, customer portal, etc.)
                        to provision, revoke, and manage licenses via the protected internal API.
                        Tokens are scoped to your account and rate-limited to 120 requests/minute.
                    </p>
                </div>
                <button wire:click="$set('showModal', true)"
                        class="flex-shrink-0 flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors ml-4">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Token
                </button>
            </div>

            {{-- API Base URL --}}
            <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Base URL</p>
                <code class="text-sm font-mono text-slate-700">{{ url('/api/v1/internal') }}</code>
            </div>

            {{-- Quick Reference --}}
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Method</th>
                            <th class="pb-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Endpoint</th>
                            <th class="pb-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            ['POST', '/licenses/create',       'Create a license (optionally customer-linked)'],
                            ['POST', '/licenses/bulk-create',  'Bulk-generate a license batch'],
                            ['POST', '/licenses/create-trial', 'Create a trial license'],
                            ['POST', '/licenses/extend',       'Extend license expiry'],
                            ['POST', '/licenses/revoke',       'Revoke a license permanently'],
                            ['POST', '/licenses/suspend',      'Suspend a license'],
                            ['POST', '/licenses/unsuspend',    'Re-activate a suspended license'],
                            ['POST', '/licenses/reset-devices','Reset all device activations'],
                            ['POST', '/licenses/regenerate-key','Regenerate the license key'],
                            ['POST', '/licenses/attach-notes', 'Update notes on a license'],
                            ['GET',  '/licenses/{key}',        'Look up a license by key'],
                        ] as [$method, $endpoint, $desc])
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 pr-4">
                                    <span class="inline-flex rounded px-1.5 py-0.5 text-xs font-bold
                                        {{ $method === 'GET' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $method }}
                                    </span>
                                </td>
                                <td class="py-2 pr-4 font-mono text-slate-700">{{ $endpoint }}</td>
                                <td class="py-2 text-slate-500">{{ $desc }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Auth example --}}
            <div class="mt-4 rounded-xl bg-slate-900 p-4">
                <p class="text-xs font-semibold text-slate-400 mb-2">Authentication Header</p>
                <code class="text-sm font-mono text-emerald-400">Authorization: Bearer YOUR_TOKEN_HERE</code>
            </div>
        </div>

        {{-- Tokens List --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Active Tokens</h3>
            </div>
            @forelse($this->tokens as $token)
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $token->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Created {{ $token->created_at->diffForHumans() }}
                            @if($token->last_used_at)
                                · Last used {{ $token->last_used_at->diffForHumans() }}
                            @else
                                · Never used
                            @endif
                        </p>
                    </div>
                    <button wire:click="deleteToken({{ $token->id }})"
                            wire:confirm="Revoke this token? This cannot be undone."
                            class="text-sm font-medium text-red-600 hover:text-red-700">
                        Revoke
                    </button>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-slate-400">
                    No tokens yet.
                    <button wire:click="$set('showModal', true)" class="text-cyan-600 hover:underline">Create your first token</button>.
                </div>
            @endforelse
        </div>

    </div>

    {{-- Create Token Modal --}}
    <div
        x-data
        x-show="$wire.showModal"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        style="display: none; position: fixed; inset: 0; z-index: 200; overflow-y: auto;"
        aria-modal="true" role="dialog"
    >
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(2px);"
                 x-on:click="$wire.set('showModal', false)"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Create API Token</h2>
                    <button wire:click="$set('showModal', false)"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createToken" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Token Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="token_name"
                               placeholder="e.g. MyStore Integration"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        @error('token_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1.5 text-xs text-slate-400">Give it a descriptive name so you know which system is using it.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                            Create Token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
