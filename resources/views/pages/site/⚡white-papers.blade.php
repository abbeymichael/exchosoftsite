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
            'white-papers',
            'White Papers — Exchosoft Consult',
            'Download Exchosoft white papers on offline-first architecture, software development in Africa, and technology for emerging markets.'
        );
    }
}; ?>

<div>
<x-page-banner
    tag="Research"
    tagIcon="menu_book"
    title='Research &amp; <span style="color:#00b8db;">Technical Thinking</span>'
    subtitle="{{ $pageBannerSubheading ?: 'Our published thinking on technology, architecture, and building software for emerging markets.' }}"
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'White Papers']]"
    glowX="30%"
    glowX2="75%"
></x-page-banner>

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
