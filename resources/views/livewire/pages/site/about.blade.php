<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('About — Exchosoft Consult')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.about');
    }
}; ?>

<div>
<style>
  /* ABOUT PAGE */
  .about-banner {
    min-height: 520px; background: var(--navy);
    position: relative; overflow: hidden;
    display: flex; align-items: center;
  }
  .about-banner-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(0,184,219,0.18) 1px, transparent 1px);
    background-size: 32px 32px; pointer-events: none;
  }
  .about-banner-accent {
    position: absolute; left: 6rem; top: 0; bottom: 0;
    width: 2px; background: var(--cyan); opacity: 0.35;
  }
  .about-banner-glow {
    position: absolute; inset: 0;
    background-image:
      radial-gradient(circle at 72% 45%, rgba(0,184,219,0.12) 0%, transparent 60%),
      radial-gradient(circle at 82% 75%, rgba(122,207,232,0.06) 0%, transparent 50%),
      radial-gradient(circle at 58% 30%, rgba(0,145,173,0.06) 0%, transparent 50%);
    pointer-events: none;
  }
  .about-banner-content {
    position: relative; z-index: 2;
    padding: 4rem 6rem; max-width: 700px;
  }
  .about-breadcrumb {
    display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;
  }
  .about-breadcrumb a { font-size: 0.8rem; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.2s; }
  .about-breadcrumb a:hover { color: var(--cyan); }
  .about-breadcrumb .sep { color: rgba(255,255,255,0.2); }
  .about-breadcrumb .current { font-size: 0.8rem; color: var(--cyan); font-weight: 500; }
  .about-banner-tag {
    display: inline-flex; align-items: center;
    background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
    color: var(--sky); padding: 0.3rem 0.85rem; border-radius: 100px;
    font-size: 0.75rem; font-weight: 500; letter-spacing: 0.05em;
    margin-bottom: 1.5rem;
  }
  .about-banner h1 {
    font-family: var(--font-display);
    font-size: clamp(2.2rem, 4vw, 3.4rem);
    font-weight: 800; color: var(--white);
    line-height: 1.1; letter-spacing: -0.03em;
    margin-bottom: 1.25rem;
  }
  .about-banner h1 em { color: var(--cyan); font-style: normal; }
  .about-banner-sub {
    font-size: 1rem; color: rgba(255,255,255,0.6);
    max-width: 520px; line-height: 1.75; font-weight: 300;
  }
  .about-banner-pings {
    position: absolute; right: 5rem; bottom: 3rem; z-index: 3;
    display: flex; flex-direction: column; gap: 0.6rem;
  }
  .ping-label {
    display: flex; align-items: center; gap: 0.6rem;
    font-size: 0.75rem; color: rgba(255,255,255,0.5);
  }
  .ping-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
  }
  .ping-dot.cyan { background: var(--cyan); }
  .ping-dot.sky { background: var(--sky); }
  .ping-dot.muted { background: rgba(122,207,232,0.35); border: 1px solid var(--sky); }

  /* SECTIONS */
  .about-section { padding: 5.5rem 6rem; }
  .section-tag-sm {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em;
    color: var(--cyan); text-transform: uppercase; margin-bottom: 0.75rem;
  }
  .section-tag-sm.sky { color: var(--sky); }
  .about-h2 {
    font-family: var(--font-display); font-size: clamp(1.7rem, 2.8vw, 2.4rem);
    font-weight: 800; letter-spacing: -0.03em; color: var(--navy); line-height: 1.15;
    margin-bottom: 1.25rem;
  }
  .about-h2.light { color: var(--white); }

  /* STORY */
  .story-section {
    background: var(--white);
    display: grid; grid-template-columns: 1fr 1.4fr; gap: 5rem; align-items: start;
  }
  .story-left { position: sticky; top: 80px; }
  .story-quote {
    margin-top: 2rem; padding: 1.5rem;
    border-left: 3px solid var(--cyan); border-radius: 0 8px 8px 0;
    background: var(--sky-light);
  }
  .story-quote p {
    font-size: 1.05rem; color: var(--cyan-deep); font-style: italic; line-height: 1.7; font-weight: 400;
  }
  .story-right p { color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.8; font-size: 0.95rem; }

  /* VALUES */
  .values-section { background: var(--navy); }
  .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 3rem; }
  .value-card {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(0,184,219,0.12);
    border-radius: 12px; padding: 2rem; transition: border-color 0.25s;
  }
  .value-card:hover { border-color: rgba(0,184,219,0.35); }
  .value-num {
    font-family: var(--font-display); font-size: 2.5rem; font-weight: 800;
    color: rgba(0,184,219,0.15); line-height: 1; margin-bottom: 1rem;
  }
  .value-card h3 { color: var(--white); margin-bottom: 0.6rem; font-size: 1rem; font-family: var(--font-display); font-weight: 700; }
  .value-card p { font-size: 0.875rem; color: rgba(255,255,255,0.5); line-height: 1.75; font-weight: 300; }

  /* PRESENCE */
  .presence-section { background: var(--ice); }
  .presence-inner { display: grid; grid-template-columns: 1.2fr 1fr; gap: 5rem; align-items: center; }
  .presence-map {
    background: var(--navy); border-radius: 16px; padding: 2.5rem;
    position: relative; overflow: hidden; min-height: 300px;
    display: flex; flex-direction: column; justify-content: flex-end;
    border: 1px solid rgba(0,184,219,0.1);
  }
  .presence-map-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(0,184,219,0.15) 1px, transparent 1px);
    background-size: 24px 24px;
  }
  .map-locations { position: relative; z-index: 2; display: flex; flex-wrap: wrap; gap: 0.75rem; }
  .map-loc {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.8rem; color: rgba(255,255,255,0.7);
    background: rgba(255,255,255,0.06); border: 1px solid rgba(0,184,219,0.2);
    padding: 0.4rem 0.85rem; border-radius: 100px;
  }
  .map-loc-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--cyan); flex-shrink: 0; }
  .presence-text p { color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.8; font-size: 0.95rem; }
  .presence-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 2rem; }
  .pstat {
    background: var(--white); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem;
  }
  .pstat-num {
    font-family: var(--font-display); font-size: 1.75rem; font-weight: 800;
    color: var(--cyan); letter-spacing: -0.03em;
  }
  .pstat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }

  /* TIMELINE */
  .timeline-section { background: var(--white); }
  .timeline { position: relative; margin-top: 3rem; padding-left: 2rem; }
  .timeline::before {
    content: ''; position: absolute; left: 0; top: 8px; bottom: 8px;
    width: 2px; background: var(--border);
  }
  .tl-item { position: relative; padding: 0 0 2.5rem 2.5rem; }
  .tl-item:last-child { padding-bottom: 0; }
  .tl-dot {
    position: absolute; left: -2rem; top: 4px;
    width: 12px; height: 12px; border-radius: 50%;
    background: var(--cyan); border: 2px solid var(--white);
    box-shadow: 0 0 0 2px var(--cyan);
    transform: translateX(calc(-50% + 1px));
  }
  .tl-year {
    font-family: var(--font-display); font-size: 0.75rem; font-weight: 700;
    color: var(--cyan); letter-spacing: 0.08em; margin-bottom: 0.4rem;
  }
  .tl-item h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.4rem; }
  .tl-item p { font-size: 0.9rem; color: var(--text-secondary); max-width: 560px; line-height: 1.75; }

  /* CTA */
  .about-cta {
    background: var(--cyan); padding: 4rem 6rem;
    display: flex; align-items: center; justify-content: space-between; gap: 2rem;
    flex-wrap: wrap;
  }
  .about-cta h2 { font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--white); margin-bottom: 0.5rem; letter-spacing: -0.02em; }
  .about-cta p { color: rgba(255,255,255,0.8); max-width: 480px; font-size: 0.95rem; }
  .btn-white-solid {
    background: var(--white); color: var(--cyan-deep);
    padding: 0.9rem 2rem; border-radius: 8px; flex-shrink: 0;
    font-family: var(--font-display); font-size: 0.95rem; font-weight: 700;
    text-decoration: none; transition: transform 0.15s; white-space: nowrap; display: inline-block;
  }
  .btn-white-solid:hover { transform: translateY(-2px); }

  @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
  .about-banner h1 { animation: fadeUp 0.6s 0.2s ease both; }
  .about-banner-sub { animation: fadeUp 0.6s 0.35s ease both; }

  @media (max-width: 1024px) {
    .about-banner-content { padding: 3rem 2rem; }
    .about-banner-accent { display: none; }
    .about-banner-pings { display: none; }
    .about-section { padding: 3.5rem 2rem; }
    .story-section { grid-template-columns: 1fr; gap: 2.5rem; }
    .story-left { position: static; }
    .values-grid { grid-template-columns: repeat(2,1fr); }
    .presence-inner { grid-template-columns: 1fr; gap: 2.5rem; }
    .about-cta { padding: 3rem 2rem; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 640px) {
    .values-grid { grid-template-columns: 1fr; }
    .presence-stats { grid-template-columns: 1fr 1fr; }
  }
</style>

<!-- BANNER -->
<x-page-banner
    tag="Our Story"
    title="Built From **Here.** Built For Here."
    subtitle="We are a Ghana-based technology consultancy that builds software for the real conditions of doing business across Africa, the Caribbean, and the diaspora — not the ideal conditions someone else imagined."
    height="lg"
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'About Us']]"
    :stats="[['value'=>'5+','label'=>'Years building'],['value'=>'10+','label'=>'Industries'],['value'=>'3','label'=>'Continents']]"
/>

<!-- STORY -->
<section class="about-section story-section">
  <div class="story-left">
    <p class="section-tag-sm">Who We Are</p>
    <h2 class="about-h2">A Different Kind of Tech Firm</h2>
    <div class="story-quote">
      <p>"We don't just acknowledge the realities of our clients — we build for them."</p>
    </div>
  </div>
  <div class="story-right">
    <p>Exchosoft Consult grew out of a frustration that many technology firms share but few admit: most software is built for conditions that don't exist in the markets that need it most. Reliable power. Constant internet. Expensive devices. Formal business structures.</p>
    <p>We started building differently. Offline-first architecture wasn't a feature we added — it was a fundamental decision made because our first hospital client needed patient records accessible during outages. LAN collaboration wasn't a buzzword — it was a solution for a pharmacy chain whose locations couldn't always reach the internet but needed to operate as one business.</p>
    <p>Over time, we built systems for churches managing hundreds of members and complex finances. For laboratories tracking samples under strict compliance. For heritage organizations preserving cultural history across continents. For financial institutions where reliability isn't optional.</p>
    <p>Each project taught us something different. Together, they gave us a breadth of experience that makes us useful to any business operating in our markets — not because we have a template for everything, but because we've genuinely solved hard problems across almost every sector.</p>
    <p>We are a small, experienced team. We don't outsource your project to junior developers. When you work with Exchosoft, you work with the people who built the systems you'll see in our portfolio.</p>
  </div>
</section>

<!-- VALUES -->
<section class="about-section values-section">
  <p class="section-tag-sm sky">What Drives Us</p>
  <h2 class="about-h2 light">Our Principles</h2>
  <div class="values-grid">
    @foreach([
      ['01','Reality over theory','We build for the world as it is — intermittent power, inconsistent connectivity, mobile-first users — not the world as it should be.'],
      ['02','Custom over compromise','Off-the-shelf forces your business into someone else\'s model. Every system we build starts from understanding how you actually work.'],
      ['03','Long-term over transactional','We are not project vendors. We build relationships because software that grows with a business requires a partner who understands it.'],
      ['04','Local knowledge, global standards','Understanding Accra, Lagos, Kingston, and London as markets means building technology that works across all of them without compromise.'],
      ['05','Reliability as a foundation','From hospital records to financial ledgers, we build systems where failure is not an option — architecturally, not just aspirationally.'],
      ['06','Clarity in every engagement','We tell clients what they need, not what they want to hear. Honest technology consulting produces better outcomes than comfortable ones.'],
    ] as [$num, $title, $text])
    <div class="value-card">
      <div class="value-num">{{ $num }}</div>
      <h3>{{ $title }}</h3>
      <p>{{ $text }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- PRESENCE -->
<section class="about-section presence-section">
  <div class="presence-inner">
    <div>
      <p class="section-tag-sm">Where We Work</p>
      <h2 class="about-h2">Accra-Based. Continent-Minded.</h2>
      <p class="presence-text">Our headquarters and core team are in Accra, Ghana. But our work spans West Africa, the United Kingdom, the Caribbean, and diaspora communities across North America and Europe.</p>
      <p class="presence-text">We understand the operational conditions, infrastructure realities, and business cultures of these markets because we operate within them — not from the outside looking in.</p>
      <div class="presence-stats">
        <div class="pstat"><div class="pstat-num">3</div><div class="pstat-label">Continents with active clients</div></div>
        <div class="pstat"><div class="pstat-num">10+</div><div class="pstat-label">Industries served</div></div>
        <div class="pstat"><div class="pstat-num">100%</div><div class="pstat-label">Custom-built solutions</div></div>
        <div class="pstat"><div class="pstat-num">0</div><div class="pstat-label">Off-the-shelf compromises</div></div>
      </div>
    </div>
    <div class="presence-map">
      <div class="presence-map-dots"></div>
      <div class="map-locations">
        @foreach(['Accra, Ghana','Lagos, Nigeria','London, UK','Kingston, Jamaica','Atlanta, USA','Kumasi, Ghana'] as $loc)
        <div class="map-loc"><span class="map-loc-dot"></span> {{ $loc }}</div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<!-- TIMELINE -->
<section class="about-section timeline-section">
  <p class="section-tag-sm">Our Journey</p>
  <h2 class="about-h2">How We Got Here</h2>
  <div class="timeline">
    @foreach([
      ['THE BEGINNING','First healthcare system built offline-first','Our first major project was a hospital management system for a client who couldn\'t afford downtime during outages. The offline-first architecture we developed became our signature approach.'],
      ['EXPANSION','Faith-based and service sectors','We extended our work into churches, pharmacies, and laundry businesses across Ghana — proving that the same architectural principles applied across very different operational contexts.'],
      ['CROSS-CONTINENTAL','Diaspora and heritage organizations','Partnerships with Black History Walks, African Odysseys, and the African Caribbean Summit brought our work to the UK and beyond — building platforms that serve diaspora communities globally.'],
      ['FINANCIAL SECTOR','Ghana Union Assurance and ACIS','Entering insurance and financial services required us to meet the highest standards of security, compliance, and reliability. We met them — and learned what institutional-grade software really means.'],
      ['TODAY','Full-spectrum tech consultancy','Exchosoft now offers the full range: custom software development, technology consulting, system architecture, digital transformation, and ongoing partnership across every sector we\'ve served.'],
    ] as [$year, $title, $desc])
    <div class="tl-item">
      <div class="tl-dot"></div>
      <div class="tl-year">{{ $year }}</div>
      <h3>{{ $title }}</h3>
      <p>{{ $desc }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- CTA -->
<div class="about-cta" id="contact">
  <div>
    <h2>Let's build something that actually works</h2>
    <p>Every business is different. Every challenge is unique. Let's talk about yours.</p>
  </div>
  <a class="btn-white-solid" href="{{ route('site.book-demo') }}" wire:navigate>Schedule a Consultation</a>
</div>

</div>
