<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('services');
        return view('pages.site.services', $this->seoViewData(
            'Our Services — Exchosoft Consult',
            'Exchosoft offers custom software development, technology consulting, system architecture, digital transformation, and long-term tech partnership for African businesses.'
        ));
    }
}; ?>

<div>
<style>
  .services-hero { min-height: 460px; background: var(--navy); position: relative; overflow: hidden; display: flex; align-items: center; }
  .services-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-top: 3rem; }
  .service-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 2rem;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
  }
  .service-card:hover { border-color: var(--cyan); box-shadow: 0 12px 40px rgba(0,184,219,0.1); transform: translateY(-2px); }
  .service-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(0,184,219,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
  .service-icon svg { width: 24px; height: 24px; stroke: var(--cyan); fill: none; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
  .service-card h3 { font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: var(--navy); margin-bottom: 0.75rem; }
  .service-card p { font-size: 0.87rem; color: var(--text-secondary); line-height: 1.8; }
  .service-tag { display: inline-block; margin-top: 1rem; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--cyan); }
  @media (max-width: 1024px) { .services-grid { grid-template-columns: 1fr 1fr; } }
  @media (max-width: 640px) { .services-grid { grid-template-columns: 1fr; } }
</style>

<header class="services-hero">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="sep">/</span>
      <span class="current">Services</span>
    </nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> What We Do</div>
    <h1>{{ $pageBannerHeading ?: 'Services Built Around Your Reality' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'From custom software development to full technology consulting — everything built for the specific conditions of your market.' }}</p>
  </div>
</header>

<section class="site-section" style="background:var(--white);">
  <p class="section-tag-label">Our Capabilities</p>
  <h2 class="section-h2">Everything You Need.<br>Nothing You Don't.</h2>
  <div class="services-grid">
    @foreach([
      ['Custom Software Development','We build systems from the ground up — designed around how your business actually works, not how an off-the-shelf product assumes it works.','<path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>','Development'],
      ['Technology Consulting','We help organisations understand what technology they actually need — and what they don\'t. Honest advice, clear direction.','<path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>','Consulting'],
      ['System Architecture & Design','We design systems that are built to last — scalable, maintainable, and resilient under the actual conditions of African markets.','<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/>','Architecture'],
      ['Digital Transformation','We help businesses transition from manual or legacy processes to modern, efficient digital systems — with minimal disruption and maximum adoption.','<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>','Transformation'],
      ['Offline-First Engineering','We specialise in systems that function fully without internet access — syncing when connectivity is available, never losing data when it isn\'t.','<path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>','Specialist'],
      ['Long-Term Tech Partnership','We stay involved after launch — providing ongoing support, feature development, and strategic guidance as your business grows.','<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>','Partnership'],
    ] as [$title, $desc, $icon, $tag])
    <div class="service-card reveal">
      <div class="service-icon"><svg viewBox="0 0 24 24">{!! $icon !!}</svg></div>
      <h3>{{ $title }}</h3>
      <p>{{ $desc }}</p>
      <span class="service-tag">{{ $tag }}</span>
    </div>
    @endforeach
  </div>
</section>

<section class="site-section" style="background:var(--navy);">
  <p class="section-tag-label sky">Industries We Serve</p>
  <h2 class="section-h2 light">Built Across<br>Every Sector</h2>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:3rem;">
    @foreach(['Healthcare & Medical','Faith-Based Organizations','Laundry & Service Industries','Heritage & Cultural','Financial Services','Retail & Distribution','Education & Training','Government & NGOs','Hospitality & Events'] as $industry)
    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(0,184,219,0.12);border-radius:10px;padding:1.25rem;display:flex;align-items:center;gap:0.75rem;">
      <span style="width:6px;height:6px;border-radius:50%;background:var(--cyan);flex-shrink:0;"></span>
      <span style="font-size:0.88rem;color:rgba(255,255,255,0.7);font-family:var(--font-display);font-weight:500;">{{ $industry }}</span>
    </div>
    @endforeach
  </div>
</section>

<div class="site-cta-strip">
  <div>
    <h2>Ready to start a project?</h2>
    <p>Tell us about your business and what you're trying to solve. We'll tell you honestly what's possible.</p>
  </div>
  <a href="{{ route('site.consulting') }}" wire:navigate class="btn-white-solid">Schedule a Consultation</a>
</div>
</div>
