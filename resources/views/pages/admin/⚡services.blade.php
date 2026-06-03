<?php

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Services — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?string $editId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $icon = 'settings';

    public function updatedName(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->name)->slug()->toString();
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(string $id): void
    {
        $service = Service::findOrFail($id);
        $this->editId = $id;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->description = $service->description ?? '';
        $this->icon = $service->icon ?? 'settings';
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:services,slug' . ($this->editMode ? ',' . $this->editId . ',id' : ''),
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?: null,
            'icon' => $this->icon ?: 'settings',
        ];

        if ($this->editMode) {
            Service::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Service updated.');
        } else {
            Service::create($data);
            session()->flash('success', 'Service created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Service deleted.');
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->icon = 'settings';
        $this->editId = null;
        $this->resetValidation();
    }

    #[Computed]
    public function services()
    {
        return Service::when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <div>
        <x-slot:heading>Services</x-slot:heading>
    </div>

    <div class="space-y-5">

        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search services..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Service
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Icon</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Description</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Slug</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->services as $service)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <span class="material-symbols-outlined text-slate-600 text-lg" style="font-variation-settings:'FILL' 1;">{{ $service->icon ?? 'settings' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $service->name }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-slate-600 truncate max-w-xs">{{ $service->description ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs text-slate-500 font-mono">{{ $service->slug }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit('{{ $service->id }}')" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete('{{ $service->id }}')" wire:confirm="Delete this service?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">No services yet. Create your first one!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->services->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $this->services->links() }}</div>@endif
        </div>
    </div>

    {{-- Slide-over Form --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Service' : 'New Service' }}</h2>
                <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="flex-1 px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Service Name *</label>
                    <input wire:model="name" type="text" placeholder="e.g., Web Development" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slug *</label>
                    <input wire:model="slug" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 font-mono text-xs">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                    <textarea wire:model="description" rows="4" placeholder="Describe this service..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Icon (Material Symbol)</label>
                    <input wire:model="icon" type="text" placeholder="e.g., settings, code, palette" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    <p class="mt-1 text-xs text-slate-500">See <a href="https://fonts.google.com/icons" target="_blank" class="text-cyan-600 hover:text-cyan-700">Google Material Symbols</a></p>
                    @error('icon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                        {{ $editMode ? 'Update Service' : 'Create Service' }}
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    </div>
</div>
