{{--
  site-footer.blade.php  — Livewire 4 component
  Full v5 footer: dot-matrix bg, brand col, 4-col grid, social icons, bottom bar
--}}

<footer class="relative overflow-hidden"
        style="background:linear-gradient(180deg,#0a1626 0%,#08121d 100%);border-top:1px solid rgba(0,184,219,.08);">

  {{-- dot-matrix background --}}
  <div class="absolute inset-0 pointer-events-none"
       style="background-image:radial-gradient(rgba(0,184,219,.06) 0.5px,transparent 0.5px);background-size:24px 24px;"></div>
  {{-- top glow line --}}
  <div class="absolute top-0 inset-x-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(0,184,219,.35) 30%,rgba(76,217,253,.5) 50%,rgba(0,184,219,.35) 70%,transparent);"></div>

  <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-12 lg:px-16">

    {{-- ── Upper grid ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.8fr_1fr_1fr_1fr] gap-10 pt-16 pb-12 border-b border-white/[.06]">

      {{-- Brand column --}}
      <div>
        <div class="flex items-center gap-2.5 mb-4">
          <span class="material-symbols-outlined text-secondary-container text-3xl" style="font-variation-settings:'FILL' 1;">hub</span>
          <span class="font-syne text-xl font-bold text-white tracking-tight">Exchosoft Consult</span>
        </div>
        <p class="text-[13px] text-white/38 leading-relaxed max-w-[280px] mb-6">
          We build custom software for Black businesses across Africa, the Caribbean, and the diaspora — designed for the conditions you actually operate in.
        </p>
        {{-- Social --}}
        <div class="flex items-center gap-3">
          {{-- Email --}}
          <a href="mailto:{{ $email }}" title="Email"
             class="w-9 h-9 rounded-[10px] flex items-center justify-center text-white/45 hover:text-secondary-fixed transition-all"
             style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);"
             onmouseover="this.style.background='rgba(0,184,219,.12)';this.style.borderColor='rgba(0,184,219,.28)'"
             onmouseout="this.style.background='rgba(255,255,255,.05)';this.style.borderColor='rgba(255,255,255,.09)'">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
          </a>
          {{-- LinkedIn --}}
          <a href="{{ $linkedIn }}" target="_blank" rel="noopener" title="LinkedIn"
             class="w-9 h-9 rounded-[10px] flex items-center justify-center text-white/45 hover:text-secondary-fixed transition-all"
             style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);"
             onmouseover="this.style.background='rgba(0,184,219,.12)';this.style.borderColor='rgba(0,184,219,.28)'"
             onmouseout="this.style.background='rgba(255,255,255,.05)';this.style.borderColor='rgba(255,255,255,.09)'">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
          {{-- Twitter/X --}}
          <a href="{{ $twitter }}" target="_blank" rel="noopener" title="X (Twitter)"
             class="w-9 h-9 rounded-[10px] flex items-center justify-center text-white/45 hover:text-secondary-fixed transition-all"
             style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);"
             onmouseover="this.style.background='rgba(0,184,219,.12)';this.style.borderColor='rgba(0,184,219,.28)'"
             onmouseout="this.style.background='rgba(255,255,255,.05)';this.style.borderColor='rgba(255,255,255,.09)'">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
        </div>
        {{-- Location badge --}}
        <div class="mt-6 inline-flex items-center gap-2 text-[11px] text-white/28">
          <span class="w-1.5 h-1.5 rounded-full bg-cyan flex-shrink-0"
                style="box-shadow:0 0 4px rgba(0,184,219,.7)"></span>
          Accra, Ghana &mdash; Africa · Caribbean · Diaspora
        </div>
      </div>

      {{-- Services column --}}
      <div>
        <h4 class="text-[11px] font-bold text-white/90 uppercase tracking-[.1em] mb-5">Services</h4>
        <ul class="space-y-2.5">
          <li><a href="{{ route('site.services') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">All Services</a></li>
          <li><a href="{{ route('site.services') }}#custom-dev" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Custom Development</a></li>
          <li><a href="{{ route('site.services') }}#consulting" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Tech Consulting</a></li>
          <li><a href="{{ route('site.services') }}#architecture" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">System Architecture</a></li>
          <li><a href="{{ route('site.consulting') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Book Consultation</a></li>
        </ul>
      </div>

      {{-- Resources column --}}
      <div>
        <h4 class="text-[11px] font-bold text-white/90 uppercase tracking-[.1em] mb-5">Resources</h4>
        <ul class="space-y-2.5">
          <li><a href="{{ route('site.blog') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Tech Blog</a></li>
          <li><a href="{{ route('site.white-papers') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">White Papers</a></li>
          <li><a href="{{ route('site.case-studies') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Case Studies</a></li>
          <li><a href="{{ route('site.portfolio') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Portfolio</a></li>
          <li><a href="{{ route('site.products') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Products</a></li>
        </ul>
      </div>

      {{-- Company column --}}
      <div>
        <h4 class="text-[11px] font-bold text-white/90 uppercase tracking-[.1em] mb-5">Company</h4>
        <ul class="space-y-2.5">
          <li><a href="{{ route('site.about') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">About Us</a></li>
          <li><a href="{{ route('site.contact') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Contact</a></li>
          <li><a href="{{ route('site.book-demo') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Book a Demo</a></li>
          <li><a href="{{ route('customer.register') }}" wire:navigate class="text-[13px] text-white/38 hover:text-cyan transition-colors">Create Account</a></li>
          <li><a href="mailto:{{ $email }}" class="text-[13px] text-white/38 hover:text-cyan transition-colors">{{ $email }}</a></li>
        </ul>
      </div>

    </div>

    {{-- ── Bottom bar ──────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 py-6">
      <p class="text-[12px] text-white/22">&copy; {{ $year }} Exchosoft Consult. All rights reserved.</p>

      <div class="flex items-center gap-5 flex-wrap">
        <a href="{{ route('site.privacy-policy') }}" wire:navigate
           class="text-[12px] text-white/22 hover:text-cyan transition-colors">Privacy</a>
        <a href="{{ route('site.terms-of-service') }}" wire:navigate
           class="text-[12px] text-white/22 hover:text-cyan transition-colors">Terms</a>
        <a href="{{ route('site.cookie-policy') }}" wire:navigate
           class="text-[12px] text-white/22 hover:text-cyan transition-colors">Cookies</a>
        <a href="{{ route('site.security') }}" wire:navigate
           class="text-[12px] text-white/22 hover:text-cyan transition-colors">Security</a>
      </div>
    </div>

  </div>
</footer>
