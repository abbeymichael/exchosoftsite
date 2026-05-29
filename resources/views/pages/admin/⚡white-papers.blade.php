<?php

use App\Models\WhitePaper;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new #[Layout('layouts.admin')] #[Title('White Papers — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?int $editId = null;

    public string $title = '';
    public string $slug = '';
    public string $summary = '';
    public string $category = 'product';
    public string $status = 'draft';
    public bool $is_gated = true;
    public string $shop_product_id = '';
    public string $meta_title = '';
    public string $meta_description = '';

    public function updatedTitle(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->title)->slug()->toString();
        }
    }

    public function openCreate(): void { $this->resetForm(); $this->showForm = true; $this->editMode = false; }

    public function openEdit(int $id): void
    {
        $wp = WhitePaper::findOrFail($id);
        $this->editId = $id;
        $this->title = $wp->title;
        $this->slug = $wp->slug;
        $this->summary = $wp->summary ?? '';
        $this->category = $wp->category;
        $this->status = $wp->status;
        $this->is_gated = $wp->is_gated;
        $this->shop_product_id = $wp->shop_product_id ? (string) $wp->shop_product_id : '';
        $this->meta_title = $wp->meta_title ?? '';
        $this->meta_description = $wp->meta_description ?? '';
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate(['title' => 'required|string|max:300', 'slug' => 'required|string']);

        $data = [
            'author_id'       => auth()->id(),
            'title'           => $this->title,
            'slug'            => $this->slug,
            'summary'         => $this->summary,
            'category'        => $this->category,
            'status'          => $this->status,
            'is_gated'        => $this->is_gated,
            'shop_product_id' => $this->shop_product_id ?: null,
            'meta_title'      => $this->meta_title,
            'meta_description'=> $this->meta_description,
            'published_at'    => $this->status === 'published' ? now() : null,
        ];

        if ($this->editMode) {
            WhitePaper::findOrFail($this->editId)->update($data);
            session()->flash('success', 'White paper updated.');
        } else {
            WhitePaper::create($data);
            session()->flash('success', 'White paper created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void { WhitePaper::findOrFail($id)->delete(); session()->flash('success', 'Deleted.'); }

    public function resetForm(): void
    {
        $this->title = $this->slug = $this->summary = $this->meta_title = $this->meta_description = '';
        $this->category = 'product';
        $this->status = 'draft';
        $this->is_gated = true;
        $this->shop_product_id = '';
        $this->editId = null;
        $this->resetValidation();
    }

    #[Computed]
    public function papers()
    {
        return WhitePaper::with(['author', 'shopProduct'])
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()->paginate(15);
    }

    #[Computed]
    public function shopProducts()    {
        return ShopProduct::published()->orderBy('name')->get(['id', 'name']);
    }


}; ?>

<div>
    <x-slot:heading>White Papers</x-slot:heading>

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
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New White Paper
            </button>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Title</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Gated</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Downloads</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->papers as $paper)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $paper->title }}</p>
                                @if($paper->summary)<p class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $paper->summary }}</p>@endif
                            </td>
                            <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $paper->category }}</span></td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $paper->shopProduct?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($paper->is_gated)
                                    <span class="inline-flex items-center rounded-full bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-700">Gated</span>
                                @else
                                    <span class="text-xs text-slate-300">Free</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center text-slate-700">{{ number_format($paper->downloads) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $paper->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} capitalize">{{ $paper->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit({{ $paper->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $paper->id }})" wire:confirm="Delete?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No white papers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->papers->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $this->papers->links() }}</div>@endif
        </div>
    </div>

    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit White Paper' : 'New White Paper' }}</h2>
                <button wire:click="$set('showForm', false)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="flex-1 px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Title *</label>
                    <input wire:model.live="title" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slug</label>
                    <input wire:model="slug" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-mono focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Summary</label>
                    <livewire:markdown-editor wire:model="summary" :height="'150px'" />

                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                        <select wire:model="category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="product">Product</option>
                            <option value="technology">Technology</option>
                            <option value="industry">Industry</option>
                            <option value="research">Research</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Linked Product</label>
                    <select wire:model="shop_product_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        <option value="">None</option>
                        @foreach($this->shopProducts as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input wire:model="is_gated" type="checkbox" id="gated" class="rounded border-slate-300 text-cyan-600">
                    <label for="gated" class="text-sm text-slate-700 cursor-pointer">Gated (requires email/registration to download)</label>
                </div>
                <div class="border-t border-slate-100 pt-3 space-y-2">
                    <p class="text-xs font-semibold uppercase text-slate-500">SEO Meta</p>
                    <input wire:model="meta_title" type="text" placeholder="Meta title" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    <textarea wire:model="meta_description" rows="2" placeholder="Meta description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
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
