<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    use LoadsPageSeo;

    public string $name    = '';
    public string $email   = '';
    public string $subject = '';
    public string $message = '';
    public bool   $sent    = false;

    public function send(): void
    {
        $this->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:200',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:20|max:5000',
        ]);
        $this->sent = true;
        $this->reset(['name','email','subject','message']);
    }

    public function mount(): void
    {
        $this->loadPageSeo(
            'contact',
            'Contact Us — Exchosoft Consult',
            "Get in touch with Exchosoft Consult. Tell us what you need and we'll be honest about what we can build."
        );
    }
}; ?>

<div>
<style>
  .contact-hero { min-height: 420px; background: var(--navy); position: relative; overflow: hidden; display: flex; align-items: center; }
  .contact-layout { display: grid; grid-template-columns: 1fr 1.4fr; gap: 5rem; align-items: start; }
  .cinfo-item { display: flex; gap: 1rem; margin-bottom: 1.75rem; }
  .cinfo-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(0,184,219,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .cinfo-icon svg { width: 18px; height: 18px; stroke: var(--cyan); fill: none; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
  .cform-group { margin-bottom: 1.1rem; }
  .cform-group label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--navy); margin-bottom: 0.45rem; }
  .cform-group input, .cform-group textarea {
    width: 100%; border: 1.5px solid rgba(0,184,219,0.2); border-radius: 10px;
    padding: 0.72rem 1rem; font-size: 0.88rem; color: var(--navy); background: var(--white);
    transition: border-color 0.2s; outline: none; font-family: var(--font-body);
  }
  .cform-group input:focus, .cform-group textarea:focus { border-color: var(--cyan); }
  .cform-group textarea { resize: vertical; min-height: 130px; }
  .cform-error { font-size: 0.72rem; color: #ef4444; margin-top: 0.25rem; }
  @media (max-width: 1024px) { .contact-layout { grid-template-columns: 1fr; gap: 3rem; } }
</style>

<x-page-banner
    tag="Get In Touch"
    tagIcon="mail"
    title='Let&rsquo;s Build<br><span style="color:#00b8db;">Something Real</span>'
    subtitle="{{ $pageBannerSubheading ?: 'Tell us what you\'re working on. We\'ll tell you honestly whether we\'re the right team for it.' }}"
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Contact']]"
    glowX="60%"
    glowX2="18%"
>
  <svg slot="ornament" class="absolute right-[10%] top-1/2 -translate-y-1/2 w-36 h-36 opacity-[.05] pointer-events-none" viewBox="0 0 150 150" fill="none">
    <polygon points="75,4 144,40 144,110 75,146 6,110 6,40" stroke="#00b8db" stroke-width="1"/><polygon points="75,28 122,54 122,96 75,122 28,96 28,54" stroke="#00b8db" stroke-width="1"/><polygon points="75,52 100,68 100,82 75,98 50,82 50,68" stroke="#00b8db" stroke-width="1"/>
    <circle cx="75" cy="75" r="5" fill="#00b8db"/>
  </svg>
</x-page-banner>

<section class="site-section" style="background:var(--white);">
  <div class="contact-layout">
    <div class="reveal">
      <p class="section-tag-label">Reach Us</p>
      <h2 class="section-h2" style="font-size:1.7rem;">Accra-based.<br>Working globally.</h2>
      <p style="font-size:0.88rem;color:var(--text-secondary);margin:1rem 0 2rem;line-height:1.85;">
        We work with clients across Africa, the UK, the Caribbean, and diaspora communities worldwide. Drop us a message and we'll get back to you within 1–2 business days.
      </p>
      <div class="cinfo-item">
        <div class="cinfo-icon"><svg viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
        <div>
          <p style="font-family:var(--font-display);font-weight:700;font-size:0.85rem;color:var(--navy);">Email</p>
          <a href="mailto:contact@exchosoft.com" style="font-size:0.88rem;color:var(--cyan);text-decoration:none;">contact@exchosoft.com</a>
        </div>
      </div>
      <div class="cinfo-item">
        <div class="cinfo-icon"><svg viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div>
          <p style="font-family:var(--font-display);font-weight:700;font-size:0.85rem;color:var(--navy);">Headquarters</p>
          <p style="font-size:0.88rem;color:var(--text-secondary);">Accra, Ghana · West Africa</p>
        </div>
      </div>
    </div>
    <div class="reveal" style="transition-delay:0.12s;">
      @if($sent)
        <div style="background:var(--ice);border:1px solid rgba(0,184,219,0.2);border-radius:16px;padding:3rem;text-align:center;">
          <svg style="width:48px;height:48px;stroke:var(--cyan);fill:none;stroke-width:1.5;margin:0 auto 1rem;display:block;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h3 style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;color:var(--navy);margin-bottom:0.75rem;">Message Received</h3>
          <p style="font-size:0.9rem;color:var(--text-secondary);">Thank you for reaching out. We'll respond within 1–2 business days.</p>
        </div>
      @else
        <form wire:submit="send">
          <div class="cform-group"><label>Your Name *</label>
            <input wire:model="name" type="text" placeholder="e.g. Kwame Mensah">
            @error('name')<p class="cform-error">{{ $message }}</p>@enderror
          </div>
          <div class="cform-group"><label>Email Address *</label>
            <input wire:model="email" type="email" placeholder="you@company.com">
            @error('email')<p class="cform-error">{{ $message }}</p>@enderror
          </div>
          <div class="cform-group"><label>Subject *</label>
            <input wire:model="subject" type="text" placeholder="e.g. Custom Software Enquiry">
            @error('subject')<p class="cform-error">{{ $message }}</p>@enderror
          </div>
          <div class="cform-group"><label>Message *</label>
            <textarea wire:model="message" placeholder="Tell us about your business and what you're trying to solve…"></textarea>
            @error('message')<p class="cform-error">{{ $message }}</p>@enderror
          </div>
          <button type="submit" style="width:100%;background:var(--cyan);color:var(--white);border:none;border-radius:10px;padding:0.9rem;font-family:var(--font-display);font-size:0.95rem;font-weight:600;cursor:pointer;transition:background 0.2s;">
            <span wire:loading.remove wire:target="send">Send Message</span>
            <span wire:loading wire:target="send">Sending…</span>
          </button>
        </form>
      @endif
    </div>
  </div>
</section>
</div>
