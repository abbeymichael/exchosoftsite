<?php

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('My Profile — ExchoSoft')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $country = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name    = $user->name;
        $this->email   = $user->email;
        $this->phone   = $user->phone ?? '';
        $this->company = $user->company ?? '';
        $this->country = $user->country ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:30',
        ]);

        auth()->user()->update([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'company' => $this->company,
            'country' => $this->country,
        ]);

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);
        $this->current_password = $this->new_password = $this->new_password_confirmation = '';
        session()->flash('password_success', 'Password updated successfully.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.customer.profile');
    }
}; ?>

<div class="py-10">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('customer.dashboard') }}" wire:navigate class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
        </div>

        {{-- Profile Form --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-8 mb-6">
            <h2 class="text-base font-semibold text-slate-900 mb-5">Account Information</h2>

            @if(session('profile_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('profile_success') }}</div>
            @endif

            <form wire:submit="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name *</label>
                        <input wire:model="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email *</label>
                        <input wire:model="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone</label>
                        <input wire:model="phone" type="tel" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company</label>
                        <input wire:model="company" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Country</label>
                        <input wire:model="country" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
                <button type="submit" class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                    Update Profile
                </button>
            </form>
        </div>

        {{-- Password Form --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-8">
            <h2 class="text-base font-semibold text-slate-900 mb-5">Change Password</h2>

            @if(session('password_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('password_success') }}</div>
            @endif

            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Current Password</label>
                    <input wire:model="current_password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">New Password</label>
                    <input wire:model="new_password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                    @error('new_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirm New Password</label>
                    <input wire:model="new_password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition-colors">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
