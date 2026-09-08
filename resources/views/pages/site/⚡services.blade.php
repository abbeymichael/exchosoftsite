<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Service;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    #[Computed]
    public function services()
    {
        return Service::all();
    }

    public function mount(): void
    {
        $this->loadPageSeo(
            'services',
            'Our Services — Exchosoft Consult',
            'Exchosoft offers custom software development, technology consulting, system architecture, digital transformation, and long-term tech partnership for African businesses.'
        );
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

<x-page-banner
    tag="What We Do"
    tagIcon="build"
    title='End-to-End <span style="color:#00b8db;">Technology</span><br>Solutions'
    subtitle="{{ $pageBannerSubheading ?: 'From custom software development to full technology consulting — everything built for the specific conditions of your market.' }}"
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Services']]"
    glowX="25%"
    glowX2="80%"
>
  <svg slot="ornament" class="absolute right-[7%] top-1/2 -translate-y-1/2 w-40 h-40 opacity-[.06] pointer-events-none" viewBox="0 0 160 160" fill="none">
    <rect x="1" y="1" width="158" height="158" rx="20" stroke="#00b8db" stroke-width="1"/><rect x="24" y="24" width="112" height="112" rx="12" stroke="#00b8db" stroke-width="1"/><rect x="48" y="48" width="64" height="64" rx="6" stroke="#00b8db" stroke-width="1"/>
    <line x1="1" y1="80" x2="159" y2="80" stroke="#00b8db" stroke-width=".5"/><line x1="80" y1="1" x2="80" y2="159" stroke="#00b8db" stroke-width=".5"/>
    <circle cx="80" cy="80" r="5" fill="#00b8db"/>
  </svg>
</x-page-banner>

<section class="site-section" style="background:var(--white);">
  <p class="section-tag-label">Our Capabilities</p>
  <h2 class="section-h2">Everything You Need.<br>Nothing You Don't.</h2>
  <div class="services-grid">
    @foreach($this->services as $service)
    <div class="service-card reveal">
      <div class="service-icon"><svg viewBox="0 0 24 24">{!! $service->icon !!}</svg></div>
      <h3>{{ $service->name }}</h3>
      <p>{{ $service->description }}</p>
      <span class="service-tag">{{ $service->tag }}</span>
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
