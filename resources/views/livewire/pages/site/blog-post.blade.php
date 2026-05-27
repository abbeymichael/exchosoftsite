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

<style>
  .blog-post-article { padding: 4rem 6rem; }
  .blog-post-inner { max-width: 780px; margin: 0 auto; }
  .blog-post-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
  .blog-post-cat { background: rgba(0,184,219,0.1); color: var(--cyan-deep); padding: 0.25rem 0.75rem; border-radius: 100px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
  .blog-post-date { font-size: 0.8rem; color: var(--text-muted); }
  .blog-post-sep { color: var(--border); }
  .blog-author { display: flex; align-items: center; gap: 0.75rem; padding: 1.25rem 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); margin: 1.5rem 0; }
  .blog-author-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--cyan); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 0.8rem; font-weight: 800; color: var(--white); flex-shrink: 0; }
  .blog-author-name { font-size: 0.85rem; color: var(--text-primary); font-weight: 500; }
  .blog-author-role { font-size: 0.75rem; color: var(--text-muted); }
  .blog-cover { width: 100%; border-radius: 14px; margin-bottom: 2rem; aspect-ratio: 16/9; object-fit: cover; }
  .blog-post-content { color: var(--text-secondary); line-height: 1.85; font-size: 1rem; }
  .blog-post-content h1,.blog-post-content h2,.blog-post-content h3 { font-family: var(--font-display); font-weight: 700; color: var(--navy); letter-spacing: -0.02em; margin: 2rem 0 0.75rem; }
  .blog-post-content h2 { font-size: 1.4rem; }
  .blog-post-content h3 { font-size: 1.1rem; }
  .blog-post-content p { margin-bottom: 1.25rem; }
  .blog-post-content ul,.blog-post-content ol { padding-left: 1.5rem; margin-bottom: 1.25rem; }
  .blog-post-content li { margin-bottom: 0.4rem; line-height: 1.75; }
  .blog-post-content blockquote { border-left: 3px solid var(--cyan); padding: 0.75rem 1.25rem; margin: 1.5rem 0; background: var(--ice); border-radius: 0 8px 8px 0; }
  .blog-post-content blockquote p { color: var(--text-secondary); font-style: italic; margin: 0; }
  .blog-post-content code { background: var(--ice); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.88em; color: var(--cyan-deep); }
  .blog-post-content pre { background: var(--navy); color: var(--sky); padding: 1.25rem; border-radius: 10px; overflow-x: auto; margin-bottom: 1.25rem; }
  .blog-post-content pre code { background: none; color: inherit; padding: 0; }
  .blog-post-content a { color: var(--cyan); text-decoration: underline; }
  .blog-post-content strong { color: var(--navy); font-weight: 700; }
  .blog-post-empty { background: var(--ice); border: 2px dashed var(--border); border-radius: 12px; padding: 4rem 2rem; text-align: center; color: var(--text-muted); }
  .blog-post-back { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); text-decoration: none; transition: color 0.2s; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border); }
  .blog-post-back:hover { color: var(--cyan); }
  @media (max-width: 1024px) { .blog-post-article { padding: 3rem 2rem; } }
  @media (max-width: 640px) { .blog-post-article { padding: 2rem 1.25rem; } }
</style>

<article class="blog-post-article">
  <div class="blog-post-inner">
    {{-- Meta --}}
    <div class="blog-post-meta">
      @if($post->category)<span class="blog-post-cat">{{ $post->category }}</span>@endif
      <span class="blog-post-date">{{ $post->published_at?->format('d M Y') }}</span>
      <span class="blog-post-sep">·</span>
      <span class="blog-post-date">{{ $post->read_time_minutes }} min read</span>
      <span class="blog-post-sep">·</span>
      <span class="blog-post-date">{{ number_format($post->views) }} views</span>
    </div>

    {{-- Author --}}
    <div class="blog-author">
      <div class="blog-author-avatar">{{ strtoupper(substr($post->author?->name ?? 'E', 0, 1)) }}</div>
      <div>
        <div class="blog-author-name">{{ $post->author?->name ?? 'ExchoSoft Team' }}</div>
        <div class="blog-author-role">Exchosoft Consult</div>
      </div>
    </div>

    {{-- Cover Image --}}
    @if($post->cover_image)
    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="blog-cover">
    @endif

    {{-- Content --}}
    @if($post->content)
    <div class="blog-post-content">
      {!! $post->content !!}
    </div>
    @else
    <div class="blog-post-empty">
      <p style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">Content coming soon</p>
      <p style="font-size:0.875rem;">This post is being written. Check back soon!</p>
    </div>
    @endif

    {{-- Back link --}}
    <a href="{{ route('site.blog') }}" wire:navigate class="blog-post-back">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Blog
    </a>
  </div>
</article>
</div>
