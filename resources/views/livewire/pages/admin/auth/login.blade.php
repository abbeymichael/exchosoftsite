<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Login — ExchoLicense Admin')] class extends Component {

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        session()->regenerate();

        $this->redirect(route('admin.dashboard'), navigate: true);
    }
}; ?>

<div class="rounded-2xl bg-white px-8 py-10 shadow-sm ring-1 ring-slate-200">

    <h2 class="mb-6 text-center text-lg font-semibold text-slate-900">Sign in to your account</h2>

    {{-- Flash --}}
    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                Email address
            </label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                autofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm
                       focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500
                       @error('email') border-red-400 focus:border-red-400 focus:ring-red-400 @enderror"
                placeholder="admin@exchosoft.com"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">
                Password
            </label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="current-password"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm
                       focus:border-cyan-500 focus:outline-none focus:ring-1 focus:ring-cyan-500
                       @error('password') border-red-400 focus:border-red-400 focus:ring-red-400 @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center gap-3">
            <input
                type="checkbox"
                id="remember"
                wire:model="remember"
                class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
            >
            <label for="remember" class="text-sm text-slate-600">Keep me signed in</label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full rounded-lg bg-cyan-600 px-4 py-2.5 text-sm font-semibold text-white
                   hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2
                   transition-colors disabled:opacity-60"
            wire:loading.attr="disabled"
            wire:target="login"
        >
            <span wire:loading.remove wire:target="login">Sign in</span>
            <span wire:loading wire:target="login">Signing in…</span>
        </button>

    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} ExchoSoft &mdash; ExchoLicense Admin
    </p>

</div>
