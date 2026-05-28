<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Case Studies — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $studies = CaseStudy::published()->latest('published_at')->paginate(9);
        return view('pages.site.case-studies', compact('studies'));
    }
}; ?>

<div>
<style>
/* ── CASE STUDIES PAGE ── */
/* HERO */
.cs-hero {
  position: relative; min-height: 70vh; display: flex; align-items: center;
  padding: 7rem 6rem 5rem; overflow: hidden;
  background: var(--navy);
}
.cs-hero-dots {
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(0,184,219,0.12) 1px, transparent 1px);
  background-size: 32px 32px; opacity: 0.5; pointer-events: none;
}
.cs-hero-radar {
  position: absolute; inset: 0;
  background: linear-gradient(90deg, transparent, rgba(0,184,219,0.08), transparent);
  transform: translateX(-100%);
  animation: cs-radar-sweep 4s infinite linear;
  pointer-events: none;
}
@keyframes cs-radar-sweep {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
.cs-hero-pings { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.cs-hero-ping { position: absolute; width: 8px; height: 8px; background: rgba(0,184,219,0.3); border-radius: 50%; animation: cs-ping 2s ease-in-out infinite; }
@keyframes cs-ping { 0%,100%{transform:scale(1);opacity:0.3} 50%{transform:scale(1.5);opacity:1} }
.cs-hero-content { position: relative; z-index: 10; max-width: 760px; }
.cs-hero-badge {
  display: inline-block; padding: 0.3rem 0.9rem; border-radius: 6px; margin-bottom: 1.5rem;
  background: rgba(0,184,219,0.15); color: var(--cyan); font-size: 0.72rem; font-weight: 700;
  letter-spacing: 0.1em; text-transform: uppercase;
}
.cs-hero h1 {
  font-family: var(--font-display); font-size: clamp(2.4rem, 5vw, 4rem);
  font-weight: 800; color: white; line-height: 1.05; letter-spacing: -0.04em; margin-bottom: 1.25rem;
}
.cs-hero-sub { font-size: 1.05rem; color: rgba(255,255,255,0.55); max-width: 560px; line-height: 1.8; font-weight: 300; margin-bottom: 2rem; }
.cs-hero-decorations { display: flex; gap: 0.75rem; align-items: center; margin-top: 1rem; }
.cs-hero-bar { height: 4px; border-radius: 2px; }
/* Right industrial ornament */
.cs-hero-ornament {
  position: absolute; right: 0; top: 50%; transform: translateY(-50%);
  width: 33%; height: 100%; opacity: 0.25; pointer-events: none;
  border-left: 1px solid rgba(255,255,255,0.1);
}

/* FILTER BUTTONS */
.cs-filters-bar { background: var(--ice); border-bottom: 1px solid var(--border); padding: 1.25rem 6rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
.cs-filter-group { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.cs-filter-btn {
  padding: 0.45rem 1rem; border: 1px solid rgba(0,184,219,0.2); border-radius: 6px;
  font-size: 0.78rem; font-weight: 600; color: var(--text-secondary);
  background: white; cursor: pointer; transition: all 0.2s; font-family: var(--font-body);
}
.cs-filter-btn:hover, .cs-filter-btn.active { background: var(--navy); color: white; border-color: var(--navy); }

/* IMPLEMENTATION LOG */
.cs-log { padding: 5rem 6rem; background: var(--white); position: relative; }
.cs-log::before {
  content: ''; position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(0,184,219,0.06) 1px, transparent 1px);
  background-size: 28px 28px; opacity: 0.5; pointer-events: none;
}
.cs-log-header { max-width: 1200px; margin: 0 auto 3rem; display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1.5rem; }
.cs-log-h2 { font-family: var(--font-display); font-size: clamp(1.6rem,2.5vw,2.2rem); font-weight: 700; color: var(--navy); margin-bottom: 0.4rem; }
.cs-log-sub { font-size: 0.9rem; color: var(--text-muted); }

/* GLASS CARDS GRID */
.cs-glass-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; max-width: 1200px; margin: 0 auto; }
.cs-glass-card {
  background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
  border: 1.5px solid rgba(0,184,219,0.2); border-radius: 14px;
  overflow: hidden; text-decoration: none; display: flex; flex-direction: column;
  transition: transform 0.4s, box-shadow 0.4s;
}
.cs-glass-card:hover { transform: translateY(-8px); box-shadow: 0 20px 48px rgba(0,184,219,0.14); }
.cs-glass-img {
  height: 220px; overflow: hidden; position: relative;
  border-bottom: 1px solid rgba(0,184,219,0.12);
}
.cs-glass-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s; }
.cs-glass-card:hover .cs-glass-img img { transform: scale(1.08); }
.cs-glass-img-placeholder {
  height: 100%; background: linear-gradient(135deg,var(--navy),var(--navy-mid));
  display: flex; align-items: center; justify-content: center;
}
.cs-glass-region { position: absolute; top: 0.75rem; left: 0.75rem; background: var(--navy); color: white; padding: 0.25rem 0.65rem; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; border-radius: 4px; }
.cs-glass-scanline { position: absolute; width: 100%; height: 2px; background: rgba(0,184,219,0.4); box-shadow: 0 0 8px rgba(0,184,219,0.4); animation: cs-scanline 8s linear infinite; }
@keyframes cs-scanline { 0%{top:0} 100%{top:100%} }
.cs-glass-body { padding: 1.75rem; flex-grow: 1; display: flex; flex-direction: column; }
.cs-glass-sector { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
.cs-glass-sector-icon { font-size: 1rem; color: var(--cyan); }
.cs-glass-sector-txt { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); }
.cs-glass-client { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem; }
.cs-glass-title { font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: var(--navy); line-height: 1.4; margin-bottom: 0.75rem; }
.cs-glass-desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.65; flex-grow: 1; }
.cs-glass-metrics { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
.cs-glass-metric { background: rgba(0,184,219,0.08); color: var(--cyan-deep); padding: 0.22rem 0.65rem; border-radius: 100px; font-size: 0.7rem; font-weight: 600; }
.cs-glass-footer { padding: 1rem 1.75rem; border-top: 1px solid rgba(0,184,219,0.1); display: flex; align-items: center; justify-content: space-between; }
.cs-glass-tag { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; color: rgba(0,0,0,0.3); font-family: var(--font-display); }
.cs-glass-view { display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; font-weight: 700; color: var(--cyan); font-family: var(--font-display); transition: gap 0.2s; }
.cs-glass-card:hover .cs-glass-view { gap: 0.65rem; }

.cs-empty { text-align: center; padding: 5rem 2rem; color: var(--text-muted); }
.cs-empty p:first-child { font-size: 1rem; font-weight: 600; }
.cs-empty p:last-child { font-size: 0.875rem; margin-top: 0.5rem; }

/* GLOBAL REACH SECTION */
.cs-global { background: var(--ice); padding: 5rem 6rem; }
.cs-global-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 5fr 7fr; gap: 4rem; align-items: center; }
.cs-global-h2 { font-family: var(--font-display); font-size: clamp(1.6rem,2.5vw,2.2rem); font-weight: 700; color: var(--navy); margin-bottom: 2rem; }
.cs-global-items { display: flex; flex-direction: column; gap: 1.25rem; }
.cs-global-item { padding: 1.25rem 1.5rem; border-left: 4px solid var(--cyan); background: rgba(255,255,255,0.7); }
.cs-global-item-title { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--cyan); margin-bottom: 0.35rem; }
.cs-global-item-body { font-size: 0.9rem; color: var(--text-secondary); }
.cs-global-item.muted { border-left-color: var(--border); }
.cs-global-item.muted .cs-global-item-title { color: var(--text-muted); }
.cs-global-visual { position: relative; height: 440px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.12); border: 1px solid rgba(0,184,219,0.12); }
.cs-global-visual img { width: 100%; height: 100%; object-fit: cover; }
.cs-global-visual-overlay { position: absolute; inset: 0; background: rgba(13,33,55,0.2); }
.cs-global-badge { position: absolute; bottom: 2rem; right: 2rem; background: rgba(255,255,255,0.9); backdrop-filter: blur(12px); border-radius: 10px; padding: 1.25rem 1.5rem; }
.cs-global-badge-num { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--cyan); }
.cs-global-badge-lbl { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-top: 0.2rem; }

@media(max-width:1024px){
  .cs-hero { padding: 6rem 2rem 4rem; }
  .cs-filters-bar { padding: 1rem 2rem; }
  .cs-log { padding: 3.5rem 2rem; }
  .cs-glass-grid { grid-template-columns: repeat(2,1fr); }
  .cs-global { padding: 3.5rem 2rem; }
  .cs-global-inner { grid-template-columns: 1fr; gap: 2.5rem; }
  .cs-global-visual { height: 300px; }
}
@media(max-width:640px){
  .cs-hero h1 { font-size: clamp(1.8rem,8vw,2.4rem); }
  .cs-glass-grid { grid-template-columns: 1fr; }
  .cs-filter-group { gap: 0.35rem; }
}
</style>

{{-- ── HERO ── --}}
<section class="cs-hero">
  <div class="cs-hero-dots"></div>
  <div class="cs-hero-radar"></div>
  <div class="cs-hero-pings">
    <div class="cs-hero-ping" style="top:25%;left:25%;animation-delay:0s;"></div>
    <div class="cs-hero-ping" style="top:50%;left:75%;animation-delay:1s;"></div>
    <div class="cs-hero-ping" style="bottom:33%;left:50%;animation-delay:2s;"></div>
  </div>
  <div class="cs-hero-content">
    <span class="cs-hero-badge">Global Portfolio</span>
    <h1>Impact Built<br>From Here</h1>
    <p class="cs-hero-sub">Transforming the backbone of critical industries across the continent. From digital spirituality to surgical precision, we build the systems that sustain growth.</p>
    <div class="cs-hero-decorations">
      <div class="cs-hero-bar" style="width:6rem;background:var(--cyan);"></div>
      <div class="cs-hero-bar" style="width:3rem;background:rgba(255,255,255,0.2);"></div>
      <div class="cs-hero-bar" style="width:1.5rem;background:rgba(255,255,255,0.12);"></div>
    </div>
  </div>
  <div class="cs-hero-ornament"></div>
</section>

{{-- ── FILTER BAR ── --}}
<div class="cs-filters-bar">
  <div></div>
  <div class="cs-filter-group">
    <button class="cs-filter-btn active" onclick="setCsFilter(this)">ALL</button>
    <button class="cs-filter-btn" onclick="setCsFilter(this)">HEALTHCARE</button>
    <button class="cs-filter-btn" onclick="setCsFilter(this)">RELIGION</button>
    <button class="cs-filter-btn" onclick="setCsFilter(this)">DIASPORA</button>
  </div>
</div>

{{-- ── CASE STUDIES GRID ── --}}
<section class="cs-log">
  <div class="cs-log-header">
    <div>
      <h2 class="cs-log-h2">The Implementation Log</h2>
      <p class="cs-log-sub">Architecting resilience through tailored digital frameworks. Filtered by sector and strategic impact.</p>
    </div>
  </div>

  @if($studies->isEmpty())
  <div class="cs-empty" style="max-width:1200px;margin:0 auto;">
    <p>No case studies published yet.</p>
    <p>Case studies added from the admin will appear here.</p>
  </div>
  @else
  <div class="cs-glass-grid">
    @foreach($studies as $study)
    <a href="{{ route('site.case-studies.show', $study->slug) }}" wire:navigate class="cs-glass-card">
      <div class="cs-glass-img">
        @if($study->client_logo)
          <img src="{{ asset('storage/'.$study->client_logo) }}" alt="{{ $study->client_name }}">
        @else
          <div class="cs-glass-img-placeholder">
            <span style="font-family:var(--font-display);font-size:3rem;font-weight:800;color:rgba(0,184,219,0.35);">{{ strtoupper(substr($study->client_name ?? 'CS', 0, 2)) }}</span>
          </div>
        @endif
        @if($study->client_location)<span class="cs-glass-region">{{ strtoupper($study->client_location) }}</span>@elseif($study->is_featured)<span class="cs-glass-region">FEATURED</span>@endif
        <div class="cs-glass-scanline"></div>
      </div>
      <div class="cs-glass-body">
        @if($study->client_industry)
        <div class="cs-glass-sector">
          <span class="material-symbols-outlined cs-glass-sector-icon" style="font-size:0.95rem;">category</span>
          <span class="cs-glass-sector-txt">{{ $study->client_industry }}</span>
        </div>
        @endif
        @if($study->client_name)<div class="cs-glass-client">{{ $study->client_name }}</div>@endif
        <h3 class="cs-glass-title">{{ Str::limit($study->title, 80) }}</h3>
        @if($study->results)<p class="cs-glass-desc">{{ Str::limit($study->results, 100) }}</p>@endif
        @if($study->metrics)
        <div class="cs-glass-metrics">
          @foreach(array_slice(is_array($study->metrics) ? $study->metrics : [], 0, 2) as $m)
          <span class="cs-glass-metric">{{ $m['value'] ?? '' }} {{ $m['label'] ?? '' }}</span>
          @endforeach
        </div>
        @endif
      </div>
      <div class="cs-glass-footer">
        <span class="cs-glass-tag">#{{ Str::slug($study->client_industry ?? 'tech', '_') }}</span>
        <span class="cs-glass-view">VIEW DETAIL
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </span>
      </div>
    </a>
    @endforeach
  </div>
  @if($studies->hasPages())<div style="margin-top:2.5rem;max-width:1200px;margin-left:auto;margin-right:auto;">{{ $studies->links() }}</div>@endif
  @endif
</section>

{{-- ── GLOBAL REACH ── --}}
<section class="cs-global">
  <div class="cs-global-inner">
    <div>
      <h2 class="cs-global-h2">Engineering Presence, Everywhere.</h2>
      <div class="cs-global-items">
        <div class="cs-global-item">
          <div class="cs-global-item-title">Cloud Sovereignty</div>
          <div class="cs-global-item-body">Building local data centers to ensure regional data remains secure and accessible within borders.</div>
        </div>
        <div class="cs-global-item muted">
          <div class="cs-global-item-title">Architectural Integrity</div>
          <div class="cs-global-item-body">Systems designed for 99.99% uptime in environments with fluctuating power and connectivity.</div>
        </div>
      </div>
    </div>
    <div class="cs-global-visual">
      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxHI4jW5HiyfcWxOZlybkS8BQb-uSaWatLSQF7RVt-mxLSe6j2dU1c0SFHFgqssFi79p_JFCM3xbSt9r7o22xlxOmxpTv7Fp--M_Pzhe9ScA8YQd3M7Wfit7XLTCM5pvaUvSbAPp4D0URIWig5YH-H7arwp8D-bdzCQZxNOfpgSq-Wq0tJSmrOL52ssOXeBAfXKeoPbRp4BnMfbieGs18y1QkSM40FwyoXglDlDGzguzyi2pxqfCMdZGJGtlPTALtJp8gdr-T1_Ok" alt="Engineering infrastructure">
      <div class="cs-global-visual-overlay"></div>
      <div class="cs-global-badge">
        <div class="cs-global-badge-num">14+</div>
        <div class="cs-global-badge-lbl">Countries Deployed</div>
      </div>
    </div>
  </div>
</section>

<div class="site-cta-strip">
  <div>
    <h2>Want results like these?</h2>
    <p>Every case study started with a conversation. Let's talk about your project.</p>
  </div>
  <a href="{{ route('site.consulting') }}" wire:navigate class="btn-white-solid">Start a Conversation</a>
</div>

<script>
function setCsFilter(btn){
  document.querySelectorAll('.cs-filter-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
</div>
