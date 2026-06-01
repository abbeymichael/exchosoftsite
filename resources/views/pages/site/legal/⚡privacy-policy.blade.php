<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public function render(): \Illuminate\View\View
    {
        $this->loadPageSeo('privacy-policy');
        return view('pages.site.legal.privacy-policy', $this->seoViewData(
            'Privacy Policy — Exchosoft Consult',
            'Read the Exchosoft Consult privacy policy. Learn how we collect, use, store, and protect your personal information.'
        ));
    }
}; ?>

<div>
<header class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-accent"></div>
  <div class="page-banner-content">
    <nav class="page-breadcrumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="current">Privacy Policy</span></nav>
    <div class="page-banner-tag"><span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);display:inline-block;"></span> Legal</div>
    <h1>{{ $pageBannerHeading ?: 'Privacy Policy' }}</h1>
    <p class="page-banner-sub">{{ $pageBannerSubheading ?: 'How we collect, use, and protect your personal information.' }}</p>
  </div>
</header>
<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto;">
  <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;">Last updated: {{ $pageSeo?->updated_at?->format('d F Y') ?? date('d F Y') }}</p>
  
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Overview</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">This Privacy Policy explains how Exchosoft Consult ("we", "our", "us") collects, uses, and protects personal information when you use our website, products, and services. We are committed to protecting your privacy and handling your data responsibly.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Information We Collect</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We collect information you provide directly to us (such as name, email address, and business details when you contact us or register for an account), information we collect automatically when you use our services (such as log data, device information, and usage patterns), and information from third-party services integrated with our platforms.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">How We Use Your Information</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We use the information we collect to provide, maintain, and improve our services; communicate with you about your account and our services; respond to enquiries and provide support; send relevant updates and information (with your consent); comply with legal obligations; and prevent fraud and ensure security.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Data Security</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction. Our systems are designed with security as a fundamental requirement, not an afterthought.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Data Retention</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">We retain personal information for as long as necessary to provide our services and fulfil the purposes outlined in this policy, unless a longer retention period is required by law.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Your Rights</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">Depending on your location, you may have rights to access, correct, delete, or port your personal data, and to object to or restrict certain processing. Contact us at contact@exchosoft.com to exercise these rights.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
      <h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--navy);margin-bottom:0.75rem;">Contact Us</h2>
      <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.85;">If you have questions about this Privacy Policy or our data practices, please contact us at contact@exchosoft.com.</p>
    </div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <p style="font-size:0.85rem;color:var(--text-muted);">
      Questions about this policy? Email us at <a href="mailto:contact@exchosoft.com" style="color:var(--cyan);">contact@exchosoft.com</a>
    </p>
  </div>
</section>
</div>
