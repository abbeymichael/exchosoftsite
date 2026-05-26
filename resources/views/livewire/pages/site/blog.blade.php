<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Tech Blog — ExchoSoft')] class extends Component
{
    use WithPagination;
    public string $search = '';
    public string $filterCategory = '';

    public function render(): \Illuminate\View\View
    {
        $posts = BlogPost::published()->with('author')
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->latest('published_at')->paginate(9);

        return view('livewire.pages.site.blog', compact('posts'));
    }
}; ?>

<div>
    <section class="bg-slate-900 text-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Knowledge Hub</p>
            <h1 class="text-4xl font-bold mb-4">Tech Blog</h1>
            <p class="text-slate-400 max-w-xl mx-auto">Insights, tutorials, product updates, and industry perspectives from the ExchoSoft team.</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <div class="relative flex-1 sm:flex-none">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search posts..." class="pl-9 pr-4 py-2 w-full sm:w-60 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                    <option value="">All Categories</option>
                    <option value="general">General</option>
                    <option value="technical">Technical</option>
                    <option value="product">Product</option>
                    <option value="company">Company</option>
                </select>
            </div>

            @if($posts->isEmpty())
            <div class="text-center py-20 text-slate-400">
                <svg class="mx-auto h-14 w-14 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <p class="text-lg font-semibold">No posts yet</p>
                <p class="text-sm mt-1">Blog posts added from the admin will appear here.</p>
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="bg-slate-100 h-44 flex items-center justify-center overflow-hidden">
                        @if($post->cover_image)
                            <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold uppercase text-cyan-600 capitalize">{{ $post->category }}</span>
                            @if($post->is_featured)<span class="text-xs text-amber-500">⭐ Featured</span>@endif
                        </div>
                        <h3 class="font-bold text-slate-900 group-hover:text-cyan-700 transition-colors line-clamp-2">{{ $post->title }}</h3>
                        @if($post->excerpt)<p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $post->excerpt }}</p>@endif
                        <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                            <span>{{ $post->published_at?->format('d M Y') }}</span>
                            <span>·</span>
                            <span>{{ $post->read_time_minutes }} min read</span>
                            <span>·</span>
                            <span>{{ number_format($post->views) }} views</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($posts->hasPages())<div class="mt-10">{{ $posts->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
