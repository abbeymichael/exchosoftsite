<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Sign In — ExchoSoft')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Invalid email or password.');
            return;
        }

        $user = Auth::user();

        // If admin, redirect to admin panel
        if ($user->account_type === 'admin' || !empty($user->role)) {
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        }

        // Customer redirect
        $this->redirect(route('customer.dashboard'), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.customer.auth.login');
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
    <div class="w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-600 text-white font-bold">E</div>
                <span class="text-xl font-bold text-slate-900">ExchoSoft</span>
            </a>
            <p class="mt-3 text-sm text-slate-500">Sign in to your account</p>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-8">
            <h1 class="text-lg font-bold text-slate-900 mb-6">Welcome Back</h1>

            @if(session('status'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address</label>
                    <input wire:model="email" type="email" autocomplete="email" autofocus
                           class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Password</label>
                    </div>
                    <input wire:model="password" type="password" autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input wire:model="remember" type="checkbox" id="remember" class="rounded border-slate-300 text-cyan-600">
                    <label for="remember" class="text-xs text-slate-600 cursor-pointer">Remember me</label>
                </div>
                <button type="submit" class="w-full rounded-xl bg-cyan-600 py-3 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-md shadow-cyan-500/25">
                    Sign In
                </button>
            </form>

            <p class="text-center text-xs text-slate-500 mt-5">
                Don't have an account?
                <a href="{{ route('customer.register') }}" wire:navigate class="text-cyan-600 font-semibold hover:underline">Create one free</a>
            </p>
        </div>

        {{-- Admin link --}}
        <p class="text-center text-xs text-slate-400 mt-4">
            Are you an admin?
            <a href="{{ route('admin.login') }}" wire:navigate class="hover:text-slate-600">Admin Login →</a>
        </p>
    </div>
</div>
