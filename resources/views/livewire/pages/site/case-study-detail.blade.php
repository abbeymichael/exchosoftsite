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
        return view('pages.site.case-study-detail')->title($this->study->title . ' — ExchoSoft');
    }
}; ?>

<div>

<style>
/* ── CASE STUDY DETAIL ── */
/* HERO BANNER */
.csd-hero {
  position: relative; height: 70vh; min-height: 440px; max-height: 680px;
  overflow: hidden; display: flex; align-items: flex-end;
  background: var(--navy);
}
.csd-hero img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.csd-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(13,33,55,0.92) 0%, rgba(13,33,55,0.35) 55%, transparent 100%); }
.csd-hero-content { position: relative; z-index: 10; padding: 0 6rem 3.5rem; width: 100%; max-width: 1200px; }
.csd-hero-pill {
  display: inline-block; padding: 0.3rem 0.9rem; border-radius: 100px; margin-bottom: 1.25rem;
  background: rgba(0,184,219,0.15); color: var(--cyan); font-size: 0.7rem; font-weight: 700;
  letter-spacing: 0.1em; text-transform: uppercase; border: 1px solid rgba(0,184,219,0.25);
}
.csd-hero h1 {
  font-family: var(--font-display); font-size: clamp(1.6rem, 3.5vw, 3rem);
  font-weight: 800; color: white; line-height: 1.15; letter-spacing: -0.03em; margin-bottom: 1.5rem;
}
.csd-hero-meta { display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: center; }
.csd-hero-meta-item { display: flex; align-items: center; gap: 0.45rem; font-size: 0.8rem; color: rgba(255,255,255,0.65); }

/* DOT GRID BACKGROUND */
.csd-dot-bg { background-image: radial-gradient(circle, rgba(0,184,219,0.08) 1px, transparent 1px); background-size: 28px 28px; }

/* MAIN CONTENT AREA */
.csd-content-area { max-width: 1440px; margin: 0 auto; padding: 4rem 6rem; display: grid; grid-template-columns: 8fr 4fr; gap: 3rem; }

/* EDITORIAL CONTENT */
.csd-main {}

/* GLASS OBJECTIVE CARD */
.csd-glass-card {
  background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
  border: 1.5px solid rgba(0,184,219,0.18); border-radius: 14px;
  padding: 2.5rem; margin-bottom: 2rem;
  position: relative; overflow: hidden;
}
.csd-glass-card::after {
  content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(0,184,219,0.06), transparent);
  animation: csd-sweep 4s infinite; pointer-events: none;
}
@keyframes csd-sweep { 0%{left:-100%} 100%{left:100%} }
.csd-section-title { font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; color: var(--cyan); margin-bottom: 1rem; }
.csd-section-body { font-size: 1rem; color: var(--text-secondary); line-height: 1.85; }
.csd-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem; }
.csd-mini-card { background: var(--ice); border: 1px solid var(--border); border-radius: 10px; padding: 1rem; }
.csd-mini-icon { color: var(--cyan); margin-bottom: 0.5rem; }
.csd-mini-title { font-family: var(--font-display); font-size: 0.82rem; font-weight: 700; color: var(--navy); margin-bottom: 0.25rem; }
.csd-mini-body { font-size: 0.78rem; color: var(--text-muted); }

/* SOLUTION SECTION */
.csd-solution-section { margin-bottom: 2rem; }
.csd-section-head { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
.csd-section-tag { font-size: 0.68rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--cyan-deep); background: rgba(0,184,219,0.1); padding: 0.25rem 0.6rem; border-radius: 100px; }
.csd-section-line { flex: 1; height: 1px; background: var(--border); }
.csd-section-heading { font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem; }
.csd-card-highlight {
  background: var(--ice); border: 1px solid var(--border);
  border-left: 4px solid var(--navy); border-radius: 0 12px 12px 0;
  padding: 1.5rem 1.75rem;
}

/* SOLUTION IMAGE */
.csd-solution-img { border-radius: 14px; overflow: hidden; border: 1px solid var(--border); margin-top: 1.5rem; position: relative; }
.csd-solution-img img { width: 100%; aspect-ratio: 16/9; object-fit: cover; transition: transform 0.7s; display: block; }
.csd-solution-img:hover img { transform: scale(1.04); }
.csd-solution-img-overlay { position: absolute; inset: 0; background: rgba(13,33,55,0.15); transition: background 0.4s; pointer-events: none; }
.csd-solution-img:hover .csd-solution-img-overlay { background: transparent; }

/* IMPACT METRICS */
.csd-impact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem; }
.csd-impact-card {
  background: var(--ice); border: 1px solid var(--border);
  border-radius: 14px; padding: 2rem; display: flex; flex-direction: column; justify-content: space-between;
}
.csd-impact-num { font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; color: var(--cyan); letter-spacing: -0.04em; }
.csd-impact-title { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-top: 0.75rem; }
.csd-impact-body { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem; }

/* TESTIMONIAL */
.csd-testimonial {
  background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
  border: 1.5px solid rgba(0,184,219,0.2); border-radius: 14px;
  padding: 2rem; margin-bottom: 2rem;
}
.csd-testimonial-quote { font-size: 1.05rem; color: var(--navy); font-style: italic; line-height: 1.75; margin-bottom: 1.5rem; }
.csd-testimonial-author { display: flex; align-items: center; gap: 1rem; }
.csd-testimonial-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--cyan); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-weight: 800; color: white; font-size: 0.85rem; flex-shrink: 0; }
.csd-testimonial-name { font-family: var(--font-display); font-size: 0.88rem; font-weight: 700; color: var(--navy); }
.csd-testimonial-role { font-size: 0.72rem; color: var(--text-muted); letter-spacing: 0.04em; text-transform: uppercase; margin-top: 0.15rem; }

/* FULL CONTENT */
.csd-full-content { color: var(--text-secondary); line-height: 1.85; font-size: 1rem; margin-bottom: 2rem; }
.csd-full-content h1,.csd-full-content h2,.csd-full-content h3 { font-family: var(--font-display); font-weight: 700; color: var(--navy); letter-spacing: -0.02em; margin: 2rem 0 0.75rem; }
.csd-full-content h2 { font-size: 1.4rem; } .csd-full-content h3 { font-size: 1.1rem; }
.csd-full-content p { margin-bottom: 1.25rem; }
.csd-full-content ul,.csd-full-content ol { padding-left: 1.5rem; margin-bottom: 1.25rem; }
.csd-full-content li { margin-bottom: 0.4rem; line-height: 1.75; }
.csd-full-content blockquote { border-left: 3px solid var(--cyan); padding: 0.75rem 1.25rem; margin: 1.5rem 0; background: var(--ice); border-radius: 0 8px 8px 0; }
.csd-full-content code { background: var(--ice); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.88em; color: var(--cyan-deep); }
.csd-full-content strong { color: var(--navy); font-weight: 700; }

/* ACTION BUTTONS */
.csd-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid var(--border); }
.csd-btn-back { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 10px; border: 1.5px solid var(--border); color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; background: white; }
.csd-btn-back:hover { border-color: var(--cyan); color: var(--cyan); }
.csd-btn-cta { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; border-radius: 10px; background: var(--cyan); color: white; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: all 0.2s; }
.csd-btn-cta:hover { background: var(--cyan-dark); transform: translateY(-1px); }

/* TECH SIDEBAR */
.csd-tech-sidebar { position: sticky; top: 80px; height: fit-content; }
.csd-tech-box {
  background: var(--navy); border-radius: 16px; padding: 2rem;
  color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden;
}
.csd-tech-box::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at top left, rgba(0,184,219,0.12) 0%, transparent 60%); pointer-events: none; }
.csd-tech-box-hdr { font-size: 0.68rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.45); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; position: relative; }
.csd-tech-item { padding: 0.85rem 0; border-bottom: 1px solid rgba(255,255,255,0.07); position: relative; }
.csd-tech-item:last-child { border-bottom: none; }
.csd-tech-key { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--cyan); margin-bottom: 0.2rem; }
.csd-tech-val { font-size: 0.88rem; font-weight: 600; color: white; }
.csd-tech-dl-btn {
  width: 100%; margin-top: 1.5rem; padding: 0.9rem; border-radius: 10px;
  background: rgba(0,184,219,0.15); border: 1px solid rgba(0,184,219,0.25);
  color: var(--cyan); font-family: var(--font-display); font-weight: 700; font-size: 0.82rem;
  cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.csd-tech-dl-btn:hover { background: rgba(0,184,219,0.25); }

.csd-tags-box { background: var(--ice); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
.csd-tags-box-title { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem; }
.csd-tag { display: inline-block; padding: 0.3rem 0.75rem; margin: 0 0.35rem 0.4rem 0; background: rgba(0,184,219,0.08); border: 1px solid var(--border); border-radius: 100px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--cyan-deep); }

.csd-consult-card { margin-bottom: 1.5rem; padding: 1.5rem; border-radius: 16px; background: rgba(0,184,219,0.06); border: 1px solid var(--border); text-align: center; }
.csd-consult-card h4 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; }
.csd-consult-card p { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1.25rem; }

/* CTA STRIP */
.csd-cta-strip { background: rgba(13,33,55,0.95); border-top: 1px solid rgba(0,184,219,0.12); padding: 4rem 6rem; text-align: center; }
.csd-cta-strip h2 { font-family: var(--font-display); font-size: clamp(1.6rem,2.5vw,2rem); font-weight: 800; color: white; letter-spacing: -0.03em; margin-bottom: 0.75rem; }
.csd-cta-strip p { font-size: 1rem; color: rgba(255,255,255,0.55); max-width: 500px; margin: 0 auto 2rem; }
.csd-cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.csd-cta-primary { padding: 0.9rem 2.5rem; border-radius: 10px; background: var(--cyan); color: white; font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; font-family: var(--font-display); }
.csd-cta-primary:hover { background: var(--cyan-dark); transform: translateY(-2px); }
.csd-cta-outline { padding: 0.9rem 2.5rem; border-radius: 10px; border: 1.5px solid rgba(255,255,255,0.25); color: white; font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; font-family: var(--font-display); }
.csd-cta-outline:hover { border-color: var(--cyan); color: var(--cyan); }

@media (max-width: 1024px) {
  .csd-hero-content { padding: 0 2rem 2.5rem; }
  .csd-content-area { grid-template-columns: 1fr; padding: 3rem 2rem; }
  .csd-tech-sidebar { position: static; }
  .csd-impact-grid { grid-template-columns: 1fr 1fr; }
  .csd-cta-strip { padding: 3rem 2rem; }
}
@media (max-width: 640px) {
  .csd-hero { height: 55vh; }
  .csd-hero h1 { font-size: clamp(1.3rem,6vw,1.8rem); }
  .csd-impact-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
  .csd-impact-num { font-size: 1.8rem; }
  .csd-mini-grid { grid-template-columns: 1fr; }
  .csd-cta-strip { padding: 2.5rem 1.25rem; }
}
</style>

{{-- ── HERO BANNER ── --}}
<section class="csd-hero">
  @if($study->cover_image ?? false)
    <img src="{{ asset('storage/'.$study->cover_image) }}" alt="{{ $study->title }}">
  @elseif($study->client_logo ?? false)
    <img src="{{ asset('storage/'.$study->client_logo) }}" alt="{{ $study->client_name }}">
  @else
    {{-- Fallback gradient background already on .csd-hero --}}
  @endif
  <div class="csd-hero-overlay"></div>
  <div class="csd-hero-content">
    <span class="csd-hero-pill">Case Study Detail</span>
    <h1>{{ $study->title }}</h1>
    <div class="csd-hero-meta">
      @if($study->client_name)
      <div class="csd-hero-meta-item">
        <span class="material-symbols-outlined" style="font-size:0.95rem;">business</span>
        {{ $study->client_name }}
      </div>
      @endif
      @if($study->client_industry)
      <div class="csd-hero-meta-item">
        <span class="material-symbols-outlined" style="font-size:0.95rem;">category</span>
        {{ $study->client_industry }}
      </div>
      @endif
      @if(!empty($study->client_location))
      <div class="csd-hero-meta-item">
        <span class="material-symbols-outlined" style="font-size:0.95rem;">location_on</span>
        {{ $study->client_location }}
      </div>
      @endif
    </div>
  </div>
</section>

{{-- ── MAIN CONTENT ── --}}
<div class="csd-content-area csd-dot-bg">

  {{-- ── EDITORIAL ── --}}
  <div class="csd-main">

    {{-- Metrics grid --}}
    @if($study->metrics && count($study->metrics))
    <div class="csd-impact-grid" style="margin-bottom:2.5rem;">
      @foreach($study->metrics as $m)
      <div class="csd-impact-card">
        <span class="csd-impact-num">{{ $m['value'] ?? '' }}</span>
        <div>
          <div class="csd-impact-title">{{ $m['label'] ?? '' }}</div>
        </div>
      </div>
      @endforeach
    </div>
    @endif

    {{-- Challenge --}}
    @if($study->challenge)
    <div class="csd-solution-section">
      <div class="csd-section-head">
        <span class="csd-section-tag">Challenge</span>
        <div class="csd-section-line"></div>
      </div>
      <div class="csd-glass-card">
        <h3 class="csd-section-title">Offline-First for {{ $study->client_industry ?? 'Business' }} Integrity</h3>
        <p class="csd-section-body">{{ $study->challenge }}</p>
        <div class="csd-mini-grid">
          <div class="csd-mini-card">
            <div class="csd-mini-icon"><span class="material-symbols-outlined" style="font-size:1.2rem;">network_check</span></div>
            <div class="csd-mini-title">Low Latency</div>
            <div class="csd-mini-body">Immediate local data processing with zero dependency on cloud response times.</div>
          </div>
          <div class="csd-mini-card">
            <div class="csd-mini-icon"><span class="material-symbols-outlined" style="font-size:1.2rem;">security</span></div>
            <div class="csd-mini-title">Data Sovereignty</div>
            <div class="csd-mini-body">Records remain within the local network during active operational phases.</div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Solution --}}
    @if($study->solution)
    <div class="csd-solution-section">
      <div class="csd-section-head">
        <span class="csd-section-tag">Solution</span>
        <div class="csd-section-line"></div>
      </div>
      <h3 class="csd-section-heading">Our Solution</h3>
      <div class="csd-card-highlight" style="border-left-color:var(--navy);">
        <p class="csd-section-body">{{ $study->solution }}</p>
      </div>
    </div>
    @endif

    {{-- Results --}}
    @if($study->results)
    <div class="csd-solution-section">
      <div class="csd-section-head">
        <span class="csd-section-tag">Results</span>
        <div class="csd-section-line"></div>
      </div>
      <div class="csd-impact-grid">
        <div class="csd-impact-card">
          <span class="csd-impact-num">100%</span>
          <div>
            <div class="csd-impact-title">Uptime Performance</div>
            <div class="csd-impact-body">Zero interruptions during scheduled and unscheduled network outages.</div>
          </div>
        </div>
        <div class="csd-impact-card">
          <span class="csd-impact-num" style="color:#22c55e;">12ms</span>
          <div>
            <div class="csd-impact-title">Local Sync Latency</div>
            <div class="csd-impact-body">Results are visible system-wide across the LAN in near real-time.</div>
          </div>
        </div>
      </div>
      <div class="csd-testimonial">
        <p class="csd-testimonial-quote">"{{ $study->results }}"</p>
        <div class="csd-testimonial-author">
          <div class="csd-testimonial-avatar">{{ strtoupper(substr($study->client_name ?? 'CL', 0, 2)) }}</div>
          <div>
            <div class="csd-testimonial-name">{{ $study->client_name ?? 'Client' }}</div>
            <div class="csd-testimonial-role">{{ $study->client_industry ?? 'Organization' }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Full rich content --}}
    @if($study->content)
    <div class="csd-full-content">{!! $study->content !!}</div>
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

  {{-- ── TECH SIDEBAR ── --}}
  <aside class="csd-tech-sidebar">
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

    <div class="csd-tech-box">
      <div class="csd-tech-box-hdr">
        <span class="material-symbols-outlined" style="font-size:1rem;color:var(--cyan);">terminal</span>
        Technical Stack
      </div>
      @if(count($techFields) > 0)
        @foreach($techFields as $key => $val)
        <div class="csd-tech-item">
          <div class="csd-tech-key">{{ $key }}</div>
          <div class="csd-tech-val">{{ $val }}</div>
        </div>
        @endforeach
      @else
        @if($study->client_name)<div class="csd-tech-item"><div class="csd-tech-key">Client</div><div class="csd-tech-val">{{ $study->client_name }}</div></div>@endif
        @if($study->client_industry)<div class="csd-tech-item"><div class="csd-tech-key">Industry</div><div class="csd-tech-val">{{ $study->client_industry }}</div></div>@endif
        @if($study->published_at)<div class="csd-tech-item"><div class="csd-tech-key">Published</div><div class="csd-tech-val">{{ $study->published_at->format('M Y') }}</div></div>@endif
        <div class="csd-tech-item"><div class="csd-tech-key">Architecture</div><div class="csd-tech-val">Offline-First PWA &amp; Mesh Nodes</div></div>
        <div class="csd-tech-item"><div class="csd-tech-key">Synchronization</div><div class="csd-tech-val">Real-time Cloud Sync / Conflict Resolvers</div></div>
        <div class="csd-tech-item"><div class="csd-tech-key">Security</div><div class="csd-tech-val">Encrypted Local Storage</div></div>
      @endif
      <a href="{{ route('site.consulting') }}" wire:navigate class="csd-tech-dl-btn">
        <span class="material-symbols-outlined" style="font-size:0.9rem;">download</span>
        Download Tech Specs
      </a>
    </div>

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

    <div class="csd-consult-card">
      <h4>Ready to replicate this?</h4>
      <p>Talk to our senior architects about your use case.</p>
      <a href="{{ route('site.consulting') }}" wire:navigate
         style="display:block;padding:0.75rem 1rem;border-radius:9px;background:var(--cyan);color:white;font-weight:700;font-size:0.82rem;text-decoration:none;font-family:var(--font-display);transition:background 0.2s;"
         onmouseover="this.style.background='var(--cyan-dark)'" onmouseout="this.style.background='var(--cyan)'">
        Schedule a Consultation
      </a>
    </div>
  </aside>
</div>

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
