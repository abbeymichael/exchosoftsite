<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function mount(): void
    {
        $this->loadPageSeo(
            'book-demo',
            'Book a Free Demo — Exchosoft Consult',
            'Book a free live demo of Exchosoft software. See WashOps, ChurchOps, or any of our platforms in action — tailored to your industry.'
        );
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Book a Demo</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Free Demo</div>
    <h1>{{ $pageBannerHeading ?: 'See Our Software in Action' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'Book a live demonstration and see how our platforms handle your specific industry's challenges.' }}</p>
  </div>
</header>

<section class="site-section" style="background:var(--white);">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start;">
    <div class="reveal">
      <p class="section-tag-label">What to Expect</p>
      <h2 class="section-h2">A Demo Tailored<br>to Your Industry</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);margin-top:1rem;line-height:1.85;">
        We don't run generic product tours. When you book a demo, we ask about your business first — then show you exactly how our software handles the challenges specific to your industry.
      </p>
      <div style="margin-top:1.75rem;display:flex;flex-direction:column;gap:0.75rem;">
        @foreach(["30-minute focused session","Tailored to your industry context","No sales pressure — just honest demonstration","Q&A on technical architecture if needed","Follow-up summary sent within 24 hours"] as $point)
        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
          <span style="width:6px;height:6px;border-radius:50%;background:var(--cyan);flex-shrink:0;margin-top:0.45rem;"></span>
          <span style="font-size:0.88rem;color:var(--text-secondary);line-height:1.6;">{{ $point }}</span>
        </div>
        @endforeach
      </div>
    </div>
    <div class="reveal" style="transition-delay:0.1s;">
      <div style="background:var(--ice);border:1px solid rgba(0,184,219,0.15);border-radius:16px;padding:2.5rem;">
        <p style="font-family:var(--font-display);font-weight:700;font-size:1rem;color:var(--navy);margin-bottom:1.5rem;">Book Your Demo</p>
        <p style="font-size:0.88rem;color:var(--text-secondary);margin-bottom:1.5rem;line-height:1.75;">
          Email us with your name, business type, and the software you want to see — and we'll arrange a time that works for you.
        </p>
        <a href="mailto:demo@exchosoft.com?subject=Demo Request"
           style="display:block;background:var(--cyan);color:var(--white);text-align:center;padding:0.9rem 2rem;border-radius:10px;font-family:var(--font-display);font-size:0.93rem;font-weight:600;text-decoration:none;transition:background 0.2s;">
          Request a Demo
        </a>
        <p style="font-size:0.78rem;color:var(--text-muted);text-align:center;margin-top:0.75rem;">We typically respond within 1 business day.</p>
      </div>
    </div>
  </div>
</section>
</div>
