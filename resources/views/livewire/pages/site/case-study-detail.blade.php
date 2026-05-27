<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public CaseStudy $study;

    public function mount(string $slug): void
    {
        $this->study = CaseStudy::published()->where('slug', $slug)->firstOrFail();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.case-study-detail')->title($this->study->title . ' — ExchoSoft');
    }
}; ?>

<div>
    <x-page-banner
        height="sm"
        :title="$study->title"
        :subtitle="$study->client_name ? ($study->client_name . ($study->client_industry ? ' · ' . $study->client_industry : '')) : null"
        tag="Case Study"
        :breadcrumbs="[
            ['label'=>'Home','route'=>'home'],
            ['label'=>'Case Studies','route'=>'site.case-studies'],
            ['label'=>$study->title],
        ]"
    />

<style>
  /* ── Case Study Detail Layout ── */
  .csd-body { padding: 4rem 6rem; background: var(--white); }
  .csd-grid { display: grid; grid-template-columns: 1fr 340px; gap: 3rem; max-width: 1200px; margin: 0 auto; align-items: start; }

  /* ── Client meta row ── */
  .csd-client-bar {
    display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;
    padding: 1rem 1.5rem; margin-bottom: 2.5rem;
    background: var(--ice); border: 1px solid var(--border);
    border-radius: 12px;
  }
  .csd-client-chip {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.78rem; font-weight: 600; color: var(--text-secondary);
    letter-spacing: 0.04em; text-transform: uppercase;
  }
  .csd-client-chip span.material-symbols-outlined { font-size: 1rem; color: var(--cyan); }
  .csd-client-sep { width: 1px; height: 16px; background: var(--border); }

  /* ── Metrics grid ── */
  .csd-metrics { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 2.5rem; }
  .csd-metric-card {
    background: var(--navy); border-radius: 14px; padding: 1.25rem 1rem;
    text-align: center; position: relative; overflow: hidden;
  }
  .csd-metric-card::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(0,184,219,0.12) 0%, transparent 60%);
    pointer-events: none;
  }
  .csd-metric-val {
    font-family: var(--font-display); font-size: 1.8rem; font-weight: 800;
    color: var(--cyan); letter-spacing: -0.03em; line-height: 1.1; position: relative;
  }
  .csd-metric-lbl {
    font-size: 0.72rem; color: rgba(255,255,255,0.6); margin-top: 0.35rem;
    text-transform: uppercase; letter-spacing: 0.06em; position: relative;
  }

  /* ── Content sections ── */
  .csd-section { margin-bottom: 2.5rem; }
  .csd-section-head {
    display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;
  }
  .csd-section-tag {
    font-size: 0.68rem; font-weight: 800; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--cyan-deep);
    background: rgba(0,184,219,0.1); padding: 0.25rem 0.6rem;
    border-radius: 100px;
  }
  .csd-section-line { flex: 1; height: 1px; background: var(--border); }
  .csd-section-title {
    font-family: var(--font-display); font-size: 1.25rem; font-weight: 700;
    color: var(--navy); letter-spacing: -0.02em; margin: 0 0 0.75rem;
  }
  .csd-section-text {
    font-size: 1rem; color: var(--text-secondary); line-height: 1.85;
  }
  .csd-section-card {
    background: var(--ice); border: 1px solid var(--border);
    border-left: 4px solid var(--cyan); border-radius: 0 12px 12px 0;
    padding: 1.5rem 1.75rem;
  }

  /* ── Full rich content ── */
  .csd-content { color: var(--text-secondary); line-height: 1.85; font-size: 1rem; margin-top: 2rem; }
  .csd-content h1,.csd-content h2,.csd-content h3 {
    font-family: var(--font-display); font-weight: 700; color: var(--navy);
    letter-spacing: -0.02em; margin: 2rem 0 0.75rem;
  }
  .csd-content h2 { font-size: 1.4rem; }
  .csd-content h3 { font-size: 1.1rem; }
  .csd-content p { margin-bottom: 1.25rem; }
  .csd-content ul,.csd-content ol { padding-left: 1.5rem; margin-bottom: 1.25rem; }
  .csd-content li { margin-bottom: 0.4rem; line-height: 1.75; }
  .csd-content blockquote {
    border-left: 3px solid var(--cyan); padding: 0.75rem 1.25rem;
    margin: 1.5rem 0; background: var(--ice); border-radius: 0 8px 8px 0;
  }
  .csd-content blockquote p { color: var(--text-secondary); font-style: italic; margin: 0; }
  .csd-content code {
    background: var(--ice); padding: 0.15rem 0.4rem; border-radius: 4px;
    font-size: 0.88em; color: var(--cyan-deep);
  }
  .csd-content pre {
    background: var(--navy); color: var(--sky); padding: 1.25rem;
    border-radius: 10px; overflow-x: auto; margin-bottom: 1.25rem;
  }
  .csd-content pre code { background: none; color: inherit; padding: 0; }
  .csd-content strong { color: var(--navy); font-weight: 700; }

  /* ── Action buttons ── */
  .csd-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid var(--border); }
  .csd-btn-back {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.25rem; border-radius: 10px;
    border: 1.5px solid var(--border); color: var(--text-secondary);
    font-size: 0.85rem; font-weight: 600; text-decoration: none;
    transition: all 0.2s; background: var(--white);
  }
  .csd-btn-back:hover { border-color: var(--cyan); color: var(--cyan); }
  .csd-btn-cta {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.5rem; border-radius: 10px;
    background: var(--cyan); color: var(--white);
    font-size: 0.85rem; font-weight: 700; text-decoration: none;
    transition: all 0.2s;
  }
  .csd-btn-cta:hover { background: var(--cyan-dark); transform: translateY(-1px); }

  /* ── Sidebar ── */
  .csd-sidebar { position: sticky; top: 80px; }
  .csd-sidebar-box {
    background: var(--navy); border-radius: 16px; padding: 1.75rem;
    color: var(--white); margin-bottom: 1.5rem; position: relative; overflow: hidden;
  }
  .csd-sidebar-box::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at top left, rgba(0,184,219,0.15) 0%, transparent 65%);
    pointer-events: none;
  }
  .csd-sidebar-box-title {
    font-size: 0.68rem; font-weight: 800; letter-spacing: 0.12em;
    text-transform: uppercase; color: rgba(255,255,255,0.5);
    margin-bottom: 1.25rem; position: relative;
  }
  .csd-stack-item {
    padding: 0.75rem 0; border-bottom: 1px solid rgba(255,255,255,0.07);
    position: relative;
  }
  .csd-stack-item:last-child { border-bottom: none; }
  .csd-stack-key {
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--cyan); margin-bottom: 0.2rem;
  }
  .csd-stack-val { font-size: 0.88rem; font-weight: 600; color: var(--white); }

  .csd-tags-box {
    background: var(--ice); border: 1px solid var(--border);
    border-radius: 16px; padding: 1.5rem;
  }
  .csd-tags-box-title {
    font-family: var(--font-display); font-size: 1rem; font-weight: 700;
    color: var(--navy); margin-bottom: 1rem;
  }
  .csd-tag {
    display: inline-block; padding: 0.3rem 0.75rem; margin: 0 0.4rem 0.4rem 0;
    background: rgba(0,184,219,0.08); border: 1px solid var(--border);
    border-radius: 100px; font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase; color: var(--cyan-deep);
  }

  /* ── Tags from technologies field ── */
  .csd-tech-tag {
    display: inline-block; padding: 0.3rem 0.75rem; margin: 0 0.4rem 0.4rem 0;
    background: var(--navy); border-radius: 100px; font-size: 0.72rem;
    font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--cyan);
  }

  /* ── CTA strip ── */
  .csd-cta-strip {
    background: var(--navy);
    border-top: 1px solid rgba(0,184,219,0.12);
    padding: 4rem 6rem;
    text-align: center;
  }
  .csd-cta-strip h2 {
    font-family: var(--font-display); font-size: 2rem; font-weight: 800;
    color: var(--white); letter-spacing: -0.03em; margin-bottom: 0.75rem;
  }
  .csd-cta-strip p {
    font-size: 1rem; color: rgba(255,255,255,0.6); max-width: 540px;
    margin: 0 auto 2rem;
  }
  .csd-cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
  .csd-cta-primary {
    padding: 0.85rem 2rem; border-radius: 10px; background: var(--cyan);
    color: var(--white); font-weight: 700; font-size: 0.9rem; text-decoration: none;
    transition: all 0.2s;
  }
  .csd-cta-primary:hover { background: var(--cyan-dark); transform: translateY(-2px); }
  .csd-cta-outline {
    padding: 0.85rem 2rem; border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,0.25); color: var(--white);
    font-weight: 700; font-size: 0.9rem; text-decoration: none;
    transition: all 0.2s;
  }
  .csd-cta-outline:hover { border-color: var(--cyan); color: var(--cyan); }

  /* ── Responsive ── */
  @media (max-width: 1024px) {
    .csd-body { padding: 3rem 2rem; }
    .csd-grid { grid-template-columns: 1fr; }
    .csd-sidebar { position: static; }
    .csd-metrics { grid-template-columns: repeat(2,1fr); }
    .csd-cta-strip { padding: 3rem 2rem; }
  }
  @media (max-width: 640px) {
    .csd-body { padding: 2rem 1.25rem; }
    .csd-metrics { grid-template-columns: repeat(2,1fr); gap: 0.75rem; }
    .csd-metric-val { font-size: 1.4rem; }
    .csd-cta-strip { padding: 2.5rem 1.25rem; }
    .csd-cta-strip h2 { font-size: 1.5rem; }
  }
</style>

<div class="csd-body">
  <div class="csd-grid">

    {{-- ── MAIN EDITORIAL COLUMN ── --}}
    <div>

      {{-- Client meta bar --}}
      @if($study->client_name || $study->client_industry || $study->client_location)
      <div class="csd-client-bar">
        @if($study->client_name)
        <div class="csd-client-chip">
          <span class="material-symbols-outlined">business</span>
          {{ $study->client_name }}
        </div>
        @endif
        @if($study->client_name && $study->client_industry)<div class="csd-client-sep"></div>@endif
        @if($study->client_industry)
        <div class="csd-client-chip">
          <span class="material-symbols-outlined">category</span>
          {{ $study->client_industry }}
        </div>
        @endif
        @if($study->client_industry && $study->client_location)<div class="csd-client-sep"></div>@endif
        @if(!empty($study->client_location))
        <div class="csd-client-chip">
          <span class="material-symbols-outlined">location_on</span>
          {{ $study->client_location }}
        </div>
        @endif
      </div>
      @endif

      {{-- Metrics grid --}}
      @if($study->metrics && count($study->metrics))
      <div class="csd-metrics">
        @foreach($study->metrics as $m)
        <div class="csd-metric-card">
          <div class="csd-metric-val">{{ $m['value'] ?? '' }}</div>
          <div class="csd-metric-lbl">{{ $m['label'] ?? '' }}</div>
        </div>
        @endforeach
      </div>
      @endif

      {{-- Challenge --}}
      @if($study->challenge)
      <div class="csd-section">
        <div class="csd-section-head">
          <span class="csd-section-tag">Challenge</span>
          <div class="csd-section-line"></div>
        </div>
        <div class="csd-section-card">
          <h3 class="csd-section-title">The Challenge</h3>
          <p class="csd-section-text">{{ $study->challenge }}</p>
        </div>
      </div>
      @endif

      {{-- Solution --}}
      @if($study->solution)
      <div class="csd-section">
        <div class="csd-section-head">
          <span class="csd-section-tag">Solution</span>
          <div class="csd-section-line"></div>
        </div>
        <div class="csd-section-card" style="border-left-color: var(--navy);">
          <h3 class="csd-section-title">Our Solution</h3>
          <p class="csd-section-text">{{ $study->solution }}</p>
        </div>
      </div>
      @endif

      {{-- Results --}}
      @if($study->results)
      <div class="csd-section">
        <div class="csd-section-head">
          <span class="csd-section-tag">Results</span>
          <div class="csd-section-line"></div>
        </div>
        <div class="csd-section-card" style="border-left-color: #22c55e; background: #f0fdf4;">
          <h3 class="csd-section-title" style="color:#166534;">Results & Impact</h3>
          <p class="csd-section-text" style="color:#166534;">{{ $study->results }}</p>
        </div>
      </div>
      @endif

      {{-- Full rich HTML content --}}
      @if($study->content)
      <div class="csd-content">
        {!! $study->content !!}
      </div>
      @endif

      {{-- Action buttons --}}
      <div class="csd-actions">
        <a href="{{ route('site.case-studies') }}" wire:navigate class="csd-btn-back">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          All Case Studies
        </a>
        <a href="{{ route('site.consulting') }}" wire:navigate class="csd-btn-cta">
          Work With Us
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>

    </div>

    {{-- ── SIDEBAR ── --}}
    <aside class="csd-sidebar">

      {{-- Tech stack box --}}
      @php
        $techFields = [
          'Architecture'      => $study->architecture      ?? null,
          'Technologies'      => is_array($study->technologies) ? implode(', ', $study->technologies) : ($study->technologies ?? null),
          'Platform'          => $study->platform          ?? null,
          'Integration'       => $study->integration       ?? null,
          'Duration'          => $study->duration          ?? null,
          'Team Size'         => $study->team_size         ?? null,
        ];
        $techFields = array_filter($techFields);
      @endphp

      @if(count($techFields) > 0)
      <div class="csd-sidebar-box">
        <div class="csd-sidebar-box-title">Technical Details</div>
        @foreach($techFields as $key => $val)
        <div class="csd-stack-item">
          <div class="csd-stack-key">{{ $key }}</div>
          <div class="csd-stack-val">{{ $val }}</div>
        </div>
        @endforeach
      </div>
      @else
      {{-- Fallback when no tech fields populated --}}
      <div class="csd-sidebar-box">
        <div class="csd-sidebar-box-title">Project Details</div>
        @if($study->client_name)
        <div class="csd-stack-item">
          <div class="csd-stack-key">Client</div>
          <div class="csd-stack-val">{{ $study->client_name }}</div>
        </div>
        @endif
        @if($study->client_industry)
        <div class="csd-stack-item">
          <div class="csd-stack-key">Industry</div>
          <div class="csd-stack-val">{{ $study->client_industry }}</div>
        </div>
        @endif
        @if($study->published_at)
        <div class="csd-stack-item">
          <div class="csd-stack-key">Published</div>
          <div class="csd-stack-val">{{ $study->published_at->format('M Y') }}</div>
        </div>
        @endif
      </div>
      @endif

      {{-- Tags / expertise --}}
      @php
        $tags = [];
        if(is_array($study->tags)) $tags = $study->tags;
        elseif($study->client_industry) $tags = [$study->client_industry];
      @endphp
      @if(count($tags))
      <div class="csd-tags-box">
        <div class="csd-tags-box-title">Expertise</div>
        @foreach($tags as $tag)
          <span class="csd-tag">{{ strtoupper($tag) }}</span>
        @endforeach
      </div>
      @endif

      {{-- Consult CTA card --}}
      <div style="margin-top:1.5rem; padding:1.5rem; border-radius:16px; background:rgba(0,184,219,0.06); border:1px solid var(--border); text-align:center;">
        <div style="font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:0.5rem;">Ready to replicate this?</div>
        <div style="font-size:0.82rem;color:var(--text-muted);margin-bottom:1.25rem;">Talk to our senior architects about your use case.</div>
        <a href="{{ route('site.consulting') }}" wire:navigate
           style="display:block;padding:0.7rem 1rem;border-radius:9px;background:var(--cyan);color:var(--white);font-weight:700;font-size:0.82rem;text-decoration:none;transition:background 0.2s;"
           onmouseover="this.style.background='var(--cyan-dark)'" onmouseout="this.style.background='var(--cyan)'">
          Schedule a Consultation
        </a>
      </div>

    </aside>

  </div>{{-- /.csd-grid --}}
</div>{{-- /.csd-body --}}

{{-- ── CTA STRIP ── --}}
<div class="csd-cta-strip">
  <h2>Ready for Transformation?</h2>
  <p>Consult with our senior architects to blueprint your systems for maximum reliability and impact.</p>
  <div class="csd-cta-btns">
    <a href="{{ route('site.consulting') }}" wire:navigate class="csd-cta-primary">Schedule Consultation</a>
    <a href="{{ route('site.case-studies') }}" wire:navigate class="csd-cta-outline">View All Case Studies</a>
  </div>
</div>

</div>
