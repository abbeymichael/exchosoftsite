<?php

use App\Models\CaseStudy;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Case Studies — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?int $editId = null;

    public string $title = '';
    public string $slug = '';
    public string $client_name = '';
    public string $client_industry = '';
    public string $challenge = '';
    public string $solution = '';
    public string $results = '';
    public string $status = 'draft';
    public bool $is_featured = false;
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
        $cs = CaseStudy::findOrFail($id);
        $this->editId = $id;
        $this->title = $cs->title;
        $this->slug = $cs->slug;
        $this->client_name = $cs->client_name;
        $this->client_industry = $cs->client_industry ?? '';
        $this->challenge = $cs->challenge ?? '';
        $this->solution = $cs->solution ?? '';
        $this->results = $cs->results ?? '';
        $this->status = $cs->status;
        $this->is_featured = $cs->is_featured;
        $this->shop_product_id = $cs->shop_product_id ? (string) $cs->shop_product_id : '';
        $this->meta_title = $cs->meta_title ?? '';
        $this->meta_description = $cs->meta_description ?? '';
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:300',
            'slug'        => 'required|string',
            'client_name' => 'required|string|max:200',
        ]);

        $data = [
            'author_id'       => auth()->id(),
            'title'           => $this->title,
            'slug'            => $this->slug,
            'client_name'     => $this->client_name,
            'client_industry' => $this->client_industry,
            'challenge'       => $this->challenge,
            'solution'        => $this->solution,
            'results'         => $this->results,
            'status'          => $this->status,
            'is_featured'     => $this->is_featured,
            'shop_product_id' => $this->shop_product_id ?: null,
            'meta_title'      => $this->meta_title,
            'meta_description'=> $this->meta_description,
            'published_at'    => $this->status === 'published' ? now() : null,
        ];

        if ($this->editMode) {
            CaseStudy::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Case study updated.');
        } else {
            CaseStudy::create($data);
            session()->flash('success', 'Case study created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void { CaseStudy::findOrFail($id)->delete(); session()->flash('success', 'Deleted.'); }

    public function resetForm(): void
    {
        $this->title = $this->slug = $this->client_name = $this->client_industry = '';
        $this->challenge = $this->solution = $this->results = '';
        $this->status = 'draft';
        $this->is_featured = false;
        $this->shop_product_id = $this->meta_title = $this->meta_description = '';
        $this->editId = null;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $studies = CaseStudy::with(['author', 'shopProduct'])
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('client_name', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()->paginate(15);

        $shopProducts = ShopProduct::published()->orderBy('name')->get(['id', 'name']);

        return view('pages.admin.case-studies', compact('studies', 'shopProducts'));
    }
}; ?>

<div>
    <x-slot:heading>Case Studies</x-slot:heading>

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
                New Case Study
            </button>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Title</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Client</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Featured</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Views</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($studies as $study)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900">{{ $study->title }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-slate-700">{{ $study->client_name }}</p>
                                @if($study->client_industry)<p class="text-xs text-slate-400">{{ $study->client_industry }}</p>@endif
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $study->shopProduct?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($study->is_featured)<span class="text-amber-400">⭐</span>@else<span class="text-slate-300">—</span>@endif
                            </td>
                            <td class="px-5 py-3 text-center text-slate-700">{{ number_format($study->views) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $study->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} capitalize">{{ $study->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit({{ $study->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $study->id }})" wire:confirm="Delete?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No case studies yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($studies->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $studies->links() }}</div>@endif
        </div>
    </div>

    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-2xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Case Study' : 'New Case Study' }}</h2>
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
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Client Name *</label>
                        <input wire:model="client_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        @error('client_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Client Industry</label>
                        <input wire:model="client_industry" type="text" placeholder="e.g. Retail, Healthcare" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Linked Product</label>
                        <select wire:model="shop_product_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="">None</option>
                            @foreach($shopProducts as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input wire:model="is_featured" type="checkbox" id="feat" class="rounded border-slate-300 text-cyan-600">
                    <label for="feat" class="text-sm text-slate-700 cursor-pointer">Feature on website</label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">The Challenge</label>
                    <textarea wire:model="challenge" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Our Solution</label>
                    <textarea wire:model="solution" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Results Achieved</label>
                    <textarea wire:model="results" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
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
