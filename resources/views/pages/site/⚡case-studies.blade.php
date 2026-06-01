<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new #[Layout('layouts.site')] #[Title('Case Studies — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public string $filterIndustry = '';

    public function updatingFilterIndustry(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function studies()
    {
        $query = CaseStudy::published()->latest('published_at');

        if ($this->filterIndustry) {
            $query->where('client_industry', $this->filterIndustry);
        }

        return $query->paginate(9);
    }
}; ?>

<div>

{{-- ═══════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════ --}}
<section class="relative flex min-h-[70vh] items-center overflow-hidden bg-[var(--navy)] px-6 pb-20 pt-28 md:px-24">

  {{-- dot grid --}}
  <div class="pointer-events-none absolute inset-0 opacity-50"
       style="background-image:radial-gradient(circle,rgba(0,184,219,0.12) 1px,transparent 1px);background-size:32px 32px;"></div>

  {{-- radar sweep --}}
  <div class="pointer-events-none absolute inset-0"
       style="background:linear-gradient(90deg,transparent,rgba(0,184,219,0.08),transparent);animation:cs-radar-sweep 4s infinite linear;"></div>

  {{-- ping dots --}}
  <div class="pointer-events-none absolute inset-0 overflow-hidden">
    <div class="absolute h-2 w-2 rounded-full bg-[rgba(0,184,219,0.3)]"
         style="top:25%;left:25%;animation:cs-ping 2s ease-in-out infinite;animation-delay:0s;"></div>
    <div class="absolute h-2 w-2 rounded-full bg-[rgba(0,184,219,0.3)]"
         style="top:50%;left:75%;animation:cs-ping 2s ease-in-out infinite;animation-delay:1s;"></div>
    <div class="absolute h-2 w-2 rounded-full bg-[rgba(0,184,219,0.3)]"
         style="bottom:33%;left:50%;animation:cs-ping 2s ease-in-out infinite;animation-delay:2s;"></div>
  </div>

  {{-- right ornament --}}
  <div class="pointer-events-none absolute right-0 top-1/2 h-full w-1/3 -translate-y-1/2 border-l border-white/10 opacity-25"></div>

  <div class="relative z-10 max-w-3xl">
    <span class="mb-6 inline-block rounded-md bg-[rgba(0,184,219,0.15)] px-4 py-1.5 text-[0.72rem] font-bold uppercase tracking-[0.1em] text-[var(--cyan)]">
      Global Portfolio
    </span>
    <h1 class="mb-5 font-[var(--font-display)] text-4xl font-extrabold leading-[1.05] tracking-[-0.04em] text-white md:text-[4rem]">
      Impact Built<br>From Here
    </h1>
    <p class="mb-8 max-w-[560px] text-[1.05rem] font-light leading-[1.8] text-white/55">
      Transforming the backbone of critical industries across the continent. From digital spirituality to surgical precision, we build the systems that sustain growth.
    </p>
    <div class="mt-4 flex items-center gap-3">
      <div class="h-1 w-24 rounded-sm bg-[var(--cyan)]"></div>
      <div class="h-1 w-12 rounded-sm bg-white/20"></div>
      <div class="h-1 w-6 rounded-sm bg-white/10"></div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════
     FILTER BAR
═══════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center justify-between gap-4 border-b border-[var(--border)] bg-[var(--ice)] px-6 py-5 md:px-24">
  <div></div>
  <div class="flex flex-wrap gap-2">
    @foreach([
        ''           => 'ALL',
        'Healthcare' => 'HEALTHCARE',
        'Religion'   => 'RELIGION',
        'Diaspora'   => 'DIASPORA',
    ] as $val => $label)
    <button
      wire:click="$set('filterIndustry', '{{ $val }}')"
      class="rounded-md border px-4 py-1.5 text-[0.78rem] font-semibold transition
             {{ $filterIndustry === $val
                  ? 'border-[var(--navy)] bg-[var(--navy)] text-white'
                  : 'border-[rgba(0,184,219,0.2)] bg-white text-[var(--text-secondary)] hover:border-[var(--navy)] hover:bg-[var(--navy)] hover:text-white' }}"
    >{{ $label }}</button>
    @endforeach
  </div>
</div>

{{-- ═══════════════════════════════════════════════
     CASE STUDIES GRID
═══════════════════════════════════════════════ --}}
<section class="relative bg-white px-6 py-20 md:px-24">

  {{-- subtle dot bg --}}
  <div class="pointer-events-none absolute inset-0 opacity-50"
       style="background-image:radial-gradient(circle,rgba(0,184,219,0.06) 1px,transparent 1px);background-size:28px 28px;"></div>

  <div class="relative mx-auto mb-12 flex max-w-[1200px] flex-wrap items-end justify-between gap-6">
    <div>
      <h2 class="mb-1.5 font-[var(--font-display)] text-2xl font-bold text-[var(--navy)] md:text-[2.2rem]">
        The Implementation Log
      </h2>
      <p class="text-sm text-[var(--text-muted)]">
        Architecting resilience through tailored digital frameworks. Filtered by sector and strategic impact.
      </p>
    </div>
  </div>

  @if($this->studies->isEmpty())
    <div class="mx-auto max-w-[1200px] py-20 text-center text-[var(--text-muted)]">
      <p class="text-base font-semibold">No case studies published yet.</p>
      <p class="mt-2 text-sm">Case studies added from the admin will appear here.</p>
    </div>
  @else
    <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach($this->studies as $study)
      <a href="{{ route('site.case-studies.show', $study->slug) }}" wire:navigate
         class="group flex flex-col overflow-hidden rounded-2xl border-[1.5px] border-[rgba(0,184,219,0.2)] bg-white/70 text-inherit no-underline backdrop-blur-md transition hover:-translate-y-2 hover:shadow-[0_20px_48px_rgba(0,184,219,0.14)]">

        {{-- image / logo --}}
        <div class="relative h-[220px] overflow-hidden border-b border-[rgba(0,184,219,0.12)]">
          @if($study->client_logo)
            <img src="{{ asset('storage/'.$study->client_logo) }}"
                 alt="{{ $study->client_name }}"
                 class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.08]">
          @else
            <div class="flex h-full w-full items-center justify-center"
                 style="background:linear-gradient(135deg,var(--navy),var(--navy-mid))">
              <span class="font-[var(--font-display)] text-5xl font-extrabold text-[rgba(0,184,219,0.35)]">
                {{ strtoupper(substr($study->client_name ?? 'CS', 0, 2)) }}
              </span>
            </div>
          @endif

          {{-- region badge --}}
          @if($study->client_location)
            <span class="absolute left-3 top-3 rounded bg-[var(--navy)] px-2.5 py-0.5 text-[0.68rem] font-bold uppercase tracking-[0.06em] text-white">
              {{ strtoupper($study->client_location) }}
            </span>
          @elseif($study->is_featured)
            <span class="absolute left-3 top-3 rounded bg-[var(--navy)] px-2.5 py-0.5 text-[0.68rem] font-bold uppercase tracking-[0.06em] text-white">
              FEATURED
            </span>
          @endif

          {{-- scan line --}}
          <div class="pointer-events-none absolute left-0 h-0.5 w-full bg-[rgba(0,184,219,0.4)] shadow-[0_0_8px_rgba(0,184,219,0.4)]"
               style="animation:cs-scanline 8s linear infinite;"></div>
        </div>

        {{-- body --}}
        <div class="flex flex-grow flex-col p-7">
          @if($study->client_industry)
            <div class="mb-3 flex items-center gap-2">
              <span class="text-[0.95rem] text-[var(--cyan)]">⬡</span>
              <span class="text-[0.72rem] font-bold uppercase tracking-[0.07em] text-[var(--text-muted)]">
                {{ $study->client_industry }}
              </span>
            </div>
          @endif

          @if($study->client_name)
            <div class="mb-1.5 text-[0.8rem] text-[var(--text-muted)]">{{ $study->client_name }}</div>
          @endif

          <h3 class="mb-3 font-[var(--font-display)] text-[1.05rem] font-bold leading-snug text-[var(--navy)]">
            {{ Str::limit($study->title, 80) }}
          </h3>

          @if($study->results)
            <p class="flex-grow text-[0.85rem] leading-[1.65] text-[var(--text-secondary)]">
              {{ Str::limit($study->results, 100) }}
            </p>
          @endif

          @if($study->metrics)
            <div class="mt-3 flex flex-wrap gap-1.5">
              @foreach(array_slice(is_array($study->metrics) ? $study->metrics : [], 0, 2) as $m)
                <span class="rounded-full bg-[rgba(0,184,219,0.08)] px-3 py-0.5 text-[0.7rem] font-semibold text-[var(--cyan-deep)]">
                  {{ $m['value'] ?? '' }} {{ $m['label'] ?? '' }}
                </span>
              @endforeach
            </div>
          @endif
        </div>

        {{-- footer --}}
        <div class="flex items-center justify-between border-t border-[rgba(0,184,219,0.1)] px-7 py-4">
          <span class="font-[var(--font-display)] text-[0.68rem] font-bold uppercase tracking-[0.06em] text-black/30">
            #{{ Str::slug($study->client_industry ?? 'tech', '_') }}
          </span>
          <span class="flex items-center gap-1.5 font-[var(--font-display)] text-[0.78rem] font-bold text-[var(--cyan)] transition group-hover:gap-2.5">
            VIEW DETAIL
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </div>

      </a>
      @endforeach
    </div>

    @if($this->studies->hasPages())
      <div class="mx-auto mt-10 max-w-[1200px]">
        {{ $this->studies->links() }}
      </div>
    @endif
  @endif
</section>

{{-- ═══════════════════════════════════════════════
     GLOBAL REACH
═══════════════════════════════════════════════ --}}
<section class="bg-[var(--ice)] px-6 py-20 md:px-24">
  <div class="mx-auto grid max-w-[1200px] items-center gap-16 lg:grid-cols-[5fr_7fr]">

    <div>
      <h2 class="mb-8 font-[var(--font-display)] text-2xl font-bold text-[var(--navy)] md:text-[2.2rem]">
        Engineering Presence, Everywhere.
      </h2>
      <div class="flex flex-col gap-5">
        <div class="border-l-4 border-[var(--cyan)] bg-white/70 px-6 py-5">
          <div class="mb-1.5 text-[0.72rem] font-bold uppercase tracking-[0.08em] text-[var(--cyan)]">
            Cloud Sovereignty
          </div>
          <div class="text-[0.9rem] text-[var(--text-secondary)]">
            Building local data centers to ensure regional data remains secure and accessible within borders.
          </div>
        </div>
        <div class="border-l-4 border-[var(--border)] bg-white/70 px-6 py-5">
          <div class="mb-1.5 text-[0.72rem] font-bold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Architectural Integrity
          </div>
          <div class="text-[0.9rem] text-[var(--text-secondary)]">
            Systems designed for 99.99% uptime in environments with fluctuating power and connectivity.
          </div>
        </div>
      </div>
    </div>

    <div class="relative h-[440px] overflow-hidden rounded-[20px] border border-[rgba(0,184,219,0.12)] shadow-[0_20px_60px_rgba(0,0,0,0.12)] lg:h-[440px]">
      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxHI4jW5HiyfcWxOZlybkS8BQb-uSaWatLSQF7RVt-mxLSe6j2dU1c0SFHFgqssFi79p_JFCM3xbSt9r7o22xlxOmxpTv7Fp--M_Pzhe9ScA8YQd3M7Wfit7XLTCM5pvaUvSbAPp4D0URIWig5YH-H7arwp8D-bdzCQZxNOfpgSq-Wq0tJSmrOL52ssOXeBAfXKeoPbRp4BnMfbieGs18y1QkSM40FwyoXglDlDGzguzyi2pxqfCMdZGJGtlPTALtJp8gdr-T1_Ok"
           alt="Engineering infrastructure"
           class="h-full w-full object-cover">
      <div class="absolute inset-0 bg-[rgba(13,33,55,0.2)]"></div>
      <div class="absolute bottom-8 right-8 rounded-xl bg-white/90 px-6 py-5 backdrop-blur-md">
        <div class="font-[var(--font-display)] text-2xl font-extrabold text-[var(--cyan)]">14+</div>
        <div class="mt-0.5 text-[0.72rem] font-bold uppercase tracking-[0.07em] text-[var(--text-muted)]">
          Countries Deployed
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ═══════════════════════════════════════════════
     CTA STRIP
═══════════════════════════════════════════════ --}}
<div class="site-cta-strip">
  <div>
    <h2>Want results like these?</h2>
    <p>Every case study started with a conversation. Let's talk about your project.</p>
  </div>
  <a href="{{ route('site.consulting') }}" wire:navigate class="btn-white-solid">Start a Conversation</a>
</div>

{{-- keyframes only — cannot be expressed as Tailwind utilities --}}
<style>
@keyframes cs-radar-sweep {
  0%   { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
@keyframes cs-ping {
  0%,100% { transform: scale(1);   opacity: 0.3; }
  50%     { transform: scale(1.5); opacity: 1;   }
}
@keyframes cs-scanline {
  0%   { top: 0; }
  100% { top: 100%; }
}
</style>

</div>
