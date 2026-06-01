<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('cookie-policy');
        return view('pages.site.legal.cookie-policy', $this->seoViewData(
            'Cookie Policy — Exchosoft Consult',
            'Read the Exchosoft cookie policy — what cookies we use, why we use them, and how you can manage your preferences.'
        ));
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Cookie Policy</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Legal</div>
    <h1>{{ $pageBannerHeading ?: 'Cookie Policy' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'How we use cookies and similar technologies on our website.' }}</p>
  </div>
</header>
<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto;">
  <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;">Last updated: {{ $pageSeo?->updated_at?->format('d F Y') ?? date('d F Y') }}</p>
  
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">What Are Cookies</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Cookies are small text files that are stored on your device when you visit a website. They help websites remember your preferences and understand how you use the site.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">How We Use Cookies</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We use cookies to operate our website effectively (essential cookies), remember your preferences, understand how visitors use our site (analytics), and improve the overall experience. We do not use cookies for advertising or tracking across third-party sites.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Types of Cookies We Use</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Essential cookies are required for the website to function. Session cookies are temporary and deleted when you close your browser. Persistent cookies remain for a set period to remember your preferences.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Managing Cookies</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">You can control cookie settings through your browser. Most browsers allow you to refuse or delete cookies. Note that disabling essential cookies may affect site functionality.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Updates to This Policy</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We may update this Cookie Policy as our practices change. The date at the top of this page reflects when the policy was last revised.</p>
    </div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <p style="font-size:0.85rem;color:var(--text-muted);">
      Questions about this policy? Email us at <a href="mailto:contact@exchosoft.com" style="color:var(--cyan);">contact@exchosoft.com</a>
    </p>
  </div>
</section>
</div>
