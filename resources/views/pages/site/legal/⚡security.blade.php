<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('security');
        return view('pages.site.legal.security', $this->seoViewData(
            'Security — Exchosoft Consult',
            'Read about Exchosoft's approach to security — how we protect client data, manage vulnerabilities, and maintain secure systems.'
        ));
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Security</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Legal</div>
    <h1>{{ $pageBannerHeading ?: 'Our Security Commitment' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'How we protect your data and keep our systems secure.' }}</p>
  </div>
</header>
<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto;">
  <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;">Last updated: {{ $pageSeo?->updated_at?->format('d F Y') ?? date('d F Y') }}</p>
  
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Security by Design</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Security is not a feature we bolt on — it is a fundamental design principle in every system we build. From the initial architecture to deployment, security considerations are integrated throughout.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Data Protection</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">All data transmitted between our systems and clients is encrypted in transit using industry-standard protocols. Sensitive data at rest is encrypted using appropriate algorithms. We follow the principle of least privilege in all system access controls.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Vulnerability Management</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We maintain a structured vulnerability management programme that includes regular security assessments, dependency scanning, and timely patching of identified vulnerabilities.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Incident Response</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We have documented incident response procedures to detect, contain, and recover from security incidents. In the event of a breach affecting client data, we will notify affected clients promptly in accordance with applicable regulations.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Responsible Disclosure</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">If you discover a security vulnerability in any Exchosoft system, please disclose it responsibly by emailing security@exchosoft.com. We will acknowledge your report and work to address it promptly.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Compliance</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Our security practices are designed to meet the requirements of applicable data protection regulations across our operating markets, including Ghana's Data Protection Act and GDPR for European clients.</p>
    </div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <p style="font-size:0.85rem;color:var(--text-muted);">
      Questions about this policy? Email us at <a href="mailto:contact@exchosoft.com" style="color:var(--cyan);">contact@exchosoft.com</a>
    </p>
  </div>
</section>
</div>
