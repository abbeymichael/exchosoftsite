<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('white-papers');
        return view('pages.site.white-papers', $this->seoViewData(
            'White Papers — Exchosoft Consult',
            'Download Exchosoft white papers on offline-first architecture, software development in Africa, and technology for emerging markets.'
        ));
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">White Papers</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Research</div>
    <h1>{{ $pageBannerHeading ?: 'Research & Technical Thinking' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'Our published thinking on technology, architecture, and building software for emerging markets.' }}</p>
  </div>
</header>

<section class="site-section" style="background:var(--white);">
  <p class="section-tag-label">Research & Thinking</p>
  <h2 class="section-h2">Published Thinking on<br>Technology for Emerging Markets</h2>
  <p style="font-size:0.9rem;color:var(--text-secondary);margin:1rem 0 3rem;max-width:640px;line-height:1.85;">
    Our white papers share what we've learned building software across healthcare, finance, education, heritage, and more — in contexts where most technology assumptions don't hold.
  </p>
  <div style="background:var(--ice);border:1px solid var(--border);border-radius:16px;padding:3rem;text-align:center;">
    <svg style="width:48px;height:48px;stroke:var(--text-muted);fill:none;stroke-width:1.5;margin:0 auto 1rem;display:block;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p style="font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:0.5rem;">White Papers Coming Soon</p>
    <p style="font-size:0.88rem;color:var(--text-secondary);">We're preparing our first set of research papers. Subscribe to be notified when they're published.</p>
    <a href="mailto:contact@exchosoft.com?subject=White Paper Updates" style="display:inline-block;margin-top:1.5rem;background:var(--cyan);color:var(--white);padding:0.75rem 2rem;border-radius:8px;font-family:var(--font-display);font-size:0.88rem;font-weight:600;text-decoration:none;">Get Notified</a>
  </div>
</section>
</div>
