<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Our Services — Exchosoft Consult')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.services');
    }
}; ?>

<div>
<style>
  /* ── SERVICES PAGE ── */
  .services-hero {
    min-height: 580px; background: var(--navy);
    position: relative; overflow: hidden;
    display: grid; grid-template-columns: 1fr 1fr;
    align-items: center; padding: 4rem 0;
  }
  .services-hero-dots { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(0,184,219,0.15) 1px, transparent 1px); background-size: 30px 30px; opacity: 0.5; pointer-events: none; }
  .services-hero-particles { position: absolute; inset: 0; pointer-events: none; }
  .services-hero-content { position: relative; z-index: 2; padding: 2rem 6rem; }
  .services-hero-crumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
  .services-hero-crumb a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .services-hero-crumb a:hover { color: var(--cyan); }
  .services-hero-crumb .sep { color: rgba(255,255,255,0.18); }
  .services-hero-crumb .current { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .services-hero-tag {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(0,184,219,0.12); border: 1px solid rgba(0,184,219,0.25);
    color: var(--cyan); padding: 0.3rem 0.85rem; border-radius: 100px;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
    margin-bottom: 1.5rem;
  }
  .services-hero h1 {
    font-family: var(--font-display); font-size: clamp(2.4rem,4.5vw,3.8rem);
    font-weight: 800; color: var(--white); line-height: 1.08; letter-spacing: -0.03em; margin-bottom: 1.25rem;
  }
  .services-hero h1 em { color: var(--cyan); font-style: normal; }
  .services-hero-sub { font-size: 1rem; color: rgba(255,255,255,0.6); max-width: 500px; line-height: 1.75; font-weight: 300; margin-bottom: 2rem; }
  .services-hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
  .btn-primary-cta {
    background: var(--cyan); color: var(--white); padding: 0.85rem 2rem; border-radius: 8px;
    font-family: var(--font-display); font-size: 0.93rem; font-weight: 600;
    text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block;
  }
  .btn-primary-cta:hover { background: var(--cyan-dark); transform: translateY(-1px); }
  .btn-outline-white {
    background: transparent; color: var(--white); padding: 0.85rem 2rem; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.2); font-family: var(--font-display); font-size: 0.93rem; font-weight: 600;
    text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block;
  }
  .btn-outline-white:hover { border-color: var(--cyan); background: rgba(0,184,219,0.08); }

  /* Radar Vis */
  .services-hero-visual { position: relative; z-index: 2; display: flex; align-items: center; justify-content: center; padding: 2rem; }
  .radar-system {
    position: relative; width: 560px; height: 560px;
    display: flex; align-items: center; justify-content: center;
  }
  .radar-ring {
    position: absolute; border: 1px solid rgba(0,184,219,0.15);
    border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%,-50%);
  }
  .radar-sweep-v2 {
    position: absolute; width: 100%; height: 100%; border-radius: 50%;
    background: conic-gradient(from 0deg, rgba(0,184,219,0.35) 0deg, transparent 90deg);
    animation: rotateSvc 6s linear infinite; z-index: 2;
  }
  .radar-line-v2 {
    position: absolute; width: 50%; height: 2px;
    background: linear-gradient(to right, rgba(0,230,255,0.8), rgba(0,230,255,0));
    top: 50%; left: 50%; transform-origin: left center;
    box-shadow: 0 0 12px rgba(0,184,219,0.6); z-index: 3;
    animation: rotateSvc 6s linear infinite;
  }
  @keyframes rotateSvc { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .radar-core-center {
    width: 72px; height: 72px; background: var(--navy-mid); border-radius: 14px;
    display: flex; align-items: center; justify-content: center; z-index: 20;
    border: 1px solid rgba(0,184,219,0.3); box-shadow: 0 0 30px rgba(0,184,219,0.15);
  }
  .radar-core-center svg { width: 34px; height: 34px; stroke: var(--cyan); fill: none; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
  .svc-node {
    position: absolute; width: 44px; height: 44px;
    background: rgba(13,33,55,0.85); border: 1px solid rgba(0,184,219,0.2);
    border-radius: 11px; display: flex; align-items: center; justify-content: center;
    z-index: 10; transition: all 0.3s ease;
    top: 50%; left: 50%; margin-left: -22px; margin-top: -22px;
    backdrop-filter: blur(4px);
  }
  .svc-node.lit { border-color: rgba(0,184,219,0.7); box-shadow: 0 0 18px rgba(0,184,219,0.4); background: rgba(0,184,219,0.12); transform: scale(1.12); }
  .svc-node svg { width: 20px; height: 20px; stroke: var(--cyan); fill: none; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; opacity: 0.7; }
  .svc-node.lit svg { opacity: 1; filter: drop-shadow(0 0 4px rgba(0,230,255,0.8)); }

  /* ── CORE SERVICES ── */
  .services-body { padding: 5rem 6rem; background: var(--white); }
  .services-section-header { display: flex; flex-direction: row; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; gap: 2rem; flex-wrap: wrap; }
  .services-section-header .left { max-width: 580px; }
  .services-version { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.08em; color: var(--sky); background: var(--navy); padding: 0.35rem 0.9rem; border-radius: 100px; font-family: var(--font-display); }
  .svc-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
  .svc-card {
    background: rgba(255,255,255,0.6); backdrop-filter: blur(16px);
    border: 1.5px solid rgba(0,184,219,0.2); border-radius: 16px;
    padding: 2.5rem; position: relative; overflow: hidden;
    transition: all 0.4s ease;
  }
  .svc-card:hover { box-shadow: 0 20px 60px rgba(0,184,219,0.1); transform: translateY(-3px); border-color: rgba(0,184,219,0.4); }
  .svc-card-ghost-icon {
    position: absolute; top: 0; right: 0; padding: 1.5rem;
    opacity: 0.07; transition: opacity 0.35s;
  }
  .svc-card:hover .svc-card-ghost-icon { opacity: 0.14; }
  .svc-card-ghost-icon svg { width: 100px; height: 100px; stroke: var(--cyan); fill: none; stroke-width: 1; }
  .svc-icon-wrap { width: 48px; height: 48px; border-radius: 12px; background: rgba(0,184,219,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; }
  .svc-icon-wrap svg { width: 26px; height: 26px; stroke: var(--cyan); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
  .svc-card h3 { font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.85rem; }
  .svc-card p { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.8; flex: 1; }
  .svc-card-link { display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem; color: var(--cyan); font-family: var(--font-display); font-size: 0.82rem; font-weight: 700; transition: gap 0.2s; }
  .svc-card:hover .svc-card-link { gap: 0.75rem; }

  /* ── METHODOLOGY ── */
  .methodology-section { padding: 5rem 6rem; background: var(--navy); position: relative; overflow: hidden; }
  .methodology-dots { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(0,184,219,0.1) 1px, transparent 1px); background-size: 28px 28px; opacity: 0.5; }
  .methodology-section h2 { font-family: var(--font-display); font-size: clamp(1.8rem,3vw,2.5rem); font-weight: 800; color: var(--white); letter-spacing: -0.03em; margin-bottom: 0.75rem; }
  .methodology-section > .sub { font-size: 1rem; color: rgba(255,255,255,0.5); max-width: 600px; margin: 0 auto 4rem; font-weight: 300; }
  .method-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.25rem; position: relative; }
  .method-connector { position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: rgba(255,255,255,0.06); pointer-events: none; }
  .method-step {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(0,184,219,0.12);
    border-radius: 14px; padding: 2rem; position: relative; z-index: 2;
    transition: border-color 0.25s, background 0.25s;
  }
  .method-step:hover { border-color: rgba(0,184,219,0.35); background: rgba(0,184,219,0.06); }
  .method-num {
    width: 44px; height: 44px; border-radius: 10px;
    background: rgba(0,184,219,0.15); display: flex; align-items: center; justify-content: center;
    font-family: var(--font-display); font-size: 0.82rem; font-weight: 700;
    color: var(--cyan); margin-bottom: 1.5rem;
  }
  .method-step h4 { font-family: var(--font-display); font-weight: 700; color: var(--white); margin-bottom: 0.75rem; font-size: 1rem; }
  .method-step p { font-size: 0.87rem; color: rgba(255,255,255,0.5); line-height: 1.75; font-weight: 300; }

  /* ── WHY US ── */
  .why-services { background: var(--ice); padding: 5rem 6rem; }
  .why-svcs-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 1.5rem; margin-top: 3rem; }
  .why-svc-item { display: flex; gap: 1.25rem; padding: 1.75rem; background: var(--white); border: 1px solid var(--border); border-radius: 14px; transition: border-color 0.2s, box-shadow 0.2s; }
  .why-svc-item:hover { border-color: var(--cyan); box-shadow: 0 8px 28px rgba(0,184,219,0.1); }
  .why-svc-bar { width: 3px; background: var(--cyan); border-radius: 3px; flex-shrink: 0; }
  .why-svc-item h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.4rem; }
  .why-svc-item p { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.75; }

  @media (max-width: 1024px) {
    .services-hero { grid-template-columns: 1fr; padding: 2rem 0 0; }
    .services-hero-visual { display: none; }
    .services-hero-content { padding: 1.5rem 2rem 4rem; }
    .services-body { padding: 3.5rem 2rem; }
    .svc-grid { grid-template-columns: 1fr; }
    .methodology-section { padding: 3.5rem 2rem; }
    .method-grid { grid-template-columns: repeat(2,1fr); }
    .method-connector { display: none; }
    .why-services { padding: 3.5rem 2rem; }
    .why-svcs-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 640px) {
    .services-hero-content { padding: 1.5rem 1.25rem 3.5rem; }
    .method-grid { grid-template-columns: 1fr; }
  }
</style>

{{-- ── HERO ── --}}
<section class="services-hero" id="top">
  <div class="services-hero-dots"></div>
  <div class="services-hero-particles" id="svc-particles"></div>
  <div class="services-hero-content">
    <div class="services-hero-crumb">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="sep">/</span>
      <span class="current">Services</span>
    </div>
    <div class="services-hero-tag">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      Precision Engineering
    </div>
    <h1>Consultancy Built for<br><em>Real-World</em> Complexity</h1>
    <p class="services-hero-sub">We design and deliver technical architectures that solve the infrastructure and operational challenges of emerging markets. Built here. Proven here.</p>
    <div class="services-hero-btns">
      <a href="{{ route('site.consulting') }}" wire:navigate class="btn-primary-cta">Explore Our Services</a>
      <a href="{{ route('site.case-studies') }}" wire:navigate class="btn-outline-white">View Case Studies</a>
    </div>
  </div>
  <div class="services-hero-visual">
    <div class="radar-system" id="svc-radar">
      <div class="radar-ring" style="width:100px;height:100px;"></div>
      <div class="radar-ring" style="width:200px;height:200px;"></div>
      <div class="radar-ring" style="width:300px;height:300px;"></div>
      <div class="radar-ring" style="width:420px;height:420px;"></div>
      <div class="radar-sweep-v2"></div>
      <div class="radar-line-v2"></div>
      <div class="radar-core-center">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/></svg>
      </div>
      {{-- Service nodes injected by JS --}}
    </div>
  </div>
</section>

{{-- ── CORE SERVICE PILLARS ── --}}
<section class="services-body" id="custom-dev">
  <div class="services-section-header">
    <div class="left">
      <p class="section-tag-label">What We Offer</p>
      <h2 class="section-h2">Core Service Pillars</h2>
      <p style="color:var(--text-secondary);margin-top:0.5rem;font-size:0.95rem;">High-velocity technology meets architectural stability. Our services are engineered to endure.</p>
    </div>
    <span class="services-version">v4.2 STABLE</span>
  </div>
  <div class="svc-grid">
    @foreach([
      [
        'title' => 'Custom Software Development',
        'desc'  => 'Focus on offline-first, enterprise-grade desktop and hybrid systems. We build resilient applications that maintain data integrity regardless of connectivity. No templates, no compromise — built for your exact workflows.',
        'icon'  => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
        'ghost' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>'
      ],
      [
        'title' => 'Strategic Tech Consulting',
        'desc'  => 'Expert guidance on digital transformation and system modernization. We align your technical trajectory with long-term business goals and help you avoid costly architectural mistakes before they happen.',
        'icon'  => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'ghost' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'
      ],
      [
        'title' => 'System Architecture & Design',
        'desc'  => 'Specializing in LAN collaboration, cloud synchronization, and resilient infrastructures designed for architectural permanence. Offline-first is a core architectural decision, not an afterthought.',
        'icon'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'ghost' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'
      ],
      [
        'title' => 'Business Process Analysis',
        'desc'  => 'Identifying bottlenecks and optimizing workflows with technical solutions that bridge the gap between human operation and machine efficiency. We understand your business before we write a single line of code.',
        'icon'  => '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>',
        'ghost' => '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>'
      ],
      [
        'title' => 'Digital Transformation',
        'desc'  => 'Complete operational modernization that respects how your business actually works, rather than forcing you into someone else\'s model. We bring traditional businesses into the digital age without disruption.',
        'icon'  => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12h8M12 8v8"/>',
        'ghost' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12h8M12 8v8"/>'
      ],
      [
        'title' => 'Ongoing Support & Evolution',
        'desc'  => 'Technology needs change as businesses grow. We provide continued consultation and development as your needs evolve — because a system that can\'t adapt is a system that will eventually fail you.',
        'icon'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'ghost' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'
      ],
    ] as $svc)
    <div class="svc-card reveal">
      <div class="svc-card-ghost-icon"><svg viewBox="0 0 24 24">{!! $svc['ghost'] !!}</svg></div>
      <div class="svc-icon-wrap"><svg viewBox="0 0 24 24">{!! $svc['icon'] !!}</svg></div>
      <h3>{{ $svc['title'] }}</h3>
      <p>{{ $svc['desc'] }}</p>
      <div class="svc-card-link">
        <span>Learn more</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- ── METHODOLOGY ── --}}
<section class="methodology-section" id="consulting">
  <div class="methodology-dots"></div>
  <div style="position:relative;z-index:2;text-align:center;">
    <p class="section-tag-label sky" style="color:var(--sky);">Our Approach</p>
    <h2>The Methodology</h2>
    <p class="sub">A rigorous, four-phase approach to solving industrial-scale technical challenges with precision and permanence.</p>
  </div>
  <div class="method-grid" style="position:relative;z-index:2;">
    <div class="method-connector"></div>
    @foreach([
      ['01','Discovery','In-depth immersion into your environment to identify real-world constraints, operational goals, and technical debt before we propose solutions.'],
      ['02','Architecture','Engineering technical blueprints that prioritize resilience, scalability, and structural integrity — built for your market conditions.'],
      ['03','Development','Agile construction phase with a focus on code quality, security architecture, and performance tuned for offline-first realities.'],
      ['04','Evolution','Continuous monitoring and optimization to ensure the system evolves with your organization, your market, and your needs.'],
    ] as [$num, $title, $desc])
    <div class="method-step reveal">
      <div class="method-num">{{ $num }}</div>
      <h4>{{ $title }}</h4>
      <p>{{ $desc }}</p>
    </div>
    @endforeach
  </div>
</section>

{{-- ── WHY EXCHOSOFT ── --}}
<section class="why-services">
  <p class="section-tag-label">Why Choose Us</p>
  <h2 class="section-h2">The Exchosoft Difference</h2>
  <div class="why-svcs-grid">
    @foreach([
      ['Built for African Realities','Every service we provide is shaped by direct experience building systems that work under real African operating conditions — intermittent power, variable connectivity, and mobile-first users.'],
      ['No Junior Outsourcing','When you work with Exchosoft, you work with the senior engineers who actually build your system — not a team of junior developers managed from a distance.'],
      ['Long-Term Partnership','We are not just project vendors. We engage with clients as long-term partners, providing continued development as your business grows and evolves.'],
      ['Custom From the Ground Up','Every engagement starts from a blank canvas. We build for your specific workflows, your specific constraints, and your specific market — not a generic template.'],
    ] as [$title, $body])
    <div class="why-svc-item reveal">
      <div class="why-svc-bar"></div>
      <div>
        <h3>{{ $title }}</h3>
        <p>{{ $body }}</p>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- ── CTA ── --}}
<div class="site-cta-strip" id="contact">
  <div>
    <h2>Let's Solve Your Hardest Problem</h2>
    <p>Industrial reliability isn't a goal — it's our foundation. Partner with us to build technology that lasts.</p>
  </div>
  <div style="display:flex;gap:1rem;flex-wrap:wrap;flex-shrink:0;">
    <a href="{{ route('site.consulting') }}" wire:navigate class="btn-white-solid">Schedule a Consultation</a>
    <a href="{{ route('site.case-studies') }}" wire:navigate class="btn-white-solid" style="background:transparent;color:var(--white);border:1px solid rgba(255,255,255,0.4);">View Case Studies</a>
  </div>
</div>

<script>
(function() {
  // Particles for hero background
  const pc = document.getElementById('svc-particles');
  if (pc) {
    for (let i = 0; i < 35; i++) {
      const p = document.createElement('div');
      Object.assign(p.style, {
        position: 'absolute', width: '2px', height: '2px',
        background: '#4cd9fd', borderRadius: '50%', filter: 'blur(1px)',
        pointerEvents: 'none', left: Math.random()*100+'%', top: Math.random()*100+'%',
        opacity: Math.random()*0.3
      });
      pc.appendChild(p);
      p.animate([
        {transform: 'translate(0,0)', opacity: 0},
        {opacity: 0.3, offset: 0.2},
        {transform: `translate(${(Math.random()-0.5)*180}px,${(Math.random()-0.5)*180}px)`, opacity: 0}
      ], { duration: (10+Math.random()*20)*1000, iterations: Infinity, delay: Math.random()*5000, easing: 'linear' });
    }
  }

  // Service nodes around radar
  const svcDefs = [
    { name:'Software',     angle:0,    r:190, svg:'<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>' },
    { name:'Consulting',   angle:60,   r:190, svg:'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>' },
    { name:'Architecture', angle:120,  r:190, svg:'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>' },
    { name:'Analysis',     angle:180,  r:190, svg:'<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>' },
    { name:'Transform',    angle:240,  r:190, svg:'<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12h8M12 8v8"/>' },
    { name:'Support',      angle:300,  r:190, svg:'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>' },
  ];
  const radarEl = document.getElementById('svc-radar');
  if (radarEl) {
    const nodeEls = svcDefs.map(s => {
      const el = document.createElement('div');
      el.className = 'svc-node';
      const rad = (s.angle * Math.PI) / 180;
      const x = s.r * Math.cos(rad), y = s.r * Math.sin(rad);
      el.style.transform = `translate(${x}px, ${y}px)`;
      el.innerHTML = `<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#00b8db" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${s.svg}</svg>`;
      el.title = s.name;
      radarEl.appendChild(el);
      return { el, angle: s.angle };
    });

    let sweepDeg = 0;
    function tick() {
      sweepDeg = (sweepDeg + 1) % 360;
      nodeEls.forEach(n => {
        const diff = Math.abs(sweepDeg - n.angle) % 360;
        if (diff < 18 || diff > 342) n.el.classList.add('lit');
        else n.el.classList.remove('lit');
      });
      requestAnimationFrame(tick);
    }
    tick();
  }
})();
</script>
</div>
