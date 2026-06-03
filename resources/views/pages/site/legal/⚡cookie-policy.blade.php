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
            'cookie-policy',
            'Cookie Policy — Exchosoft Consult',
            'Read the Exchosoft cookie policy — what cookies we use, why we use them, and how you can manage your preferences.'
        );
    }
}; ?>

<div>


<x-page-banner
    tag="Cookie Policy"
    tagIcon="info"
    title='Cookie Policy'
    subtitle="Read the Exchosoft cookie policy — what cookies we use, why we use them, and how you can manage your preferences."
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Cookie Policy','route'=>'site.cookie-policy']]"
    glowX="72%"
    glowX2="12%"
>
  <svg slot="ornament" class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none" viewBox="0 0 180 180" fill="none">
    <circle cx="90" cy="90" r="88" stroke="#00b8db" stroke-width="1"/><circle cx="90" cy="90" r="60" stroke="#00b8db" stroke-width="1"/><circle cx="90" cy="90" r="32" stroke="#00b8db" stroke-width="1"/>
    <line x1="2" y1="90" x2="178" y2="90" stroke="#00b8db" stroke-width=".5"/><line x1="90" y1="2" x2="90" y2="178" stroke="#00b8db" stroke-width=".5"/>
    <circle cx="90" cy="90" r="4" fill="#00b8db"/>
  </svg>
</x-page-banner>

<section class="site-section" style="background:var(--white);max-width:800px;margin:0 auto; mt-12 mb-24; padding:2rem;">
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
