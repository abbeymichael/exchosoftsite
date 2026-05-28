<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Create Account — ExchoSoft')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree_terms = false;

    public function register(): void
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'company'  => 'nullable|string|max:200',
            'password' => 'required|min:8|confirmed',
            'agree_terms' => 'accepted',
        ], [
            'agree_terms.accepted' => 'You must agree to the terms and conditions.',
        ]);

        $user = User::create([
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'company'      => $this->company,
            'password'     => Hash::make($this->password),
            'account_type' => 'customer',
            'is_customer'  => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        $this->redirect(route('customer.dashboard'), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('pages.customer.auth.register');
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-600 text-white font-bold">E</div>
                <span class="text-xl font-bold text-slate-900">ExchoSoft</span>
            </a>
            <p class="mt-3 text-sm text-slate-500">Create your customer account</p>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 p-8">
            <h1 class="text-lg font-bold text-slate-900 mb-6">Create Account</h1>

            <form wire:submit="register" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name *</label>
                    <input wire:model="name" type="text" autocomplete="name" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address *</label>
                    <input wire:model="email" type="email" autocomplete="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone</label>
                        <input wire:model="phone" type="tel" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company</label>
                        <input wire:model="company" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password *</label>
                    <input wire:model="password" type="password" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirm Password *</label>
                    <input wire:model="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                </div>
                <div class="flex items-start gap-2">
                    <input wire:model="agree_terms" type="checkbox" id="terms" class="mt-0.5 rounded border-slate-300 text-cyan-600">
                    <label for="terms" class="text-xs text-slate-600 cursor-pointer">
                        I agree to ExchoSoft's <a href="#" class="text-cyan-600 hover:underline">Terms of Service</a> and <a href="#" class="text-cyan-600 hover:underline">Privacy Policy</a>
                    </label>
                </div>
                @error('agree_terms') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                <button type="submit" class="w-full rounded-xl bg-cyan-600 py-3 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-md shadow-cyan-500/25 mt-2">
                    Create Account
                </button>
            </form>

            <p class="text-center text-xs text-slate-500 mt-5">
                Already have an account?
                <a href="{{ route('customer.login') }}" wire:navigate class="text-cyan-600 font-semibold hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
