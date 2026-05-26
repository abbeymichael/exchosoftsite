<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public BlogPost $post;

    public function mount(string $slug): void
    {
        $this->post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        // Increment views
        $this->post->increment('views');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.blog-post')->title($this->post->title . ' — ExchoSoft Blog');
    }
}; ?>

<div>
    <x-page-banner
        height="sm"
        :title="$post->title"
        :subtitle="$post->excerpt ?? null"
        :tag="$post->category ? ucfirst($post->category) : 'Blog'"
        :breadcrumbs="[
            ['label'=>'Home','route'=>'home'],
            ['label'=>'Blog','route'=>'site.blog'],
            ['label'=>$post->title],
        ]"
    />
    <article class="py-14">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            {{-- Meta --}}
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-semibold text-cyan-700 capitalize">{{ $post->category }}</span>
                <span class="text-xs text-slate-400">{{ $post->published_at?->format('d M Y') }}</span>
                <span class="text-xs text-slate-400">{{ $post->read_time_minutes }} min read</span>
                <span class="text-xs text-slate-400">{{ number_format($post->views) }} views</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 leading-tight mb-4">{{ $post->title }}</h1>

            @if($post->excerpt)
            <p class="text-lg text-slate-500 leading-relaxed mb-6">{{ $post->excerpt }}</p>
            @endif

            <div class="flex items-center gap-2 text-xs text-slate-500 mb-8 pb-8 border-b border-slate-100">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-cyan-500 text-white text-xs font-bold">
                    {{ strtoupper(substr($post->author?->name ?? 'E', 0, 1)) }}
                </div>
                <span>By <strong>{{ $post->author?->name ?? 'ExchoSoft Team' }}</strong></span>
            </div>

            @if($post->cover_image)
            <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="w-full rounded-2xl mb-8 object-cover aspect-video">
            @endif

            @if($post->content)
            <div class="prose prose-slate prose-lg max-w-none">
                {!! $post->content !!}
            </div>
            @else
            <div class="rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 p-12 text-center text-slate-400">
                <p>Post content is being written. Check back soon!</p>
            </div>
            @endif

            <div class="mt-12 pt-8 border-t border-slate-100">
                <a href="{{ route('site.blog') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Blog
                </a>
            </div>
        </div>
    </article>
</div>
