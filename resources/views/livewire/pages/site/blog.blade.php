<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Tech Blog — Exchosoft Consult')] class extends Component
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
<style>
  .page-banner{min-height:380px;background:var(--navy);position:relative;overflow:hidden;display:flex;align-items:center;}
  .page-banner-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(0,184,219,0.14) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;}
  .page-banner-glow{position:absolute;inset:0;background:radial-gradient(circle at 75% 50%,rgba(0,184,219,0.1) 0%,transparent 60%);pointer-events:none;}
  .page-banner-content{position:relative;z-index:2;padding:4rem 6rem;max-width:700px;}
  .page-banner-crumb{display:flex;align-items:center;gap:0.5rem;margin-bottom:2rem;}
  .page-banner-crumb a{font-size:0.78rem;color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;}
  .page-banner-crumb a:hover{color:var(--cyan);}
  .page-banner-crumb .sep{color:rgba(255,255,255,0.2);}
  .page-banner-crumb .ccurrent{font-size:0.78rem;color:var(--cyan);font-weight:500;}
  .page-banner-tag{display:inline-flex;background:rgba(0,184,219,0.1);border:1px solid rgba(0,184,219,0.2);color:var(--sky);padding:0.28rem 0.85rem;border-radius:100px;font-size:0.72rem;font-weight:600;letter-spacing:0.06em;margin-bottom:1.25rem;text-transform:uppercase;}
  .page-banner h1{font-family:var(--font-display);font-size:clamp(2rem,3.8vw,3.2rem);font-weight:800;color:var(--white);line-height:1.1;letter-spacing:-0.03em;margin-bottom:1rem;}
  .page-banner h1 em{color:var(--cyan);font-style:normal;}
  .page-banner-sub{font-size:1rem;color:rgba(255,255,255,0.55);max-width:540px;line-height:1.75;font-weight:300;}
  .listing-body{padding:4rem 6rem;}
  .listing-filters{display:flex;flex-wrap:wrap;align-items:center;gap:1rem;margin-bottom:2.5rem;}
  .lf-input-wrap{position:relative;flex:1;min-width:180px;max-width:320px;}
  .lf-input-wrap svg{position:absolute;left:0.85rem;top:50%;transform:translateY(-50%);}
  .lf-input{width:100%;padding:0.65rem 0.85rem 0.65rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.875rem;font-family:var(--font-body);background:white;color:var(--text-primary);transition:border-color 0.2s;}
  .lf-input:focus{outline:none;border-color:var(--cyan);}
  .lf-select{padding:0.65rem 1rem;border:1px solid var(--border);border-radius:8px;font-size:0.875rem;font-family:var(--font-body);background:white;color:var(--text-secondary);}
  .lf-select:focus{outline:none;border-color:var(--cyan);}
  .listing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;}
  .blog-card{background:var(--white);border:1px solid var(--border);border-radius:14px;overflow:hidden;text-decoration:none;display:block;transition:border-color 0.2s,box-shadow 0.2s;}
  .blog-card:hover{border-color:var(--cyan);box-shadow:0 8px 24px rgba(0,184,219,0.1);}
  .blog-card-img{height:160px;background:var(--ice);display:flex;align-items:center;justify-content:center;overflow:hidden;}
  .blog-card-img img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s;}
  .blog-card:hover .blog-card-img img{transform:scale(1.04);}
  .blog-card-body{padding:1.25rem 1.5rem;}
  .blog-card-cat{font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--cyan);}
  .blog-card-title{font-family:var(--font-display);font-size:0.95rem;font-weight:700;color:var(--navy);margin:0.4rem 0 0.5rem;line-height:1.4;}
  .blog-card-excerpt{font-size:0.82rem;color:var(--text-secondary);line-height:1.65;}
  .blog-card-meta{font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;}
  .empty-state{text-align:center;padding:5rem 2rem;color:var(--text-muted);}
  .empty-state p:first-child{font-size:1rem;font-weight:600;}
  .empty-state p:last-child{font-size:0.875rem;margin-top:0.5rem;}
  @media(max-width:1024px){.page-banner-content{padding:3rem 2rem;}.listing-body{padding:2.5rem 2rem;}.listing-grid{grid-template-columns:1fr 1fr;}}
  @media(max-width:640px){.listing-grid{grid-template-columns:1fr;}}
</style>
<div class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-content">
    <div class="page-banner-crumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="ccurrent">Blog</span></div>
    <div class="page-banner-tag">Knowledge Hub</div>
    <h1>Insights from the <em>Exchosoft Team</em></h1>
    <p class="page-banner-sub">Tutorials, product updates, industry perspectives, and technology insights — straight from the people building software for Africa and beyond.</p>
  </div>
</div>

    <section class="listing-body">
        <div>
            <div class="listing-filters">
              <div class="lf-input-wrap">
                <svg style="width:16px;height:16px;color:var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search posts..." class="lf-input">
              </div>
              <select wire:model.live="filterCategory" class="lf-select">
                <option value="">All Categories</option>
                <option value="general">General</option>
                <option value="technical">Technical</option>
                <option value="product">Product</option>
                <option value="company">Company</option>
              </select>
            </div>

            @if($posts->isEmpty())
            <div class="empty-state"><p>No posts yet</p><p>Blog posts added from the admin will appear here.</p></div>
            @else
            <div class="listing-grid">
              @foreach($posts as $post)
              <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate class="blog-card">
                <div class="blog-card-img">
                  @if($post->cover_image)<img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}">
                  @else<svg style="width:40px;height:40px;color:var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>@endif
                </div>
                <div class="blog-card-body">
                  <div class="blog-card-cat">{{ $post->category }}</div>
                  <div class="blog-card-title">{{ $post->title }}</div>
                  @if($post->excerpt)<div class="blog-card-excerpt">{{ Str::limit($post->excerpt, 100) }}</div>@endif
                  <div class="blog-card-meta">{{ $post->published_at?->format('d M Y') }} &middot; {{ $post->read_time_minutes }} min read</div>
                </div>
              </a>
              @endforeach
            </div>
            @if($posts->hasPages())<div style="margin-top:2rem;">{{ $posts->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
