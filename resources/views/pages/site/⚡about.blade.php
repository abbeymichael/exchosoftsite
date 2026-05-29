<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('About — Exchosoft Consult')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('pages.site.about');
    }
}; ?>

<div>
<style>
  /* ── ABOUT PAGE ── */
  .about-hero {
    position: relative; min-height: 600px; background: var(--navy);
    overflow: hidden; display: flex; align-items: flex-end; padding-top: 5rem;
  }
  .about-hero::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 6rem;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0)); pointer-events: none; z-index: 2;
  }
  .about-hero-canvas { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.8; }
  .about-hero-dots {
    position: absolute; inset: 0; z-index: 0;
    background-image: radial-gradient(circle, rgba(0,184,219,0.2) 1px, transparent 1px);
    background-size: 28px 28px; pointer-events: none; opacity: 0.6;
  }
  .about-hero-left-line {
    position: absolute; left: 5.5rem; top: 0; bottom: 0;
    width: 1px; background: linear-gradient(to bottom, transparent, rgba(0,184,219,0.3), transparent);
    pointer-events: none;
  }
  .about-hero-content {
    position: relative; z-index: 3;
    padding: 3rem 6rem 5rem; width: 100%; max-width: 780px;
  }
  .about-hero-crumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; }
  .about-hero-crumb a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .about-hero-crumb a:hover { color: var(--cyan); }
  .about-hero-crumb .sep { color: rgba(255,255,255,0.18); }
  .about-hero-crumb .current { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .about-hero-tag {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
    color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px;
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
    margin-bottom: 1.5rem;
  }
  .about-hero-tag span { width: 5px; height: 5px; border-radius: 50%; background: rgba(122,207,232,0.7); }
  .about-hero h1 {
    font-family: var(--font-display);
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800; color: var(--white);
    line-height: 1.05; letter-spacing: -0.04em; margin-bottom: 1.25rem;
  }
  .about-hero h1 .gradient {
    background: linear-gradient(135deg, #00b8db 0%, #7acfe8 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  }
  .about-hero-sub {
    font-size: 1rem; color: rgba(255,255,255,0.55);
    max-width: 560px; line-height: 1.75; font-weight: 300;
  }

  /* ── STORY ── */
  .about-section { padding: 5.5rem 6rem; }
  .story-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 5rem; align-items: start; }
  .story-left { position: sticky; top: 80px; }
  .story-tag { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; color: var(--cyan); text-transform: uppercase; margin-bottom: 0.75rem; }
  .story-h2 { font-family: var(--font-display); font-size: clamp(1.7rem,2.8vw,2.4rem); font-weight: 800; letter-spacing: -0.03em; color: var(--navy); line-height: 1.15; margin-bottom: 1.5rem; }
  .story-h2.light { color: var(--white); }
  .story-quote {
    margin-top: 1.5rem; padding: 1.25rem 1.5rem;
    border-left: 3px solid var(--cyan); border-radius: 0 8px 8px 0;
    background: var(--sky-light); position: relative;
  }
  .story-quote::before {
    content: '\201C'; font-family: var(--font-display); font-size: 4rem; line-height: 0;
    color: rgba(0,184,219,0.18); position: absolute; top: 1.5rem; left: 0.5rem;
  }
  .story-quote p { font-size: 1rem; color: var(--cyan-deep); font-style: italic; line-height: 1.7; font-weight: 400; }
  .story-dots { display: flex; gap: 0.6rem; align-items: center; margin-top: 1.5rem; }
  .story-dots span { width: 10px; height: 10px; border-radius: 50%; }
  .story-label { font-size: 0.75rem; color: rgba(13,33,55,0.4); font-family: var(--font-display); font-weight: 500; letter-spacing: 0.04em; margin-left: 0.25rem; }
  .story-right p { color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.85; font-size: 0.97rem; }
  .story-context-cards { margin-top: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .context-card { background: var(--ice); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; }
  .context-card h4 { font-family: var(--font-display); font-weight: 700; color: var(--navy); font-size: 0.88rem; margin-bottom: 0.4rem; }
  .context-card p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.65; }

  /* ── VALUES ── */
  .values-section { background: var(--navy); }
  .values-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; margin-top: 3rem; }

  /* ── PRESENCE ── */
  .presence-section { background: var(--ice); }
  .presence-inner { display: grid; grid-template-columns: 1.2fr 1fr; gap: 5rem; align-items: center; }
  .presence-text p { color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.8; font-size: 0.97rem; }
  .presence-stats { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem; margin-top: 2rem; }
  .pstat { background: var(--white); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; }
  .pstat-num { font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: var(--cyan); letter-spacing: -0.03em; }
  .pstat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }
  .presence-map {
    background: var(--navy); border-radius: 18px; padding: 2.5rem;
    position: relative; overflow: hidden; min-height: 340px;
    display: flex; flex-direction: column; justify-content: flex-end;
    border: 1px solid rgba(0,184,219,0.12);
  }
  .presence-map-dots { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(0,184,219,0.15) 1px, transparent 1px); background-size: 24px 24px; }
  .map-canvas { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
  .map-glow { position: absolute; top: 33%; left: 50%; transform: translate(-50%,-50%); width: 160px; height: 160px; background: rgba(0,184,219,0.08); border-radius: 50%; filter: blur(40px); pointer-events: none; }
  .map-locations { position: relative; z-index: 2; display: flex; flex-wrap: wrap; gap: 0.6rem; }
  .map-loc {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.75rem; color: rgba(255,255,255,0.7);
    background: rgba(255,255,255,0.05); border: 1px solid rgba(0,184,219,0.2);
    padding: 0.35rem 0.8rem; border-radius: 100px;
  }
  .map-loc-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

  /* ── TIMELINE ── */
  .timeline-section { background: var(--white); }
  .tl-wrap { position: relative; margin-top: 3rem; padding-left: 2.5rem; }
  .tl-wrap::before {
    content: ''; position: absolute; left: 0; top: 8px; bottom: 8px;
    width: 1px; background: linear-gradient(to bottom, rgba(0,184,219,0.6) 0%, rgba(0,184,219,0.1) 100%);
  }
  .tl-item { position: relative; padding: 0 0 2.5rem 2.5rem; }
  .tl-item:last-child { padding-bottom: 0; }
  .tl-dot {
    position: absolute; left: -2.5rem; top: 4px;
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--cyan); border: 2px solid var(--white);
    box-shadow: 0 0 0 2px var(--cyan); transform: translateX(calc(-50% + 0.5px));
  }
  .tl-year { font-family: var(--font-display); font-size: 0.72rem; font-weight: 700; color: var(--cyan); letter-spacing: 0.09em; text-transform: uppercase; margin-bottom: 0.4rem; }
  .tl-item h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.35rem; }
  .tl-item p { font-size: 0.88rem; color: var(--text-secondary); max-width: 560px; line-height: 1.8; }

  /* ── CTA ── */
  .about-cta-strip {
    background: var(--cyan); padding: 4rem 6rem;
    display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap;
  }
  .about-cta-strip h2 { font-family: var(--font-display); font-size: clamp(1.5rem,2.5vw,2rem); font-weight: 800; color: var(--white); margin-bottom: 0.4rem; letter-spacing: -0.02em; }
  .about-cta-strip p { color: rgba(255,255,255,0.78); max-width: 480px; font-size: 0.93rem; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }
  .about-hero h1 { animation: fadeUp 0.6s 0.2s ease both; }
  .about-hero-sub { animation: fadeUp 0.6s 0.35s ease both; }

  @media (max-width: 1024px) {
    .about-hero-left-line { display: none; }
    .about-hero-content { padding: 3rem 2rem 5rem; }
    .about-section { padding: 3.5rem 2rem; }
    .story-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    .story-left { position: static; }
    .values-grid { grid-template-columns: repeat(2,1fr); }
    .presence-inner { grid-template-columns: 1fr; gap: 2.5rem; }
    .about-cta-strip { padding: 3rem 2rem; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 640px) {
    .values-grid { grid-template-columns: 1fr; }
    .presence-stats { grid-template-columns: 1fr 1fr; }
    .story-context-cards { grid-template-columns: 1fr; }
    .about-hero-content { padding: 2.5rem 1.25rem 5rem; }
    .about-hero h1 { font-size: clamp(2rem, 9vw, 3rem); }
  }
</style>

{{-- ── HERO BANNER ── --}}
<header class="about-hero">
  <canvas class="about-hero-canvas" id="about-banner-canvas"></canvas>
  <div class="about-hero-dots"></div>
  <div class="about-hero-left-line"></div>
  <div class="about-hero-content">
    <div class="about-hero-crumb">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="sep">/</span>
      <span class="current">About Us</span>
    </div>
    <div class="about-hero-tag"><span></span> Our Story</div>
    <h1>Built From <span class="gradient">Here.</span><br>Built For Here.</h1>
    <p class="about-hero-sub">We are a Ghana-based technology consultancy that builds software for the real conditions of doing business across Africa, the Caribbean, and the diaspora — not the ideal conditions someone else imagined.</p>
  </div>
</header>

{{-- ── BRAND STORY ── --}}
<section class="about-section story-grid" style="background: var(--white);">
  <div class="story-left reveal">
    <p class="story-tag">Who We Are</p>
    <h2 class="story-h2">A Different Kind<br>of Tech Firm</h2>
    <div class="story-quote">
      <p>"We don't just acknowledge the realities of our clients — we build for them."</p>
    </div>
    <div class="story-dots">
      <span style="background:var(--cyan);"></span>
      <span style="background:var(--sky);"></span>
      <span style="background:rgba(0,184,219,0.3);border:1px solid var(--sky);"></span>
      <span class="story-label">Ghana · Caribbean · Diaspora</span>
    </div>
  </div>
  <div class="reveal" style="transition-delay:0.15s;">
    <p>Exchosoft Consult grew out of a frustration that many technology firms share but few admit: most software is built for conditions that don't exist in the markets that need it most. Reliable power. Constant internet. Expensive devices. Formal business structures.</p>
    <p>We started building differently. Offline-first architecture wasn't a feature we added — it was a fundamental decision made because our first hospital client needed patient records accessible during outages. LAN collaboration wasn't a buzzword — it was a solution for a pharmacy chain whose locations couldn't always reach the internet but needed to operate as one business.</p>
    <p>Over time, we built systems for churches managing hundreds of members and complex finances. For laboratories tracking samples under strict compliance. For heritage organizations preserving cultural history across continents. For financial institutions where reliability isn't optional.</p>
    <p>Each project taught us something different. Together, they gave us a breadth of experience that makes us useful to any business operating in our markets — not because we have a template for everything, but because we've genuinely solved hard problems across almost every sector.</p>
    <p>We are a small, experienced team. We don't outsource your project to junior developers. When you work with Exchosoft, you work with the people who built the systems you'll see in our portfolio.</p>
    <div class="story-context-cards">
      <div class="context-card">
        <h4>Small &amp; Experienced</h4>
        <p>We don't outsource to juniors. You work with the people who built the systems in our portfolio.</p>
      </div>
      <div class="context-card">
        <h4>Rooted in Context</h4>
        <p>We understand the infrastructure and business cultures of our markets because we operate within them.</p>
      </div>
    </div>
  </div>
</section>

{{-- ── CORE VALUES ── --}}
<section class="about-section values-section">
  <div class="reveal">
    <p class="story-tag" style="color:var(--sky);">What Drives Us</p>
    <h2 class="story-h2 light">Six Principles We Don't<br>Compromise On</h2>
  </div>
  <div class="values-grid">
    @foreach([
      ['01','Reality over theory','We build for the world as it is — intermittent power, inconsistent connectivity, mobile-first users — not the world as it should be.', '0'],
      ['02','Custom over compromise','Off-the-shelf forces your business into someone else\'s model. Every system we build starts from understanding how you actually work.', '0.07s'],
      ['03','Long-term over transactional','We are not project vendors. We build relationships because software that grows with a business requires a partner who understands it.', '0.14s'],
      ['04','Local knowledge, global standards','Understanding Accra, Lagos, Kingston, and London as markets means building technology that works across all of them without compromise.', '0.21s'],
      ['05','Reliability as a foundation','From hospital records to financial ledgers, we build systems where failure is not an option — architecturally, not just aspirationally.', '0.28s'],
      ['06','Clarity in every engagement','We tell clients what they need, not what they want to hear. Honest technology consulting produces better outcomes than comfortable ones.', '0.35s'],
    ] as [$num, $title, $text, $delay])
    <div class="value-card" style="background:rgba(255,255,255,0.03);border:1px solid rgba(0,184,219,0.12);border-radius:16px;padding:1.75rem;transition-delay:{{ $delay }};">
      <div style="font-family:var(--font-display);font-size:2.5rem;font-weight:800;color:rgba(0,184,219,0.12);line-height:1;margin-bottom:1rem;">{{ $num }}</div>
      <h3 style="font-family:var(--font-display);font-weight:700;color:var(--white);font-size:0.95rem;margin-bottom:0.6rem;">{{ $title }}</h3>
      <p style="font-size:0.87rem;color:rgba(255,255,255,0.5);line-height:1.8;font-weight:300;">{{ $text }}</p>
    </div>
    @endforeach
  </div>
</section>

{{-- ── GLOBAL PRESENCE ── --}}
<section class="about-section presence-section">
  <div class="presence-inner">
    <div class="reveal">
      <p class="story-tag">Where We Work</p>
      <h2 class="story-h2">Accra-Based.<br>Continent-Minded.</h2>
      <div class="presence-text">
        <p>Our headquarters and core team are in Accra, Ghana. But our work spans West Africa, the United Kingdom, the Caribbean, and diaspora communities across North America and Europe.</p>
        <p>We understand the operational conditions, infrastructure realities, and business cultures of these markets because we operate within them — not from the outside looking in.</p>
      </div>
      <div class="presence-stats">
        @foreach([['3','Continents with active clients'],['10+','Industries served'],['100%','Custom-built solutions'],['0','Off-the-shelf compromises']] as [$n,$l])
        <div class="pstat"><div class="pstat-num">{{ $n }}</div><div class="pstat-label">{{ $l }}</div></div>
        @endforeach
      </div>
    </div>
    <div class="presence-map reveal" style="transition-delay:0.15s;">
      <div class="presence-map-dots"></div>
      <canvas class="map-canvas" id="about-map-canvas"></canvas>
      <div class="map-glow"></div>
      <div class="map-locations">
        @foreach([
          ['Accra, Ghana','var(--cyan)'],
          ['Lagos, Nigeria','var(--cyan)'],
          ['London, UK','var(--sky)'],
          ['Kingston, Jamaica','var(--sky)'],
          ['Atlanta, USA','rgba(122,207,232,0.6)'],
          ['Kumasi, Ghana','var(--cyan)'],
        ] as [$loc, $color])
        <div class="map-loc"><span class="map-loc-dot" style="background:{{ $color }};"></span> {{ $loc }}</div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ── JOURNEY TIMELINE ── --}}
<section class="about-section timeline-section">
  <div class="reveal">
    <p class="story-tag">Our Journey</p>
    <h2 class="story-h2">How We Got Here</h2>
  </div>
  <div class="tl-wrap tl-track">
    @foreach([
      ['The Beginning','First healthcare system built offline-first','Our first major project was a hospital management system for a client who couldn\'t afford downtime during outages. The offline-first architecture we developed became our signature approach.'],
      ['Expansion','Faith-based and service sectors','We extended our work into churches, pharmacies, and laundry businesses across Ghana — proving that the same architectural principles applied across very different operational contexts.'],
      ['Cross-Continental','Diaspora and heritage organizations','Partnerships with Black History Walks, African Odysseys, and the African Caribbean Summit brought our work to the UK and beyond — building platforms that serve diaspora communities globally.'],
      ['Financial Sector','Ghana Union Assurance and ACIS','Entering insurance and financial services required us to meet the highest standards of security, compliance, and reliability. We met them — and learned what institutional-grade software really means.'],
      ['Today','Full-spectrum tech consultancy','Exchosoft now offers the full range: custom software development, technology consulting, system architecture, digital transformation, and ongoing partnership across every sector we\'ve served.'],
    ] as [$year, $title, $desc])
    <div class="tl-item reveal">
      <div class="tl-dot"></div>
      <div class="tl-year">{{ $year }}</div>
      <h3>{{ $title }}</h3>
      <p>{{ $desc }}</p>
    </div>
    @endforeach
  </div>
</section>

{{-- ── CTA STRIP ── --}}
<div class="about-cta-strip" id="contact">
  <div>
    <h2>Let's build something that actually works</h2>
    <p>Every business is different. Every challenge is unique. Let's talk about yours.</p>
  </div>
  <a class="btn-white-solid" href="{{ route('site.consulting') }}" wire:navigate>Schedule a Consultation</a>
</div>

{{-- ── CANVAS SCRIPTS ── --}}
<script>
(function() {
  // Banner particle + radar canvas
  const bc = document.getElementById('about-banner-canvas');
  if (bc) {
    const ctx = bc.getContext('2d');
    let W, H;
    function resize() { W = bc.width = bc.offsetWidth; H = bc.height = bc.offsetHeight; }
    resize(); window.addEventListener('resize', resize);
    const particles = Array.from({length: 70}, () => ({
      x: Math.random(), y: Math.random(),
      vx: (Math.random()-0.5)*0.0001, vy: (Math.random()-0.5)*0.0001,
      r: Math.random()*1.5+0.5, a: Math.random()*0.4+0.1
    }));
    const radar = { cx: 0.75, cy: 0.45, angle: 0, speed: 0.02, maxR: 260, pings: [], pulses: [] };
    function spawnPing(a) {
      const d = 50 + Math.random()*(radar.maxR-70);
      radar.pings.push({ x: radar.cx*W+Math.cos(a)*d, y: radar.cy*H+Math.sin(a)*d, life: 1 });
    }
    function draw() {
      ctx.clearRect(0,0,W,H);
      particles.forEach(p => {
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0) p.x=1; if (p.x > 1) p.x=0;
        if (p.y < 0) p.y=1; if (p.y > 1) p.y=0;
        ctx.beginPath(); ctx.arc(p.x*W, p.y*H, p.r, 0, Math.PI*2);
        ctx.fillStyle = `rgba(0,184,219,${p.a})`; ctx.fill();
      });
      const rx = radar.cx*W, ry = radar.cy*H;
      [0.3,0.6,1.0].forEach(f => {
        ctx.beginPath(); ctx.arc(rx,ry,radar.maxR*f,0,Math.PI*2);
        ctx.strokeStyle='rgba(0,184,219,0.1)'; ctx.lineWidth=1; ctx.stroke();
      });
      ctx.beginPath(); ctx.moveTo(rx-18,ry); ctx.lineTo(rx+18,ry); ctx.moveTo(rx,ry-18); ctx.lineTo(rx,ry+18);
      ctx.strokeStyle='rgba(0,184,219,0.2)'; ctx.stroke();
      radar.angle += radar.speed;
      if (Math.random() < 0.02) spawnPing(radar.angle);
      ctx.save(); ctx.translate(rx,ry); ctx.rotate(radar.angle);
      const grd = ctx.createConicGradient(-Math.PI/2, 0, 0);
      grd.addColorStop(0,'rgba(0,184,219,0.4)'); grd.addColorStop(0.15,'rgba(0,184,219,0)');
      ctx.beginPath(); ctx.moveTo(0,0); ctx.arc(0,0,radar.maxR,0,-Math.PI/3,true); ctx.closePath();
      ctx.fillStyle=grd; ctx.fill();
      ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(radar.maxR,0);
      ctx.strokeStyle='rgba(122,207,232,0.5)'; ctx.lineWidth=2; ctx.stroke();
      ctx.restore();
      radar.pulses.forEach((p,i) => {
        p.r+=2.5; p.life-=0.005;
        if (p.life<=0) { radar.pulses.splice(i,1); return; }
        ctx.beginPath(); ctx.arc(rx,ry,p.r,0,Math.PI*2);
        ctx.strokeStyle=`rgba(0,184,219,${p.life*0.2})`; ctx.stroke();
      });
      radar.pings.forEach((p,i) => {
        p.life-=0.01;
        if (p.life<=0) { radar.pings.splice(i,1); return; }
        ctx.beginPath(); ctx.arc(p.x,p.y,3*p.life,0,Math.PI*2);
        ctx.fillStyle=`rgba(122,207,232,${p.life})`; ctx.fill();
      });
      requestAnimationFrame(draw);
    }
    draw();
  }

  // Map canvas
  const mc = document.getElementById('about-map-canvas');
  if (mc) {
    const ctx = mc.getContext('2d');
    let W, H;
    function resize() { W = mc.width = mc.offsetWidth; H = mc.height = mc.offsetHeight; }
    resize(); window.addEventListener('resize', resize);
    const nodes = [{x:0.3,y:0.4},{x:0.5,y:0.3},{x:0.7,y:0.5},{x:0.4,y:0.7}];
    function draw() {
      ctx.clearRect(0,0,W,H);
      const time = Date.now()/1500;
      nodes.forEach((n,i) => {
        const nx=n.x*W, ny=n.y*H;
        nodes.forEach((n2,j) => {
          if (i===j) return;
          ctx.beginPath(); ctx.moveTo(nx,ny); ctx.lineTo(n2.x*W,n2.y*H);
          ctx.strokeStyle='rgba(0,184,219,0.08)'; ctx.lineWidth=1; ctx.stroke();
        });
        const p = (time+i/nodes.length)%1;
        ctx.beginPath(); ctx.arc(nx,ny,p*30,0,Math.PI*2);
        ctx.strokeStyle=`rgba(0,184,219,${0.4*(1-p)})`; ctx.stroke();
        ctx.beginPath(); ctx.arc(nx,ny,3,0,Math.PI*2);
        ctx.fillStyle='#00b8db'; ctx.fill();
      });
      requestAnimationFrame(draw);
    }
    draw();
  }
})();
</script>

</div>
