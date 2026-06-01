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
            'consulting',
            'Consulting Services — Exchosoft Consult',
            'Exchosoft technology consulting — system audits, architecture advice, digital transformation, and honest technology guidance for African businesses.'
        );
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Consulting</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Tech Consulting</div>
    <h1>{{ $pageBannerHeading ?: 'Technology Consulting That Tells the Truth' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'We help businesses understand exactly what technology they need — and what they don't.' }}</p>
  </div>
</header>

<section class="site-section" style="background:var(--white);">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start;">
    <div class="reveal">
      <p class="section-tag-label">What We Offer</p>
      <h2 class="section-h2">Technology Consulting<br>That Tells the Truth</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);margin-top:1rem;line-height:1.85;">
        Most tech consultants tell clients what they want to hear. We tell clients what they need to know — even when that means recommending less technology, not more.
      </p>
      <p style="font-size:0.9rem;color:var(--text-secondary);margin-top:0.75rem;line-height:1.85;">
        Our consulting engagements start with a deep understanding of your business, your market, and the conditions you actually operate in — then we give you honest, practical direction.
      </p>
    </div>
    <div class="reveal" style="transition-delay:0.1s;">
      <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach([
          ["System Audit", "We review your existing technology and identify what is working, what isn't, and what you actually need."],
          ["Architecture Advice", "Before you build, we help you understand what to build, how it should be structured, and what the trade-offs are."],
          ["Digital Strategy", "We help leadership understand what role technology should play in your business — and how to prioritise investment."],
          ["Vendor Evaluation", "We help you evaluate technology vendors and products objectively — without hidden incentives."],
          ["Build vs. Buy Analysis", "Should you build custom, buy off-the-shelf, or configure an existing platform? We help you decide clearly."],
        ] as [$title, $desc])
        <div style="background:var(--ice);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
          <p style="font-family:var(--font-display);font-weight:700;font-size:0.9rem;color:var(--navy);margin-bottom:0.35rem;">{{ $title }}</p>
          <p style="font-size:0.83rem;color:var(--text-secondary);line-height:1.75;">{{ $desc }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
<div class="site-cta-strip">
  <div><h2>Schedule a consultation</h2><p>An honest conversation about your technology — no commitment required.</p></div>
  <a href="mailto:contact@exchosoft.com" class="btn-white-solid">Email Us to Book</a>
</div>
</div>
