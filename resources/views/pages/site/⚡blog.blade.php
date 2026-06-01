<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Tech Blog — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    // Plain public property — avoids the #[Computed] serialisation issue in Volt
    public ?BlogPost $featuredPost = null;

    public function mount(): void
    {
        $this->featuredPost = BlogPost::query()
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->first();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        $query = BlogPost::query()
            ->where('published_at', '<=', now())
            ->latest('published_at');

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(6);
    }
}; ?>

<div>

{{-- ═══════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-[var(--navy)] px-6 pb-20 pt-28 md:px-24 border-b border-[rgba(0,184,219,0.1)]">

  {{-- radar sweep --}}
  <div class="pointer-events-none absolute left-1/2 top-1/2 h-[200%] w-[200%] -translate-x-1/2 -translate-y-1/2"
       style="background:conic-gradient(from 0deg at 50% 50%,rgba(76,217,253,0.08) 0deg,transparent 90deg);animation:blog-rotate 8s linear infinite;"></div>

  {{-- dot grid --}}
  <div class="pointer-events-none absolute inset-0"
       style="background-image:radial-gradient(circle,rgba(0,184,219,0.12) 1px,transparent 1px);background-size:32px 32px;"></div>

  {{-- particles --}}
  <div id="blog-particles-container" class="pointer-events-none absolute inset-0"></div>

  <div class="relative z-10 max-w-3xl">
    {{-- badge --}}
    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-[rgba(0,184,219,0.2)] bg-[rgba(0,184,219,0.1)] px-4 py-1.5">
      <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[var(--cyan)]"></span>
      <span class="text-[0.68rem] font-bold uppercase tracking-[0.2em] text-[var(--cyan)]">System Operational</span>
    </div>

    <h1 class="mb-6 font-[var(--font-display)] text-4xl font-extrabold leading-[1.1] tracking-tight text-white md:text-6xl">
      Insights <em class="text-[var(--cyan)] not-italic">Built From Here.</em>
    </h1>

    <p class="mb-8 max-w-xl text-[1.05rem] font-light leading-[1.8] text-white/55">
      Explorations into industrial-grade digital architecture, cross-continental connectivity, and the future of resilient technical systems.
    </p>

    <div class="flex flex-wrap gap-4">
      <a href="#blog-featured"
         class="inline-flex items-center gap-2 rounded-lg bg-[var(--cyan)] px-8 py-3.5 font-[var(--font-display)] text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-[var(--cyan-dark)]">
        Read Featured
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      </a>
      <a href="#blog-grid"
         class="rounded-lg border border-white/20 px-8 py-3.5 font-[var(--font-display)] text-sm font-bold text-white/80 transition hover:border-[var(--cyan)] hover:text-white">
        View All Posts
      </a>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════
     CATEGORY FILTER BAR
═══════════════════════════════════════════════ --}}
<div class="sticky top-[58px] z-50 overflow-x-auto border-b border-[var(--border)] bg-[var(--ice)]">
  <div class="mx-auto flex max-w-[1300px] px-6 md:px-24">
    @foreach([
        ''          => 'All Disciplines',
        'technical' => 'Architecture',
        'company'   => 'Case Studies',
        'general'   => 'Industry Trends',
        'product'   => 'Technical Deep-Dives',
    ] as $val => $label)
    <button
      wire:click="$set('filterCategory', '{{ $val }}')"
      class="whitespace-nowrap border-b-2 bg-transparent px-6 py-4 text-[0.82rem] font-semibold transition
             {{ $filterCategory === $val
                  ? 'border-[var(--cyan)] text-[var(--cyan)]'
                  : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text-primary)]' }}"
    >{{ $label }}</button>
    @endforeach
  </div>
</div>

{{-- ═══════════════════════════════════════════════
     FEATURED ARTICLE
═══════════════════════════════════════════════ --}}
@if($featuredPost)
<section id="blog-featured" class="bg-white px-6 py-20 md:px-24">
  <div class="mx-auto grid max-w-[1200px] items-center gap-16 lg:grid-cols-[7fr_5fr]">

    {{-- image --}}
    <a href="{{ route('site.blog.show', $featuredPost->slug) }}" wire:navigate
       class="group relative block overflow-hidden rounded-2xl">
      {{-- scan line --}}
      <div class="pointer-events-none absolute left-0 top-0 z-10 h-0.5 w-full"
           style="background:linear-gradient(90deg,transparent,var(--cyan),transparent);animation:blog-scan 3s linear infinite;"></div>

      @if($featuredPost->cover_image)
        <img src="{{ asset('storage/'.$featuredPost->cover_image) }}"
             alt="{{ $featuredPost->title }}"
             class="h-[420px] w-full object-cover transition duration-700 group-hover:scale-105">
      @else
        <div class="flex h-[420px] w-full items-center justify-center"
             style="background:linear-gradient(135deg,var(--navy),var(--navy-mid))">
          <svg width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.3)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        </div>
      @endif

      <div class="absolute bottom-5 left-5 flex gap-2">
        <span class="rounded bg-[var(--navy)] px-3 py-1 text-[0.7rem] font-bold uppercase tracking-[0.06em] text-white">
          {{ strtoupper($featuredPost->category ?? 'INSIGHTS') }}
        </span>
        <span class="rounded bg-[var(--cyan)] px-3 py-1 text-[0.7rem] font-bold text-white">
          {{ $featuredPost->read_time_minutes }} MIN READ
        </span>
      </div>
    </a>

    {{-- aside --}}
    <div>
      <p class="mb-4 text-[0.72rem] font-bold uppercase tracking-[0.2em] text-[var(--cyan)]">
        Featured Technical Insight
      </p>
      <h2 class="mb-5 font-[var(--font-display)] text-2xl font-bold leading-snug text-[var(--navy)] md:text-[2.2rem]">
        {{ $featuredPost->title }}
      </h2>
      @if($featuredPost->excerpt)
        <p class="mb-8 text-base leading-[1.75] text-[var(--text-secondary)]">
          {{ Str::limit($featuredPost->excerpt, 180) }}
        </p>
      @endif

      <div class="mb-8 flex items-center gap-3">
        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-[var(--navy)] font-[var(--font-display)] text-sm font-extrabold text-white">
          {{ strtoupper(substr($featuredPost->author?->name ?? 'E', 0, 2)) }}
        </div>
        <div>
          <div class="text-sm font-semibold text-[var(--text-primary)]">
            {{ $featuredPost->author?->name ?? 'Exchosoft Engineering Team' }}
          </div>
          <div class="text-xs text-[var(--text-muted)]">
            {{ $featuredPost->published_at?->format('F d, Y') }}
          </div>
        </div>
      </div>

      <a href="{{ route('site.blog.show', $featuredPost->slug) }}" wire:navigate
         class="group inline-flex items-center gap-3 rounded-lg bg-[var(--navy)] px-8 py-3.5 font-[var(--font-display)] text-sm font-bold text-white transition hover:bg-[var(--navy-mid)]">
        Full Article
        <svg class="transition-transform group-hover:translate-x-1" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>

  </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════
     BLOG GRID
═══════════════════════════════════════════════ --}}
<section id="blog-grid" class="relative overflow-hidden bg-[var(--ice)] px-6 py-16 md:px-24">

  {{-- glow blob --}}
  <div class="pointer-events-none absolute -right-40 -top-40 h-[500px] w-[500px] rounded-full bg-[rgba(0,184,219,0.06)] blur-[120px]"></div>

  {{-- header --}}
  <div class="mx-auto mb-12 flex max-w-[1200px] flex-wrap items-end justify-between gap-6">
    <div>
      <h3 class="mb-1.5 font-[var(--font-display)] text-2xl font-bold text-[var(--navy)] md:text-[2rem]">
        Latest Transmissions
      </h3>
      <p class="text-sm text-[var(--text-muted)]">Global perspectives from our consultants and engineers.</p>
    </div>
  </div>

  {{-- filters --}}
  <div class="mx-auto mb-10 flex max-w-[1200px] flex-wrap items-center gap-4">
    {{-- search --}}
    <div class="relative min-w-[180px] max-w-xs flex-1">
      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input
        wire:model.live.debounce.300ms="search"
        type="text"
        placeholder="Search posts…"
        class="w-full rounded-lg border border-[var(--border)] bg-white py-2.5 pl-10 pr-3.5 text-sm text-[var(--text-primary)] transition focus:border-[var(--cyan)] focus:outline-none"
      >
    </div>
    {{-- category select --}}
    <select
      wire:model.live="filterCategory"
      class="rounded-lg border border-[var(--border)] bg-white px-4 py-2.5 text-sm text-[var(--text-secondary)] focus:border-[var(--cyan)] focus:outline-none"
    >
      <option value="">All Categories</option>
      <option value="general">General</option>
      <option value="technical">Technical</option>
      <option value="product">Product</option>
      <option value="company">Company</option>
    </select>
  </div>

  {{-- posts --}}
  @if($this->posts->isEmpty())
    <div class="mx-auto max-w-[1200px] py-20 text-center text-[var(--text-muted)]">
      <p class="text-base font-semibold">No posts found</p>
      <p class="mt-2 text-sm">Try adjusting your search or category filter.</p>
    </div>
  @else
    <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-8 md:grid-cols-2">
      @foreach($this->posts as $post)
      <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate
         class="group block overflow-hidden rounded-2xl border-[1.5px] border-[rgba(0,184,219,0.18)] bg-white/65 text-inherit no-underline backdrop-blur-md transition hover:-translate-y-1.5 hover:border-[var(--cyan)] hover:shadow-[0_16px_40px_rgba(0,184,219,0.12)]">

        {{-- image --}}
        <div class="relative m-2 overflow-hidden rounded-xl">
          @if($post->cover_image)
            <img src="{{ asset('storage/'.$post->cover_image) }}"
                 alt="{{ $post->title }}"
                 class="block aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-[1.08]">
          @else
            <div class="flex aspect-[16/10] w-full items-center justify-center"
                 style="background:linear-gradient(135deg,var(--navy),var(--navy-mid))">
              <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.25)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
          @endif
          {{-- icon badge --}}
          <div class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 backdrop-blur transition group-hover:bg-[var(--cyan)] group-hover:text-white">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
          </div>
        </div>

        {{-- body --}}
        <div class="px-6 pb-3 pt-5">
          @if($post->category)
            <span class="mb-3 inline-block rounded-full bg-[rgba(0,184,219,0.1)] px-3 py-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.06em] text-[var(--cyan-deep)]">
              {{ $post->category }}
            </span>
          @endif
          <div class="mb-2 font-[var(--font-display)] text-[1.1rem] font-bold leading-snug text-[var(--navy)] transition group-hover:text-[var(--cyan-dark)]">
            {{ $post->title }}
          </div>
          @if($post->excerpt)
            <div class="line-clamp-3 text-[0.85rem] leading-[1.65] text-[var(--text-secondary)]">
              {{ $post->excerpt }}
            </div>
          @endif
        </div>

        {{-- footer --}}
        <div class="flex items-center justify-between border-t border-[var(--border)] px-6 py-3.5">
          <div class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            {{ $post->read_time_minutes }} min read
          </div>
          <div class="flex items-center gap-1 text-[0.8rem] font-bold text-[var(--navy)] transition group-hover:gap-2">
            Read More
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
          </div>
        </div>

      </a>
      @endforeach
    </div>

    @if($this->posts->hasPages())
      <div class="mx-auto mt-10 max-w-[1200px]">
        {{ $this->posts->links() }}
      </div>
    @endif
  @endif
</section>

{{-- ═══════════════════════════════════════════════
     NEWSLETTER CTA
═══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-[var(--navy)] px-6 py-20 text-center md:px-24">
  {{-- dot grid --}}
  <div class="pointer-events-none absolute inset-0 opacity-40"
       style="background-image:radial-gradient(circle,rgba(0,184,219,0.1) 1px,transparent 1px);background-size:28px 28px;"></div>

  <div class="relative z-10">
    {{-- badge --}}
    <div class="mb-6 inline-flex items-center gap-3 rounded-md border border-[rgba(0,184,219,0.25)] bg-[rgba(0,184,219,0.1)] px-4 py-2">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--cyan)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      <span class="text-[0.72rem] font-bold uppercase tracking-[0.15em] text-[var(--sky)]">The Transmission</span>
    </div>

    <h2 class="mb-3 font-[var(--font-display)] text-3xl font-extrabold tracking-tight text-white md:text-[2.4rem]">
      Stay Ahead of the Data Stream.
    </h2>
    <p class="mx-auto mb-10 max-w-[560px] text-[0.95rem] font-light leading-[1.75] text-white/50">
      Subscribe to our monthly briefing. High-fidelity insights on infrastructure, engineering, and digital strategy for industrial leaders.
    </p>

    <form class="relative z-10 mx-auto max-w-[440px]">
      <div class="flex items-center gap-2 rounded-xl border border-[rgba(0,184,219,0.25)] bg-[rgba(13,33,55,0.7)] p-1.5">
        <input
          type="email"
          placeholder="professional@email.com"
          class="flex-1 bg-transparent px-3 py-2.5 text-[0.9rem] text-white placeholder-white/30 outline-none"
        >
        <button
          type="submit"
          class="whitespace-nowrap rounded-lg bg-[var(--cyan)] px-5 py-2.5 font-[var(--font-display)] text-[0.82rem] font-bold text-white transition hover:bg-[var(--cyan-dark)]"
        >Subscribe</button>
      </div>
      <p class="mt-3 text-[0.72rem] text-white/30">Zero spam. Pure technical value. Unsubscribe anytime.</p>
    </form>
  </div>
</section>

{{-- keyframes for the hero decorations (only 3 tiny animations — not worth a full CSS file) --}}
<style>
@keyframes blog-rotate {
  from { transform: translate(-50%,-50%) rotate(0deg); }
  to   { transform: translate(-50%,-50%) rotate(360deg); }
}
@keyframes blog-scan {
  0%   { top: 0%; }
  100% { top: 100%; }
}
</style>

<script>
(function () {
  const container = document.getElementById('blog-particles-container');
  if (!container) return;
  for (let i = 0; i < 20; i++) {
    const dot = document.createElement('div');
    dot.style.cssText = `position:absolute;background:#4cd9fd;border-radius:50%;width:4px;height:4px;opacity:0.6;left:${Math.random()*100}%;top:${Math.random()*100}%`;
    const dx = (Math.random() - 0.5) * 300;
    const dy = (Math.random() - 0.5) * 300;
    dot.animate(
      [
        { transform: 'translate(0,0)',              opacity: 0   },
        { transform: `translate(${dx}px,${dy}px)`,  opacity: 0.6 },
        { transform: `translate(${dx*2}px,${dy*2}px)`, opacity: 0 },
      ],
      { duration: (5 + Math.random()*10)*1000, delay: Math.random()*5000, iterations: Infinity, easing: 'ease-in-out' }
    );
    container.appendChild(dot);
  }
})();
</script>
</div>
