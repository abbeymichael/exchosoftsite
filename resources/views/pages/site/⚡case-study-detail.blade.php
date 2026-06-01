<?php

use App\Models\CaseStudy;
use League\CommonMark\CommonMarkConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public CaseStudy $study;

    public function mount(string $slug): void
    {
        $this->study = CaseStudy::published()->where('slug', $slug)->firstOrFail();

        $converter = new CommonMarkConverter([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Parse every field that accepts markdown input
        foreach (['challenge', 'solution', 'results', 'content'] as $field) {
            if (!empty($this->study->{$field})) {
                $this->study->{$field} = $converter->convert($this->study->{$field})->getContent();
            }
        }
    }

    public function title(): string
    {
        return $this->study->title . ' — ExchoSoft';
    }
}; ?>

<div>

{{-- ═══════════════════════════════════════════════
     HERO BANNER
═══════════════════════════════════════════════ --}}
<section class="relative flex min-h-[440px] max-h-[680px] items-end overflow-hidden bg-[var(--navy)]" style="height:70vh;">

  @if($study->cover_image ?? false)
    <img src="{{ asset('storage/'.$study->cover_image) }}" alt="{{ $study->title }}"
         class="absolute inset-0 h-full w-full object-cover">
  @elseif($study->client_logo ?? false)
    <img src="{{ asset('storage/'.$study->client_logo) }}" alt="{{ $study->client_name }}"
         class="absolute inset-0 h-full w-full object-cover">
  @endif

  {{-- gradient overlay --}}
  <div class="absolute inset-0"
       style="background:linear-gradient(to top,rgba(13,33,55,0.92) 0%,rgba(13,33,55,0.35) 55%,transparent 100%);"></div>

  <div class="relative z-10 w-full max-w-[1200px] px-6 pb-14 md:px-24">
    <span class="mb-5 inline-block rounded-full border border-[rgba(0,184,219,0.25)] bg-[rgba(0,184,219,0.15)] px-4 py-1.5 text-[0.7rem] font-bold uppercase tracking-[0.1em] text-[var(--cyan)]">
      Case Study Detail
    </span>
    <h1 class="mb-6 font-[var(--font-display)] text-3xl font-extrabold leading-[1.15] tracking-[-0.03em] text-white md:text-[3rem]">
      {{ $study->title }}
    </h1>
    <div class="flex flex-wrap items-center gap-5">
      @if($study->client_name)
        <div class="flex items-center gap-1.5 text-[0.8rem] text-white/65">
          <span class="material-symbols-outlined text-[0.95rem]">business</span>
          {{ $study->client_name }}
        </div>
      @endif
      @if($study->client_industry)
        <div class="flex items-center gap-1.5 text-[0.8rem] text-white/65">
          <span class="material-symbols-outlined text-[0.95rem]">category</span>
          {{ $study->client_industry }}
        </div>
      @endif
      @if(!empty($study->client_location))
        <div class="flex items-center gap-1.5 text-[0.8rem] text-white/65">
          <span class="material-symbols-outlined text-[0.95rem]">location_on</span>
          {{ $study->client_location }}
        </div>
      @endif
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════
     MAIN CONTENT GRID
═══════════════════════════════════════════════ --}}
<div class="mx-auto grid max-w-[1440px] grid-cols-1 gap-12 px-6 py-16 md:px-24 lg:grid-cols-[8fr_4fr]"
     style="background-image:radial-gradient(circle,rgba(0,184,219,0.08) 1px,transparent 1px);background-size:28px 28px;">

  {{-- ── EDITORIAL ── --}}
  <div>

    {{-- Metrics grid --}}
    @if($study->metrics && count($study->metrics))
    <div class="mb-10 grid grid-cols-2 gap-5">
      @foreach($study->metrics as $m)
      <div class="flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--ice)] p-8">
        <span class="font-[var(--font-display)] text-[2.5rem] font-extrabold tracking-[-0.04em] text-[var(--cyan)]">
          {{ $m['value'] ?? '' }}
        </span>
        <div>
          <div class="mt-3 font-[var(--font-display)] text-base font-bold text-[var(--navy)]">{{ $m['label'] ?? '' }}</div>
        </div>
      </div>
      @endforeach
    </div>
    @endif

    {{-- Challenge --}}
    @if($study->challenge)
    <div class="mb-8">
      <div class="mb-5 flex items-center gap-4">
        <span class="rounded-full bg-[rgba(0,184,219,0.1)] px-3 py-1 text-[0.68rem] font-extrabold uppercase tracking-[0.1em] text-[var(--cyan-deep)]">Challenge</span>
        <div class="h-px flex-1 bg-[var(--border)]"></div>
      </div>
      <div class="relative overflow-hidden rounded-2xl border-[1.5px] border-[rgba(0,184,219,0.18)] bg-white/70 p-10 backdrop-blur-md">
        <div class="pointer-events-none absolute inset-0"
             style="background:linear-gradient(90deg,transparent,rgba(0,184,219,0.06),transparent);animation:csd-sweep 4s infinite;"></div>
        <div class="csd-prose relative">{!! $study->challenge !!}</div>
      </div>
    </div>
    @endif

    {{-- Solution --}}
    @if($study->solution)
    <div class="mb-8">
      <div class="mb-5 flex items-center gap-4">
        <span class="rounded-full bg-[rgba(0,184,219,0.1)] px-3 py-1 text-[0.68rem] font-extrabold uppercase tracking-[0.1em] text-[var(--cyan-deep)]">Solution</span>
        <div class="h-px flex-1 bg-[var(--border)]"></div>
      </div>
      <div class="rounded-r-xl border border-[var(--border)] border-l-[4px] border-l-[var(--navy)] bg-[var(--ice)] px-7 py-6">
        <div class="csd-prose">{!! $study->solution !!}</div>
      </div>
    </div>
    @endif

    {{-- Results --}}
    @if($study->results)
    <div class="mb-8">
      <div class="mb-5 flex items-center gap-4">
        <span class="rounded-full bg-[rgba(0,184,219,0.1)] px-3 py-1 text-[0.68rem] font-extrabold uppercase tracking-[0.1em] text-[var(--cyan-deep)]">Results</span>
        <div class="h-px flex-1 bg-[var(--border)]"></div>
      </div>
      {{-- metrics grid shown above if available; results field is free-form markdown --}}
      <div class="rounded-2xl border-[1.5px] border-[rgba(0,184,219,0.2)] bg-white/70 p-8 backdrop-blur-md">
        <div class="csd-prose">{!! $study->results !!}</div>
        <div class="mt-6 flex items-center gap-4 border-t border-[rgba(0,184,219,0.12)] pt-6">
          <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-[var(--cyan)] font-[var(--font-display)] text-[0.85rem] font-extrabold text-white">
            {{ strtoupper(substr($study->client_name ?? 'CL', 0, 2)) }}
          </div>
          <div>
            <div class="font-[var(--font-display)] text-[0.88rem] font-bold text-[var(--navy)]">{{ $study->client_name ?? 'Client' }}</div>
            <div class="mt-0.5 text-[0.72rem] uppercase tracking-[0.04em] text-[var(--text-muted)]">{{ $study->client_industry ?? 'Organization' }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Full rich content --}}
    @if($study->content)
    <div class="csd-prose mb-8">{!! $study->content !!}</div>
    @endif

    {{-- Action buttons --}}
    <div class="mt-10 flex flex-wrap gap-3 border-t border-[var(--border)] pt-8">
      <a href="{{ route('site.case-studies') }}" wire:navigate
         class="inline-flex items-center gap-2 rounded-xl border-[1.5px] border-[var(--border)] bg-white px-5 py-2.5 text-[0.85rem] font-semibold text-[var(--text-secondary)] no-underline transition hover:border-[var(--cyan)] hover:text-[var(--cyan)]">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        All Case Studies
      </a>
      <a href="{{ route('site.consulting') }}" wire:navigate
         class="inline-flex items-center gap-2 rounded-xl bg-[var(--cyan)] px-6 py-2.5 text-[0.85rem] font-bold text-white no-underline transition hover:-translate-y-px hover:bg-[var(--cyan-dark)]">
        Work With Us
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>

  {{-- ── TECH SIDEBAR ── --}}
  <aside class="lg:sticky lg:top-20 lg:h-fit flex flex-col gap-6">

    {{-- tech stack box --}}
    @php
      $techFields = [
        'Architecture'  => $study->architecture  ?? null,
        'Technologies'  => is_array($study->technologies) ? implode(', ', $study->technologies) : ($study->technologies ?? null),
        'Platform'      => $study->platform      ?? null,
        'Integration'   => $study->integration   ?? null,
        'Duration'      => $study->duration      ?? null,
        'Team Size'     => $study->team_size     ?? null,
      ];
      $techFields = array_filter($techFields);
    @endphp

    <div class="relative overflow-hidden rounded-2xl bg-[var(--navy)] p-8 text-white">
      <div class="pointer-events-none absolute inset-0"
           style="background:radial-gradient(ellipse at top left,rgba(0,184,219,0.12) 0%,transparent 60%);"></div>
      <div class="relative mb-6 flex items-center gap-2 text-[0.68rem] font-extrabold uppercase tracking-[0.12em] text-white/45">
        <span class="material-symbols-outlined text-[1rem] text-[var(--cyan)]">terminal</span>
        Technical Stack
      </div>

      @if(count($techFields) > 0)
        @foreach($techFields as $key => $val)
        <div class="relative border-b border-white/[0.07] py-3.5 last:border-b-0">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">{{ $key }}</div>
          <div class="text-[0.88rem] font-semibold text-white">{{ $val }}</div>
        </div>
        @endforeach
      @else
        @if($study->client_name)
        <div class="relative border-b border-white/[0.07] py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Client</div>
          <div class="text-[0.88rem] font-semibold text-white">{{ $study->client_name }}</div>
        </div>
        @endif
        @if($study->client_industry)
        <div class="relative border-b border-white/[0.07] py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Industry</div>
          <div class="text-[0.88rem] font-semibold text-white">{{ $study->client_industry }}</div>
        </div>
        @endif
        @if($study->published_at)
        <div class="relative border-b border-white/[0.07] py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Published</div>
          <div class="text-[0.88rem] font-semibold text-white">{{ $study->published_at->format('M Y') }}</div>
        </div>
        @endif
        <div class="relative border-b border-white/[0.07] py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Architecture</div>
          <div class="text-[0.88rem] font-semibold text-white">Offline-First PWA &amp; Mesh Nodes</div>
        </div>
        <div class="relative border-b border-white/[0.07] py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Synchronization</div>
          <div class="text-[0.88rem] font-semibold text-white">Real-time Cloud Sync / Conflict Resolvers</div>
        </div>
        <div class="relative py-3.5">
          <div class="mb-0.5 text-[0.68rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">Security</div>
          <div class="text-[0.88rem] font-semibold text-white">Encrypted Local Storage</div>
        </div>
      @endif

      <a href="{{ route('site.consulting') }}" wire:navigate
         class="relative mt-6 flex w-full items-center justify-center gap-2 rounded-xl border border-[rgba(0,184,219,0.25)] bg-[rgba(0,184,219,0.15)] py-3.5 font-[var(--font-display)] text-[0.82rem] font-bold text-[var(--cyan)] no-underline transition hover:bg-[rgba(0,184,219,0.25)]">
        <span class="material-symbols-outlined text-[0.9rem]">download</span>
        Download Tech Specs
      </a>
    </div>

    {{-- tags --}}
    @php
      $tags = [];
      if(is_array($study->tags)) $tags = $study->tags;
      elseif($study->client_industry) $tags = [$study->client_industry];
    @endphp
    @if(count($tags))
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--ice)] p-6">
      <div class="mb-4 font-[var(--font-display)] text-base font-bold text-[var(--navy)]">Expertise</div>
      <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
          <span class="rounded-full border border-[var(--border)] bg-[rgba(0,184,219,0.08)] px-3 py-1 text-[0.7rem] font-bold uppercase tracking-[0.06em] text-[var(--cyan-deep)]">
            {{ strtoupper($tag) }}
          </span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- consult card --}}


  </aside>
</div>

{{-- ═══════════════════════════════════════════════
     CTA STRIP
═══════════════════════════════════════════════ --}}
<div class="border-t border-[rgba(0,184,219,0.12)] bg-[rgba(13,33,55,0.95)] px-6 py-16 text-center md:px-24">
  <h2 class="mb-3 font-[var(--font-display)] text-2xl font-extrabold tracking-[-0.03em] text-white md:text-[2rem]">
    Ready for Transformation?
  </h2>
  <p class="mx-auto mb-8 max-w-[500px] text-base text-white/55">
    Consult with our senior architects to blueprint your systems for maximum reliability and impact.
  </p>
  <div class="flex flex-wrap justify-center gap-4">
    <a href="{{ route('site.consulting') }}" wire:navigate
       class="rounded-xl bg-[var(--cyan)] px-10 py-3.5 font-[var(--font-display)] text-[0.9rem] font-bold text-white no-underline transition hover:-translate-y-0.5 hover:bg-[var(--cyan-dark)]">
      Schedule Consultation
    </a>
    <a href="{{ route('site.case-studies') }}" wire:navigate
       class="rounded-xl border-[1.5px] border-white/25 px-10 py-3.5 font-[var(--font-display)] text-[0.9rem] font-bold text-white no-underline transition hover:border-[var(--cyan)] hover:text-[var(--cyan)]">
      View All Case Studies
    </a>
  </div>
</div>

{{-- prose styles for rich content — identical pattern to blog-post --}}
<style>
.csd-prose { color: var(--text-secondary); line-height: 1.85; font-size: 1rem; word-break: break-word; overflow-wrap: break-word; }
.csd-prose h1,.csd-prose h2,.csd-prose h3,.csd-prose h4,.csd-prose h5,.csd-prose h6 { font-family: var(--font-display); font-weight: 700; color: var(--navy); letter-spacing: -0.02em; margin: 2rem 0 0.75rem; line-height: 1.3; }
.csd-prose h2 { font-size: 1.4rem; } .csd-prose h3 { font-size: 1.1rem; }
.csd-prose p { margin-bottom: 1.25em; }
.csd-prose a { color: var(--cyan); text-decoration: underline; word-break: break-all; }
.csd-prose strong { font-weight: 700; color: var(--navy); }
.csd-prose em { font-style: italic; }
.csd-prose ul,.csd-prose ol { padding-left: 1.5rem; margin-bottom: 1.25em; }
.csd-prose ul { list-style: disc; } .csd-prose ol { list-style: decimal; }
.csd-prose li { margin-bottom: 0.4em; line-height: 1.75; }
.csd-prose blockquote { border-left: 4px solid var(--cyan); padding: 0.75rem 1.25rem; margin: 1.5rem 0; background: var(--ice); border-radius: 0 8px 8px 0; font-style: italic; color: var(--text-secondary); }
.csd-prose code { background: rgba(0,184,219,0.08); color: var(--cyan-deep); border-radius: 4px; padding: 0.15em 0.4em; font-size: 0.85em; word-break: break-all; }
.csd-prose pre { background: #0d2137; color: #e2e8f0; border-radius: 10px; padding: 1.25em 1.5em; overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 1.5em; font-size: 0.85em; line-height: 1.65; max-width: 100%; white-space: pre; }
.csd-prose pre code { background: none; color: inherit; padding: 0; font-size: inherit; word-break: normal; }
.csd-prose table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; font-size: 0.875em; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.csd-prose th,.csd-prose td { border: 1px solid var(--border); padding: 0.6em 0.9em; text-align: left; white-space: nowrap; }
.csd-prose th { background: rgba(0,184,219,0.07); font-weight: 700; }
.csd-prose tr:nth-child(even) td { background: rgba(0,0,0,0.02); }
.csd-prose img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.25em 0; display: block; }
.csd-prose hr { border: none; border-top: 1px solid var(--border); margin: 2em 0; }

@keyframes csd-sweep { 0%{left:-100%} 100%{left:100%} }
</style>

</div>
