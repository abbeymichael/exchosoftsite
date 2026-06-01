<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('terms-of-service');
        return view('pages.site.legal.terms-of-service', $this->seoViewData(
            'Terms of Service — Exchosoft Consult',
            'Read the Exchosoft Consult terms of service governing use of our website, software products, and consulting services.'
        ));
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Terms of Service</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Legal</div>
    <h1>{{ $pageBannerHeading ?: 'Terms of Service' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'The terms that govern your use of Exchosoft products and services.' }}</p>
  </div>
</header>
<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto;">
  <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;">Last updated: {{ $pageSeo?->updated_at?->format('d F Y') ?? date('d F Y') }}</p>
  
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Acceptance of Terms</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">By accessing or using any Exchosoft Consult service, website, or software product, you agree to be bound by these Terms of Service. If you do not agree, please do not use our services.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Use of Services</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">You agree to use our services only for lawful purposes and in accordance with these Terms. You must not use our services to harm others, violate any laws, or infringe on intellectual property rights.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Accounts</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. Notify us immediately of any unauthorised access.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Intellectual Property</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">All content, software, and materials provided through our services are the intellectual property of Exchosoft Consult or our licensors. You may not reproduce, distribute, or create derivative works without our express written permission.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Software Licensing</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Our software products are licensed, not sold. License terms are specified in individual product agreements. Unlicensed use, redistribution, or reverse engineering is prohibited.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Limitation of Liability</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">To the maximum extent permitted by law, Exchosoft Consult shall not be liable for any indirect, incidental, or consequential damages arising from your use of our services.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Changes to Terms</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We may update these Terms from time to time. We will notify you of material changes. Your continued use of our services after such changes constitutes acceptance of the updated Terms.</p>
    </div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <p style="font-size:0.85rem;color:var(--text-muted);">
      Questions about this policy? Email us at <a href="mailto:contact@exchosoft.com" style="color:var(--cyan);">contact@exchosoft.com</a>
    </p>
  </div>
</section>
</div>
