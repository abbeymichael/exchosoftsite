<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;

new #[Layout('layouts.admin')] #[Title('My Profile — ExchoLicense')] class extends Component {

    use WithFileUploads;

    // Profile fields
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    public $avatar = null;

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // UI state
    public string $profileTab = 'profile'; // profile | security
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    public function mount(): void
    {
        $user        = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $this->successMessage = null;
        $this->errorMessage   = null;

        $user = Auth::user();

        $this->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ]);

        // Handle avatar upload
        $avatarPath = $user->avatar;
        if ($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
        }

        $user->update([
            'name'   => $this->name,
            'email'  => $this->email,
            'avatar' => $avatarPath,
        ]);

        $this->successMessage = 'Profile updated successfully.';
        $this->avatar         = null;
    }

    public function updatePassword(): void
    {
        $this->successMessage = null;
        $this->errorMessage   = null;

        $user = Auth::user();

        $this->validate([
            'current_password'          => 'required|string',
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => 'required|string',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->errorMessage = 'Current password is incorrect.';
            return;
        }

        if ($this->current_password === $this->new_password) {
            $this->errorMessage = 'New password must be different from current password.';
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->current_password          = '';
        $this->new_password              = '';
        $this->new_password_confirmation = '';

        $this->successMessage = 'Password changed successfully.';
    }

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }


}; ?>

<div>
    <x-slot:heading>My Profile</x-slot:heading>

    <div class="max-w-3xl space-y-6">

        {{-- Success / Error banner --}}
        @if($successMessage)
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $successMessage }}
            </div>
        @endif
        @if($errorMessage)
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Tab switcher --}}
        <div class="flex gap-2 border-b border-slate-200">
            <button wire:click="$set('profileTab','profile')"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px
                           {{ $profileTab === 'profile' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Profile Info
            </button>
            <button wire:click="$set('profileTab','security')"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px
                           {{ $profileTab === 'security' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Security
            </button>
        </div>

        {{-- ── Profile Tab ─────────────────────────────────────────────────── --}}
        @if($profileTab === 'profile')
        <form wire:submit="updateProfile" class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Profile Information</h2>
                <p class="mt-1 text-sm text-slate-500">Update your display name, email address, and avatar.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

                {{-- Avatar --}}
                <div class="flex items-center gap-5">
                    <div class="relative flex-shrink-0">
                        @if($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-slate-200" alt="Preview">
                        @elseif($this->user()->avatar)
                            <img src="{{ asset('storage/' . $this->user()->avatar) }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-slate-200" alt="{{ $this->user()->name }}">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-cyan-500 text-white text-lg font-bold ring-2 ring-slate-200">
                                {{ $this->user()->initials() }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Change Avatar
                            <input type="file" wire:model="avatar" accept="image/*" class="sr-only">
                        </label>
                        <p class="mt-1 text-xs text-slate-400">PNG, JPG, GIF up to 2MB</p>
                        @error('avatar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                    <input wire:model="name" type="text" autocomplete="name"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input wire:model="email" type="email" autocomplete="email"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Role badge --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                     {{ $this->user()->is_main_admin ? 'bg-violet-100 text-violet-700' : 'bg-cyan-50 text-cyan-700' }}">
                            {{ $this->user()->is_main_admin ? 'Main Admin' : ucwords(str_replace('_', ' ', $this->user()->role)) }}
                        </span>
                        @if($this->user()->is_main_admin)
                            <span class="text-xs text-slate-400">— This account is protected and cannot be deleted.</span>
                        @endif
                    </div>
                </div>

                {{-- Last login --}}
                @if($this->user()->last_login_at)
                <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-sm text-slate-600">
                    <span class="font-medium">Last login:</span>
                    {{ $this->user()->last_login_at->format('d M Y, H:i') }}
                    @if($this->user()->last_login_ip)
                        <span class="text-slate-400">from {{ $this->user()->last_login_ip }}</span>
                    @endif
                </div>
                @endif

            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
        @endif

        {{-- ── Security Tab ─────────────────────────────────────────────────── --}}
        @if($profileTab === 'security')
        <form wire:submit="updatePassword" class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Change Password</h2>
                <p class="mt-1 text-sm text-slate-500">Use a strong password of at least 8 characters.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                    <input wire:model="current_password" type="password" autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                    <input wire:model="new_password" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('new_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                    <input wire:model="new_password_confirmation" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('new_password_confirmation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Password tips --}}
                <ul class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-xs text-slate-500 space-y-1 list-disc list-inside">
                    <li>At least 8 characters long</li>
                    <li>Mix of uppercase and lowercase letters</li>
                    <li>Include at least one number or symbol</li>
                </ul>

            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Update Password
                </button>
            </div>
        </form>
        @endif

    </div>
</div>
