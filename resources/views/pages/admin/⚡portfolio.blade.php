<?php

use App\Models\PortfolioItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Portfolio — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?int $editId = null;

    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public string $category = 'software';
    public string $client_name = '';
    public string $client_industry = '';
    public string $project_url = '';
    public string $github_url = '';
    public string $duration = '';
    public string $status = 'published';
    public bool $is_featured = false;
    public int $sort_order = 0;

    public function updatedTitle(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->title)->slug()->toString();
        }
    }

    public function openCreate(): void { $this->resetForm(); $this->showForm = true; $this->editMode = false; }

    public function openEdit(int $id): void
    {
        $item = PortfolioItem::findOrFail($id);
        $this->editId = $id;
        $this->title = $item->title;
        $this->slug = $item->slug;
        $this->description = $item->description ?? '';
        $this->category = $item->category;
        $this->client_name = $item->client_name ?? '';
        $this->client_industry = $item->client_industry ?? '';
        $this->project_url = $item->project_url ?? '';
        $this->github_url = $item->github_url ?? '';
        $this->duration = $item->duration ?? '';
        $this->status = $item->status;
        $this->is_featured = $item->is_featured;
        $this->sort_order = $item->sort_order;
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate(['title' => 'required|string|max:300', 'slug' => 'required|string']);

        $data = [
            'title'           => $this->title,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'category'        => $this->category,
            'client_name'     => $this->client_name,
            'client_industry' => $this->client_industry,
            'project_url'     => $this->project_url,
            'github_url'      => $this->github_url,
            'duration'        => $this->duration,
            'status'          => $this->status,
            'is_featured'     => $this->is_featured,
            'sort_order'      => $this->sort_order,
        ];

        if ($this->editMode) {
            PortfolioItem::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Portfolio item updated.');
        } else {
            PortfolioItem::create($data);
            session()->flash('success', 'Portfolio item created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void { PortfolioItem::findOrFail($id)->delete(); session()->flash('success', 'Deleted.'); }

    public function resetForm(): void
    {
        $this->title = $this->slug = $this->description = $this->client_name = '';
        $this->client_industry = $this->project_url = $this->github_url = $this->duration = '';
        $this->category = 'software';
        $this->status = 'published';
        $this->is_featured = false;
        $this->sort_order = 0;
        $this->editId = null;
        $this->resetValidation();
    }

    // ────────────────────────────────────────────────────────────────────────
    #[Computed]
    public function items()
    {
        return PortfolioItem::when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('client_name', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('sort_order')->orderByDesc('created_at')
            ->paginate(15);
    }
}; ?>

<div>
    <x-slot:heading>Portfolio</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="software">Software</option>
                    <option value="web">Web</option>
                    <option value="mobile">Mobile</option>
                    <option value="design">Design</option>
                    <option value="consulting">Consulting</option>
                </select>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Project
            </button>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">#</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Project</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Client</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Featured</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $item->sort_order }}</td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $item->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($item->project_url)<a href="{{ $item->project_url }}" target="_blank" class="text-xs text-cyan-600 hover:underline">Live</a>@endif
                                    @if($item->github_url)<a href="{{ $item->github_url }}" target="_blank" class="text-xs text-slate-400 hover:underline">GitHub</a>@endif
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-slate-700">{{ $item->client_name ?: '—' }}</p>
                                @if($item->duration)<p class="text-xs text-slate-400">{{ $item->duration }}</p>@endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $item->category }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($item->is_featured)<span class="text-amber-400">⭐</span>@else<span class="text-slate-300">—</span>@endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $item->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} capitalize">{{ $item->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit({{ $item->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Delete?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No portfolio items yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->items->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $this->items->links() }}</div>@endif
        </div>
    </div>

    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Project' : 'New Portfolio Item' }}</h2>
                <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="flex-1 px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Project Title *</label>
                    <input wire:model.live="title" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                    <livewire:markdown-editor wire:model="description" :height="'150px'" placeholder="Description" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                        <select wire:model="category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="software">Software</option>
                            <option value="web">Web</option>
                            <option value="mobile">Mobile</option>
                            <option value="design">Design</option>
                            <option value="consulting">Consulting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Client Name</label>
                        <input wire:model="client_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Duration</label>
                        <input wire:model="duration" type="text" placeholder="e.g. 3 months" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Sort Order</label>
                        <input wire:model="sort_order" type="number" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_featured" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Featured</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Live URL</label>
                    <input wire:model="project_url" type="url" placeholder="https://" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">GitHub URL</label>
                    <input wire:model="github_url" type="url" placeholder="https://github.com/..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">{{ $editMode ? 'Update' : 'Create' }}</button>
                    <button type="button" wire:click="$set('showForm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
