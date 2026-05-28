<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Blog Posts — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterCategory = '';

    public bool $showForm = false;
    public bool $editMode = false;
    public ?int $editId = null;

    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public string $category = 'general';
    public string $status = 'draft';
    public bool $is_featured = false;
    public int $read_time_minutes = 5;
    public string $meta_title = '';
    public string $meta_description = '';

    public function updatedTitle(): void
    {
        if (!$this->editMode) {
            $this->slug = str($this->title)->slug()->toString();
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openEdit(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $this->editId = $id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content ?? '';
        $this->category = $post->category;
        $this->status = $post->status;
        $this->is_featured = $post->is_featured;
        $this->read_time_minutes = $post->read_time_minutes;
        $this->meta_title = $post->meta_title ?? '';
        $this->meta_description = $post->meta_description ?? '';
        $this->showForm = true;
        $this->editMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'   => 'required|string|max:300',
            'slug'    => 'required|string|max:300',
            'content' => 'nullable|string',
        ]);

        $data = [
            'author_id'          => auth()->id(),
            'title'              => $this->title,
            'slug'               => $this->slug,
            'excerpt'            => $this->excerpt,
            'content'            => $this->content,
            'category'           => $this->category,
            'status'             => $this->status,
            'is_featured'        => $this->is_featured,
            'read_time_minutes'  => $this->read_time_minutes,
            'meta_title'         => $this->meta_title,
            'meta_description'   => $this->meta_description,
            'published_at'       => $this->status === 'published' ? now() : null,
        ];

        if ($this->editMode) {
            BlogPost::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Blog post updated.');
        } else {
            BlogPost::create($data);
            session()->flash('success', 'Blog post created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        BlogPost::findOrFail($id)->delete();
        session()->flash('success', 'Post deleted.');
    }

    public function toggleStatus(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $newStatus = $post->status === 'published' ? 'draft' : 'published';
        $post->update([
            'status'       => $newStatus,
            'published_at' => $newStatus === 'published' ? now() : null,
        ]);
    }

    public function resetForm(): void
    {
        $this->title = $this->slug = $this->excerpt = $this->content = '';
        $this->category = 'general';
        $this->status = 'draft';
        $this->is_featured = false;
        $this->read_time_minutes = 5;
        $this->meta_title = $this->meta_description = '';
        $this->editId = null;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $posts = BlogPost::with('author')
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->latest()
            ->paginate(15);

        return view('livewire.pages.admin.blog-posts', compact('posts'));
    }
}; ?>

<div>
    <x-slot:heading>Blog Posts</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search posts..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
                </div>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="general">General</option>
                    <option value="technical">Technical</option>
                    <option value="product">Product</option>
                    <option value="company">Company</option>
                </select>
                <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <button wire:click="openCreate" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Post
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Title</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Author</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Views</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($posts as $post)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-start gap-2">
                                    @if($post->is_featured)
                                        <span class="mt-0.5 text-amber-400 flex-shrink-0">⭐</span>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $post->title }}</p>
                                        <p class="text-xs font-mono text-slate-400">{{ $post->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $post->category }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-xs">{{ $post->author?->name ?? 'Admin' }}</td>
                            <td class="px-5 py-3 text-center text-slate-700">{{ number_format($post->views) }}</td>
                            <td class="px-5 py-3 text-center">
                                <button wire:click="toggleStatus({{ $post->id }})"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                               {{ $post->status === 'published' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    {{ $post->status === 'published' ? 'Published' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">
                                {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEdit({{ $post->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $post->id }})" wire:confirm="Delete this post?" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No blog posts yet. Write your first post!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($posts->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $posts->links() }}</div>@endif
        </div>
    </div>

    {{-- Slide-over Form --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showForm', false)"></div>
        <div class="relative ml-auto w-full max-w-2xl bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <h2 class="text-base font-semibold text-slate-900">{{ $editMode ? 'Edit Post' : 'New Blog Post' }}</h2>
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slug *</label>
                    <input wire:model="slug" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-mono focus:outline-none focus:border-cyan-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                        <select wire:model="category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="general">General</option>
                            <option value="technical">Technical</option>
                            <option value="product">Product</option>
                            <option value="company">Company</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Read Time (minutes)</label>
                        <input wire:model="read_time_minutes" type="number" min="1" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="is_featured" type="checkbox" class="rounded border-slate-300 text-cyan-600">
                            <span class="text-sm text-slate-700">Featured Post</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Excerpt</label>
                    <textarea wire:model="excerpt" rows="2" placeholder="Short summary shown in listings..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Content</label>
                    <livewire:markdown-editor 
                        wire:model="content" 
                        placeholder="Write your blog post content with markdown..."
                        :rows="15"
                        :show-toolbar="true"
                        :show-upload="true"
                    />
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">SEO Meta</p>
                    <div class="space-y-2">
                        <input wire:model="meta_title" type="text" placeholder="Meta title" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        <textarea wire:model="meta_description" rows="2" placeholder="Meta description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">
                        {{ $editMode ? 'Update Post' : 'Create Post' }}
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
