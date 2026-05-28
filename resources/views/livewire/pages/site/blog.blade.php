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

        return view('pages.site.blog', compact('posts'));
    }
}; ?>

<div>
<style>
/* ── BLOG PAGE ── */
/* HERO */
.blog-hero {
  position: relative; overflow: hidden;
  background: var(--navy); padding: 7rem 6rem 5rem;
  border-bottom: 1px solid rgba(0,184,219,0.1);
}
.blog-hero-radar-sweep {
  position: absolute; top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  width: 200%; height: 200%;
  background: conic-gradient(from 0deg at 50% 50%, rgba(76,217,253,0.08) 0deg, transparent 90deg);
  animation: blog-rotate 8s linear infinite;
  pointer-events: none;
}
@keyframes blog-rotate {
  from { transform: translate(-50%,-50%) rotate(0deg); }
  to { transform: translate(-50%,-50%) rotate(360deg); }
}
.blog-hero-dots {
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(0,184,219,0.12) 1px, transparent 1px);
  background-size: 32px 32px; pointer-events: none;
}
.blog-hero-particles { position: absolute; inset: 0; pointer-events: none; }
.blog-hero-content { position: relative; z-index: 10; max-width: 760px; }
.blog-hero-badge {
  display: inline-flex; align-items: center; gap: 0.6rem;
  padding: 0.3rem 0.9rem; border-radius: 100px; margin-bottom: 1.5rem;
  background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
}
.blog-hero-badge-dot { width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; animation: blog-ping 1.5s ease-in-out infinite; }
@keyframes blog-ping { 0%,100%{opacity:1} 50%{opacity:0.3} }
.blog-hero-badge-txt { font-size: 0.68rem; font-weight: 700; color: var(--cyan); letter-spacing: 0.2em; text-transform: uppercase; }
.blog-hero h1 {
  font-family: var(--font-display); font-size: clamp(2.4rem, 5vw, 3.8rem);
  font-weight: 800; color: var(--white); line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1.5rem;
}
.blog-hero h1 em { color: var(--cyan); font-style: italic; }
.blog-hero-sub { font-size: 1.05rem; color: rgba(255,255,255,0.55); max-width: 560px; line-height: 1.8; font-weight: 300; margin-bottom: 2rem; }
.blog-hero-actions { display: flex; flex-wrap: wrap; gap: 1rem; }
.blog-hero-btn-primary {
  background: var(--cyan); color: white; padding: 0.9rem 2rem; border-radius: 8px;
  font-family: var(--font-display); font-weight: 700; font-size: 0.88rem;
  text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
  transition: background 0.2s, transform 0.15s;
}
.blog-hero-btn-primary:hover { background: var(--cyan-dark); transform: translateY(-2px); }
.blog-hero-btn-outline {
  border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.8);
  padding: 0.9rem 2rem; border-radius: 8px;
  font-family: var(--font-display); font-weight: 700; font-size: 0.88rem;
  text-decoration: none; transition: border-color 0.2s, color 0.2s;
}
.blog-hero-btn-outline:hover { border-color: var(--cyan); color: white; }

/* CATEGORY FILTER */
.blog-cat-filter {
  background: var(--ice); border-bottom: 1px solid var(--border);
  position: sticky; top: 58px; z-index: 50;
  overflow-x: auto;
}
.blog-cat-inner {
  max-width: 1300px; margin: 0 auto;
  display: flex; gap: 0; padding: 0 6rem;
}
.blog-cat-btn {
  padding: 1rem 1.5rem; font-size: 0.82rem; font-weight: 600;
  color: var(--text-muted); border-bottom: 2px solid transparent;
  background: none; border-top: none; border-left: none; border-right: none;
  cursor: pointer; transition: color 0.2s, border-color 0.2s; white-space: nowrap;
  font-family: var(--font-body);
}
.blog-cat-btn:hover { color: var(--text-primary); }
.blog-cat-btn.active { color: var(--cyan); border-bottom-color: var(--cyan); }

/* FEATURED ARTICLE */
.blog-featured { padding: 5rem 6rem; background: var(--white); }
.blog-featured-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 7fr 5fr; gap: 4rem; align-items: center; }
.blog-featured-img {
  position: relative; overflow: hidden; border-radius: 14px; cursor: pointer;
}
.blog-featured-img img { width: 100%; height: 420px; object-fit: cover; transition: transform 0.7s ease; }
.blog-featured-img:hover img { transform: scale(1.05); }
.blog-feat-scan { position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg,transparent,var(--cyan),transparent); animation: blog-scan 3s linear infinite; }
@keyframes blog-scan { 0%{top:0%} 100%{top:100%} }
.blog-feat-badges { position: absolute; bottom: 1.25rem; left: 1.25rem; display: flex; gap: 0.5rem; }
.blog-feat-badge-dark { background: var(--navy); color: white; padding: 0.3rem 0.8rem; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; border-radius: 4px; }
.blog-feat-badge-cyan { background: var(--cyan); color: white; padding: 0.3rem 0.8rem; font-size: 0.7rem; font-weight: 700; border-radius: 4px; }
.blog-featured-aside {}
.blog-feat-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--cyan); margin-bottom: 1rem; }
.blog-feat-title { font-family: var(--font-display); font-size: clamp(1.5rem,2.5vw,2.2rem); font-weight: 700; color: var(--navy); margin-bottom: 1.25rem; line-height: 1.25; }
.blog-feat-excerpt { font-size: 1rem; color: var(--text-secondary); line-height: 1.75; margin-bottom: 2rem; }
.blog-feat-author { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2rem; }
.blog-feat-avatar { width: 42px; height: 42px; border-radius: 50%; background: var(--navy); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 0.85rem; font-weight: 800; color: white; flex-shrink: 0; }
.blog-feat-author-name { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
.blog-feat-author-date { font-size: 0.75rem; color: var(--text-muted); }
.blog-feat-read-btn {
  display: inline-flex; align-items: center; gap: 0.75rem;
  background: var(--navy); color: white; padding: 0.85rem 2rem;
  border-radius: 8px; font-family: var(--font-display); font-weight: 700; font-size: 0.88rem;
  text-decoration: none; transition: background 0.2s, gap 0.2s; border: none; cursor: pointer;
}
.blog-feat-read-btn:hover { background: var(--navy-mid); gap: 1.1rem; }

/* BLOG GRID */
.blog-listing { padding: 4rem 6rem 5rem; background: var(--ice); position: relative; overflow: hidden; }
.blog-listing::before {
  content: ''; position: absolute; top: -10rem; right: -10rem;
  width: 500px; height: 500px; background: rgba(0,184,219,0.06);
  filter: blur(120px); border-radius: 50%; pointer-events: none;
}
.blog-listing-header { max-width: 1200px; margin: 0 auto 3rem; display: flex; align-items: flex-end; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; }
.blog-listing-h3 { font-family: var(--font-display); font-size: clamp(1.5rem,2.5vw,2rem); font-weight: 700; color: var(--navy); margin-bottom: 0.35rem; }
.blog-listing-sub { font-size: 0.9rem; color: var(--text-muted); }
.listing-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 2.5rem; max-width: 1200px; margin-left: auto; margin-right: auto; }
.lf-input-wrap { position: relative; flex: 1; min-width: 180px; max-width: 320px; }
.lf-input-wrap svg { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); }
.lf-input { width:100%;padding:0.65rem 0.85rem 0.65rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.875rem;font-family:var(--font-body);background:white;color:var(--text-primary);transition:border-color 0.2s; }
.lf-input:focus { outline:none;border-color:var(--cyan); }
.lf-select { padding:0.65rem 1rem;border:1px solid var(--border);border-radius:8px;font-size:0.875rem;font-family:var(--font-body);background:white;color:var(--text-secondary); }
.lf-select:focus { outline:none;border-color:var(--cyan); }

.listing-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 3rem; max-width: 1200px; margin: 0 auto; }
.blog-card {
  background: rgba(255,255,255,0.65); backdrop-filter: blur(16px);
  border: 1.5px solid rgba(0,184,219,0.18); border-radius: 14px;
  overflow: hidden; text-decoration: none; display: block;
  transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
}
.blog-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,184,219,0.12); border-color: var(--cyan); }
.blog-card-img { position: relative; overflow: hidden; border-radius: 10px; margin: 0.5rem; }
.blog-card-img img { width: 100%; aspect-ratio: 16/10; object-fit: cover; transition: transform 0.5s; display: block; }
.blog-card:hover .blog-card-img img { transform: scale(1.08); }
.blog-card-img-placeholder { width: 100%; aspect-ratio: 16/10; background: linear-gradient(135deg, var(--navy), var(--navy-mid)); display: flex; align-items: center; justify-content: center; }
.blog-card-icon-wrap {
  position: absolute; top: 0.75rem; right: 0.75rem;
  width: 40px; height: 40px; background: rgba(255,255,255,0.9);
  backdrop-filter: blur(8px); border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s;
}
.blog-card:hover .blog-card-icon-wrap { background: var(--cyan); color: white; }
.blog-card-body { padding: 1.25rem 1.5rem 0.75rem; }
.blog-card-cat {
  display: inline-block; padding: 0.2rem 0.75rem; border-radius: 100px; margin-bottom: 0.75rem;
  background: rgba(0,184,219,0.1); color: var(--cyan-deep);
  font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
}
.blog-card-title { font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.6rem; line-height: 1.35; transition: color 0.2s; }
.blog-card:hover .blog-card-title { color: var(--cyan-dark); }
.blog-card-excerpt { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.65; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.blog-card-footer { padding: 0.85rem 1.5rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.blog-card-meta { display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--text-muted); }
.blog-card-read-more { font-size: 0.8rem; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: 0.25rem; transition: gap 0.2s; }
.blog-card:hover .blog-card-read-more { gap: 0.5rem; }

.empty-state{text-align:center;padding:5rem 2rem;color:var(--text-muted);}
.empty-state p:first-child{font-size:1rem;font-weight:600;}
.empty-state p:last-child{font-size:0.875rem;margin-top:0.5rem;}

/* NEWSLETTER */
.blog-newsletter {
  background: var(--navy); padding: 5rem 6rem; text-align: center;
  position: relative; overflow: hidden;
}
.blog-newsletter-dots {
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(0,184,219,0.1) 1px, transparent 1px);
  background-size: 28px 28px; opacity: 0.4; pointer-events: none;
}
.blog-newsletter-badge {
  display: inline-flex; align-items: center; gap: 0.75rem;
  padding: 0.5rem 1rem; border-radius: 6px;
  background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.25); margin-bottom: 1.5rem;
}
.blog-newsletter-badge svg { color: var(--cyan); }
.blog-newsletter-badge-txt { font-size: 0.72rem; font-weight: 700; color: var(--sky); letter-spacing: 0.15em; text-transform: uppercase; }
.blog-newsletter h2 { font-family: var(--font-display); font-size: clamp(1.6rem,2.8vw,2.4rem); font-weight: 800; color: var(--white); letter-spacing: -0.03em; margin-bottom: 0.75rem; position: relative; z-index: 1; }
.blog-newsletter p { font-size: 0.95rem; color: rgba(255,255,255,0.5); max-width: 560px; margin: 0 auto 2.5rem; font-weight: 300; position: relative; z-index: 1; line-height: 1.75; }

@media(max-width:1024px){
  .blog-hero { padding: 6rem 2rem 4rem; }
  .blog-cat-inner { padding: 0 2rem; }
  .blog-featured { padding: 3.5rem 2rem; }
  .blog-featured-inner { grid-template-columns: 1fr; gap: 2.5rem; }
  .blog-featured-img img { height: 280px; }
  .blog-listing { padding: 3rem 2rem 4rem; }
  .listing-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
  .blog-newsletter { padding: 3.5rem 2rem; }
}
@media(max-width:640px){
  .blog-hero h1 { font-size: clamp(1.8rem,8vw,2.4rem); }
  .listing-grid { grid-template-columns: 1fr; }
  .blog-listing-header { flex-direction: column; align-items: flex-start; }
}
</style>

{{-- ── HERO ── --}}
<section class="blog-hero">
  <div class="blog-hero-radar-sweep"></div>
  <div class="blog-hero-dots"></div>
  <div class="blog-hero-particles" id="blog-particles-container"></div>
  <div class="blog-hero-content">
    <div class="blog-hero-badge">
      <span class="blog-hero-badge-dot"></span>
      <span class="blog-hero-badge-txt">System Operational</span>
    </div>
    <h1>Insights <em>Built From Here.</em></h1>
    <p class="blog-hero-sub">Explorations into industrial-grade digital architecture, cross-continental connectivity, and the future of resilient technical systems.</p>
    <div class="blog-hero-actions">
      <a href="#blog-featured" class="blog-hero-btn-primary">Read Featured
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      </a>
      <a href="#blog-grid" class="blog-hero-btn-outline">View All Posts</a>
    </div>
  </div>
</section>

{{-- ── CATEGORY FILTER ── --}}
<div class="blog-cat-filter">
  <div class="blog-cat-inner">
    <button class="blog-cat-btn active" onclick="filterBlogCat(this,'')">All Disciplines</button>
    <button class="blog-cat-btn" onclick="filterBlogCat(this,'technical')">Architecture</button>
    <button class="blog-cat-btn" onclick="filterBlogCat(this,'company')">Case Studies</button>
    <button class="blog-cat-btn" onclick="filterBlogCat(this,'general')">Industry Trends</button>
    <button class="blog-cat-btn" onclick="filterBlogCat(this,'product')">Technical Deep-Dives</button>
  </div>
</div>

{{-- ── FEATURED ARTICLE (first post) ── --}}
@if($posts->isNotEmpty())
@php $featured = $posts->first(); @endphp
<section class="blog-featured" id="blog-featured">
  <div class="blog-featured-inner">
    <div>
      <a href="{{ route('site.blog.show', $featured->slug) }}" wire:navigate class="blog-featured-img">
        <div class="blog-feat-scan"></div>
        @if($featured->cover_image)
          <img src="{{ asset('storage/'.$featured->cover_image) }}" alt="{{ $featured->title }}">
        @else
          <div style="height:420px;background:linear-gradient(135deg,var(--navy),var(--navy-mid));display:flex;align-items:center;justify-content:center;">
            <svg width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.3)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
          </div>
        @endif
        <div class="blog-feat-badges">
          <span class="blog-feat-badge-dark">{{ strtoupper($featured->category ?? 'INSIGHTS') }}</span>
          <span class="blog-feat-badge-cyan">{{ $featured->read_time_minutes }} MIN READ</span>
        </div>
      </a>
    </div>
    <div class="blog-featured-aside">
      <p class="blog-feat-label">Featured Technical Insight</p>
      <h2 class="blog-feat-title">{{ $featured->title }}</h2>
      @if($featured->excerpt)<p class="blog-feat-excerpt">{{ Str::limit($featured->excerpt, 180) }}</p>@endif
      <div class="blog-feat-author">
        <div class="blog-feat-avatar">{{ strtoupper(substr($featured->author?->name ?? 'E', 0, 2)) }}</div>
        <div>
          <div class="blog-feat-author-name">{{ $featured->author?->name ?? 'Exchosoft Engineering Team' }}</div>
          <div class="blog-feat-author-date">{{ $featured->published_at?->format('F d, Y') }}</div>
        </div>
      </div>
      <a href="{{ route('site.blog.show', $featured->slug) }}" wire:navigate class="blog-feat-read-btn">
        Full Article
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
@endif

{{-- ── BLOG GRID ── --}}
<section class="blog-listing" id="blog-grid">
  <div class="blog-listing-header">
    <div>
      <h3 class="blog-listing-h3">Latest Transmissions</h3>
      <p class="blog-listing-sub">Global perspectives from our consultants and engineers.</p>
    </div>
  </div>
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
        @if($post->cover_image)
          <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}">
        @else
          <div class="blog-card-img-placeholder">
            <svg style="width:40px;height:40px;" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.25)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
          </div>
        @endif
        <div class="blog-card-icon-wrap">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
        </div>
      </div>
      <div class="blog-card-body">
        @if($post->category)<span class="blog-card-cat">{{ $post->category }}</span>@endif
        <div class="blog-card-title">{{ $post->title }}</div>
        @if($post->excerpt)<div class="blog-card-excerpt">{{ $post->excerpt }}</div>@endif
      </div>
      <div class="blog-card-footer">
        <div class="blog-card-meta">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ $post->read_time_minutes }} min read
        </div>
        <div class="blog-card-read-more">Read More
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
        </div>
      </div>
    </a>
    @endforeach
  </div>
  @if($posts->hasPages())<div style="margin-top:2.5rem;max-width:1200px;margin-left:auto;margin-right:auto;">{{ $posts->links() }}</div>@endif
  @endif
</section>

{{-- Newsletter CTA --}}
<section class="blog-newsletter">
  <div class="blog-newsletter-dots"></div>
  <div style="position:relative;z-index:1;">
    <div class="blog-newsletter-badge">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--cyan)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      <span class="blog-newsletter-badge-txt">The Transmission</span>
    </div>
    <h2>Stay Ahead of the Data Stream.</h2>
    <p>Subscribe to our monthly briefing. High-fidelity insights on infrastructure, engineering, and digital strategy for industrial leaders.</p>
    <form style="max-width:440px;margin:0 auto;position:relative;z-index:1;">
      <div style="background:rgba(13,33,55,0.7);border:1px solid rgba(0,184,219,0.25);border-radius:10px;display:flex;align-items:center;gap:0.5rem;padding:0.35rem;">
        <input type="email" placeholder="professional@email.com" style="background:transparent;border:none;flex:1;padding:0.6rem 0.75rem;color:white;font-family:var(--font-body);font-size:0.9rem;outline:none;">
        <button type="submit" style="background:var(--cyan);color:white;border:none;border-radius:8px;padding:0.65rem 1.25rem;font-family:var(--font-display);font-size:0.82rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:background 0.2s;" onmouseover="this.style.background='var(--cyan-dark)'" onmouseout="this.style.background='var(--cyan)'">Subscribe</button>
      </div>
      <p style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-top:0.75rem;">Zero spam. Pure technical value. Unsubscribe anytime.</p>
    </form>
  </div>
</section>

<script>
// Blog particle system
(function(){
  const container = document.getElementById('blog-particles-container');
  if (!container) return;
  const count = 20;
  for(let i=0;i<count;i++){
    const dot = document.createElement('div');
    dot.style.cssText = `position:absolute;background:#4cd9fd;border-radius:50%;width:4px;height:4px;opacity:0.6;left:${Math.random()*100}%;top:${Math.random()*100}%`;
    const dx = (Math.random()-0.5)*300, dy = (Math.random()-0.5)*300;
    const dur = (5+Math.random()*10)*1000, del = Math.random()*5000;
    dot.animate([
      {transform:'translate(0,0)',opacity:0},
      {transform:`translate(${dx}px,${dy}px)`,opacity:0.6},
      {transform:`translate(${dx*2}px,${dy*2}px)`,opacity:0}
    ],{duration:dur,delay:del,iterations:Infinity,easing:'ease-in-out'});
    container.appendChild(dot);
  }
})();

function filterBlogCat(btn, cat){
  document.querySelectorAll('.blog-cat-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
</div>
