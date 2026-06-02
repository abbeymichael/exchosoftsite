<?php

use App\Models\SiteNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Site Notifications — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?string $editId = null;

    public string $title = '';
    public string $message = '';
    public string $button_label = '';
    public string $button_url = '';
    public int $priority = 1;
    public bool $is_active = true;
    public bool $is_dismissible = true;
    public ?string $starts_at = null;
    public ?string $ends_at = null;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $notification = SiteNotification::findOrFail($id);
        $this->editId = $id;
        $this->title = $notification->title;
        $this->message = $notification->message;
        $this->button_label = $notification->button_label ?? '';
        $this->button_url = $notification->button_url ?? '';
        $this->priority = $notification->priority;
        $this->is_active = $notification->is_active;
        $this->is_dismissible = $notification->is_dismissible;
        $this->starts_at = $notification->starts_at?->format('Y-m-d\TH:i') ?? null;
        $this->ends_at = $notification->ends_at?->format('Y-m-d\TH:i') ?? null;
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'           => 'required|string|max:200',
            'message'         => 'required|string|max:1000',
            'button_label'    => 'nullable|string|max:50',
            'button_url'      => 'nullable|url',
            'priority'        => 'required|integer|min:1|max:10',
            'starts_at'       => 'nullable|date_format:Y-m-d\TH:i',
            'ends_at'         => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $data = [
            'title'           => $this->title,
            'message'         => $this->message,
            'button_label'    => $this->button_label ?: null,
            'button_url'      => $this->button_url ?: null,
            'priority'        => $this->priority,
            'is_active'       => $this->is_active,
            'is_dismissible'  => $this->is_dismissible,
            'starts_at'       => $this->starts_at ? now()->parse($this->starts_at) : null,
            'ends_at'         => $this->ends_at ? now()->parse($this->ends_at) : null,
        ];

        if ($this->editMode) {
            SiteNotification::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Notification updated.');
        } else {
            SiteNotification::create($data);
            session()->flash('success', 'Notification created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        SiteNotification::findOrFail($id)->delete();
        session()->flash('success', 'Notification deleted.');
    }

    public function toggleStatus(string $id): void
    {
        $notification = SiteNotification::findOrFail($id);
        $notification->update(['is_active' => !$notification->is_active]);
    }

    public function resetForm(): void
    {
        $this->title = $this->message = $this->button_label = $this->button_url = '';
        $this->priority = 1;
        $this->is_active = true;
        $this->is_dismissible = true;
        $this->starts_at = $this->ends_at = null;
        $this->editId = null;
        $this->resetValidation();
    }

    #[Computed]
    public function notifications()
    {
        return SiteNotification::when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%')->orWhere('message', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <x-slot:heading>Site Notifications</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search notifications..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Notification
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Title</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Message</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Active Period</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->notifications as $notification)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $notification->title }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-slate-600 truncate max-w-xs">{{ $notification->message }}</p>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $notification->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <button wire:click="toggleStatus('{{ $notification->id }}')"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $notification->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    {{ $notification->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">
                                @if($notification->starts_at || $notification->ends_at)
                                    {{ $notification->starts_at?->format('d M Y') ?? 'No start' }} 
                                    — 
                                    {{ $notification->ends_at?->format('d M Y') ?? 'No end' }}
                                @else
                                    <span class="text-slate-400">Always</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit('{{ $notification->id }}')" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete('{{ $notification->id }}')" wire:confirm="Delete this notification?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">No notifications yet. Create your first one!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->notifications->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $this->notifications->links() }}</div>@endif
        </div>
    </div>

    {{-- Slide-over Form --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-2xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Notification' : 'New Notification' }}</h2>
                <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="flex-1 px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Title *</label>
                    <input wire:model="title" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Message *</label>
                    <textarea wire:model="message" rows="3" placeholder="Main notification message..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Priority</label>
                        <input wire:model="priority" type="number" min="1" max="10" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end pb-1 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_active" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_dismissible" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Dismissible</span>
                        </label>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Button (Optional)</p>
                    <div class="space-y-2">
                        <input wire:model="button_label" type="text" placeholder="Button text..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        <input wire:model="button_url" type="url" placeholder="Button URL..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('button_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-3">Active Period (Optional)</p>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Starts At</label>
                            <input wire:model="starts_at" type="datetime-local" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Ends At</label>
                            <input wire:model="ends_at" type="datetime-local" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                        {{ $editMode ? 'Update Notification' : 'Create Notification' }}
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
