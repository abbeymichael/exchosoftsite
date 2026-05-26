<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new #[Layout('layouts.admin')] #[Title('Admin Management — ExchoLicense')] class extends Component {

    use WithPagination;

    // List / filter
    public string $search = '';

    // Modal state
    public bool $showModal    = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId    = null;
    public ?int $deletingId   = null;

    // Form fields
    public string $form_name     = '';
    public string $form_email    = '';
    public string $form_role     = 'admin';
    public string $form_password = '';
    public string $form_password_confirmation = '';
    public bool   $form_is_active = true;

    // Success / Error messages
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    // ──────────────────────────────────────────────────────────────────────────
    // Authorization gate
    // ──────────────────────────────────────────────────────────────────────────

    private function canManageAdmins(): bool
    {
        return Auth::user()->isSuperAdmin();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Computed
    // ──────────────────────────────────────────────────────────────────────────

    public function getAdminsProperty()
    {
        return User::query()
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->orderByDesc('is_main_admin')
            ->orderBy('name')
            ->paginate(15);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        if (! $this->canManageAdmins()) {
            $this->errorMessage = 'You do not have permission to add admins.';
            return;
        }
        $this->resetForm();
        $this->editingId  = null;
        $this->showModal  = true;
    }

    public function openEdit(int $id): void
    {
        if (! $this->canManageAdmins()) {
            $this->errorMessage = 'You do not have permission to edit admins.';
            return;
        }
        $admin = User::findOrFail($id);
        $this->resetForm();
        $this->editingId   = $id;
        $this->form_name   = $admin->name;
        $this->form_email  = $admin->email;
        $this->form_role   = $admin->role;
        $this->form_is_active = (bool) $admin->is_active;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->successMessage = null;
        $this->errorMessage   = null;

        if (! $this->canManageAdmins()) {
            $this->errorMessage = 'Permission denied.';
            return;
        }

        if ($this->editingId) {
            $this->updateAdmin();
        } else {
            $this->createAdmin();
        }
    }

    private function createAdmin(): void
    {
        $this->validate([
            'form_name'                  => 'required|string|max:100',
            'form_email'                 => 'required|email|max:150|unique:users,email',
            'form_role'                  => 'required|in:admin,super_admin',
            'form_password'              => 'required|string|min:8|confirmed',
            'form_password_confirmation' => 'required|string',
        ]);

        User::create([
            'name'       => $this->form_name,
            'email'      => $this->form_email,
            'role'       => $this->form_role,
            'password'   => Hash::make($this->form_password),
            'is_active'  => $this->form_is_active,
            'created_by' => Auth::id(),
        ]);

        $this->closeModal();
        $this->successMessage = "Admin \"{$this->form_name}\" created successfully.";
    }

    private function updateAdmin(): void
    {
        $rules = [
            'form_name'  => 'required|string|max:100',
            'form_email' => 'required|email|max:150|unique:users,email,' . $this->editingId,
            'form_role'  => 'required|in:admin,super_admin',
        ];

        if ($this->form_password !== '') {
            $rules['form_password']              = 'required|string|min:8|confirmed';
            $rules['form_password_confirmation'] = 'required|string';
        }

        $this->validate($rules);

        $admin = User::findOrFail($this->editingId);

        // Protect main admin from being demoted or deactivated
        if ($admin->is_main_admin) {
            $this->form_role      = 'super_admin';
            $this->form_is_active = true;
        }

        $data = [
            'name'      => $this->form_name,
            'email'     => $this->form_email,
            'role'      => $admin->is_main_admin ? 'super_admin' : $this->form_role,
            'is_active' => $admin->is_main_admin ? true : $this->form_is_active,
        ];

        if ($this->form_password !== '') {
            $data['password'] = Hash::make($this->form_password);
        }

        $admin->update($data);

        $this->closeModal();
        $this->successMessage = "Admin \"{$admin->name}\" updated successfully.";
    }

    public function confirmDelete(int $id): void
    {
        if (! $this->canManageAdmins()) {
            $this->errorMessage = 'Permission denied.';
            return;
        }

        $admin = User::findOrFail($id);

        if ($admin->is_main_admin) {
            $this->errorMessage = 'The main admin account cannot be deleted.';
            return;
        }

        if ($admin->id === Auth::id()) {
            $this->errorMessage = 'You cannot delete your own account.';
            return;
        }

        $this->deletingId        = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteAdmin(): void
    {
        if (! $this->deletingId) return;

        $admin = User::findOrFail($this->deletingId);

        if ($admin->is_main_admin) {
            $this->errorMessage = 'The main admin account cannot be deleted.';
            $this->showDeleteConfirm = false;
            return;
        }

        if ($admin->id === Auth::id()) {
            $this->errorMessage = 'You cannot delete your own account.';
            $this->showDeleteConfirm = false;
            return;
        }

        $name = $admin->name;
        $admin->delete();

        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
        $this->successMessage    = "Admin \"{$name}\" deleted.";
    }

    public function toggleActive(int $id): void
    {
        $admin = User::findOrFail($id);

        if ($admin->is_main_admin) {
            $this->errorMessage = 'Cannot deactivate the main admin.';
            return;
        }

        $admin->update(['is_active' => ! $admin->is_active]);
        $this->successMessage = "Admin \"" . $admin->name . "\" " . ($admin->is_active ? 'deactivated' : 'activated') . '.';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function closeModal(): void
    {
        $this->showModal  = false;
        $this->editingId  = null;
        $this->resetForm();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
    }

    private function resetForm(): void
    {
        $this->form_name                  = '';
        $this->form_email                 = '';
        $this->form_role                  = 'admin';
        $this->form_password              = '';
        $this->form_password_confirmation = '';
        $this->form_is_active             = true;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <x-slot:heading>Admin Management</x-slot:heading>

    <div class="space-y-6">

        {{-- Alerts --}}
        @if($successMessage)
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $successMessage }}
            </div>
        @endif
        @if($errorMessage)
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                <svg class="h-4 w-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ $errorMessage }}
            </div>
        @endif

        @if(! auth()->user()->isSuperAdmin())
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                You have read-only access. Only Super Admins can manage admin accounts.
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative w-full sm:w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search admins…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 shadow-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none">
            </div>
            @if(auth()->user()->isSuperAdmin())
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Admin
            </button>
            @endif
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Admin</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Role</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Last Login</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Created</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->admins as $admin)
                        <tr class="hover:bg-slate-50/50 transition-colors {{ $admin->id === auth()->id() ? 'bg-cyan-50/30' : '' }}">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($admin->avatar)
                                        <img src="{{ asset('storage/' . $admin->avatar) }}" class="h-8 w-8 rounded-full object-cover flex-shrink-0" alt="{{ $admin->name }}">
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500 text-white text-xs font-bold flex-shrink-0">
                                            {{ $admin->initials() }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-900">
                                            {{ $admin->name }}
                                            @if($admin->id === auth()->id())
                                                <span class="ml-1 text-xs text-slate-400">(you)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                             {{ $admin->is_main_admin ? 'bg-violet-100 text-violet-700' : ($admin->role === 'super_admin' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                    @if($admin->is_main_admin)
                                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Main Admin
                                    @else
                                        {{ ucwords(str_replace('_', ' ', $admin->role)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                             {{ $admin->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $admin->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-xs">
                                {{ $admin->last_login_at?->diffForHumans() ?? 'Never' }}
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-xs">
                                {{ $admin->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @if(auth()->user()->isSuperAdmin())
                                <div class="flex items-center gap-2 justify-end">
                                    {{-- Toggle active (not for main admin) --}}
                                    @if(! $admin->is_main_admin && $admin->id !== auth()->id())
                                        <button wire:click="toggleActive({{ $admin->id }})"
                                                title="{{ $admin->is_active ? 'Deactivate' : 'Activate' }}"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                            @if($admin->is_active)
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    @endif

                                    {{-- Edit --}}
                                    <button wire:click="openEdit({{ $admin->id }})"
                                            title="Edit"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-cyan-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete (not main admin, not self) --}}
                                    @if(! $admin->is_main_admin && $admin->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $admin->id }})"
                                                title="Delete"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">No admins found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->admins->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">
                    {{ $this->admins->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ── Create / Edit Modal ─────────────────────────────────────────────── --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" wire:click.self="closeModal">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-xl overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">
                    {{ $editingId ? 'Edit Admin' : 'Add New Admin' }}
                </h2>
                <button wire:click="closeModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                    <input wire:model="form_name" type="text"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('form_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input wire:model="form_email" type="email"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('form_email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <select wire:model="form_role"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    @error('form_role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password {{ $editingId ? '(leave blank to keep current)' : '' }}
                    </label>
                    <input wire:model="form_password" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('form_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                    <input wire:model="form_password_confirmation" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                    @error('form_password_confirmation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                @if($editingId)
                @php $editingAdmin = \App\Models\User::find($editingId); @endphp
                @if($editingAdmin && ! $editingAdmin->is_main_admin)
                <div class="flex items-center gap-3">
                    <button type="button"
                            wire:click="$toggle('form_is_active')"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200
                                   {{ $form_is_active ? 'bg-cyan-600' : 'bg-slate-200' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200
                                     {{ $form_is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                    <label class="text-sm text-slate-700">Active account</label>
                </div>
                @endif
                @endif

                <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" wire:click="closeModal"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-xl bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                        {{ $editingId ? 'Update Admin' : 'Create Admin' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Delete Confirm Modal ─────────────────────────────────────────────── --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" wire:click.self="cancelDelete">
        <div class="w-full max-w-sm rounded-2xl bg-white shadow-xl overflow-hidden" @click.stop>
            <div class="px-6 pt-6 pb-4 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900">Delete Admin?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    This action cannot be undone. The admin account will be permanently removed.
                </p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="cancelDelete"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="deleteAdmin"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
