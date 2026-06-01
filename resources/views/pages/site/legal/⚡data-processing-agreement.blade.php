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
            'data-processing-agreement',
            'Data Processing Agreement — Exchosoft Consult',
            'Read the Exchosoft Data Processing Agreement (DPA) — the terms under which we process personal data on behalf of clients and partners.'
        );
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Data Processing Agreement</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Legal</div>
    <h1>{{ $pageBannerHeading ?: 'Data Processing Agreement' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'The terms under which Exchosoft processes personal data on behalf of clients.' }}</p>
  </div>
</header>
<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto;">
  <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;">Last updated: {{ $pageSeo?->updated_at?->format('d F Y') ?? date('d F Y') }}</p>
  
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Purpose</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">This Data Processing Agreement ("DPA") governs the processing of personal data by Exchosoft Consult ("Processor") on behalf of clients ("Controller") in connection with the provision of our software and consulting services.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Scope of Processing</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Exchosoft processes personal data only to the extent necessary to provide the contracted services, in accordance with the Controller's documented instructions, and in compliance with applicable data protection laws.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Security Measures</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Exchosoft implements appropriate technical and organisational measures to ensure a level of security appropriate to the risk, including encryption of personal data, ongoing confidentiality, integrity, availability and resilience of systems, and regular security testing.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Sub-processors</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Exchosoft may engage sub-processors to assist in providing services. We will inform Controllers of any intended changes regarding the addition or replacement of sub-processors and provide opportunity to object.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Data Subject Rights</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Exchosoft will assist the Controller in responding to data subject requests and in fulfilling obligations under applicable data protection regulations.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Data Return and Deletion</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Upon termination of services, Exchosoft will return or delete all personal data as directed by the Controller, unless retention is required by law.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Contact</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">For questions about this DPA or to request a signed copy for your records, contact us at contact@exchosoft.com.</p>
    </div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <p style="font-size:0.85rem;color:var(--text-muted);">
      Questions about this policy? Email us at <a href="mailto:contact@exchosoft.com" style="color:var(--cyan);">contact@exchosoft.com</a>
    </p>
  </div>
</section>
</div>
