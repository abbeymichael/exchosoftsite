<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Exchosoft Consult — Software Development & Technology Consultancy' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Exchosoft Consult is a Ghana-based technology consultancy and software development company serving Black businesses across Africa, the Caribbean, and the diaspora.' }}">

    @if(!empty($metaKeywords))
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    @if(!empty($canonicalUrl))
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    {{-- Open Graph --}}
    @if(!empty($ogTitle))
    <meta property="og:title" content="{{ $ogTitle }}">
    @endif
    @if(!empty($ogDescription))
    <meta property="og:description" content="{{ $ogDescription }}">
    @endif
    @if(!empty($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $twitterCard ?? 'summary_large_image' }}">
    @if(!empty($twitterTitle))
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    @elseif(!empty($ogTitle))
    <meta name="twitter:title" content="{{ $ogTitle }}">
    @endif
    @if(!empty($twitterDescription))
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    @elseif(!empty($ogDescription))
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @endif
    @if(!empty($twitterImage))
    <meta name="twitter:image" content="{{ $twitterImage }}">
    @elseif(!empty($ogImage))
    <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    {{-- Schema.org JSON-LD --}}
    @if(!empty($schemaMarkup))
    <script type="application/ld+json">{!! json_encode($schemaMarkup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    @endif

    {{-- Favicon --}}
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Vite assets (Tailwind CSS + app JS with Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* ──────────────────────────────────────────────────────────────────
           SITE-WIDE TOKEN VARIABLES  (v5 palette, matches home_page_v5.html)
        ─────────────────────────────────────────────────────────────────── */
        :root {
            --cyan:           #00b8db;
            --cyan-dark:      #0091ad;
            --cyan-deep:      #006d82;
            --sky:            #7acfe8;
            --sky-light:      #e5f7fb;
            --navy:           #0d2137;
            --navy-mid:       #162d47;
            --navy-deepest:   #08121d;
            --ice:            #f4f8fb;
            --white:          #ffffff;
            --text-primary:   #0d2137;
            --text-secondary: #3a5a72;
            --text-muted:     #7a9ab0;
            --border:         rgba(0,184,219,0.15);
            --font-display:   'Syne', sans-serif;
            --font-body:      'DM Sans', sans-serif;

            /* Tailwind-named tokens (matches tailwind.config in home_page_v5.html) */
            --surface-tint:              #4d6079;
            --secondary-fixed:           #b1ecff;
            --primary:                   #000917;
            --on-primary-container:      #7689a4;
            --surface-container-high:    #e5e9ec;
            --on-surface:                #171c1f;
            --secondary:                 #00677c;
            --outline-variant:           #c4c6cd;
            --surface-container-lowest:  #ffffff;
            --inverse-on-surface:        #edf1f4;
            --secondary-container:       #4cd9fd;
            --inverse-primary:           #b5c8e5;
            --surface:                   #f6fafd;
            --primary-container:         #0d2137;
            --on-secondary:              #ffffff;
            --primary-fixed-dim:         #b5c8e5;
            --on-tertiary-container:     #3792aa;
            --tertiary:                  #000a0e;
            --surface-bright:            #f6fafd;
            --on-surface-variant:        #44474d;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            color: var(--text-primary);
            background: #f6fafd;
            overflow-x: hidden;
            line-height: 1.7;
            margin: 0; padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── MATERIAL SYMBOLS ── */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block; line-height: 1;
            vertical-align: middle;
        }

        /* ── MEGA MENU SAFE-HOVER BRIDGE ── */
        .mega-trigger::after {
            content: ''; position: absolute; bottom: -16px; left: -20px; right: -20px; height: 20px;
        }

        /* ── ANIMATION DELAYS ── */
        .d1{animation-delay:.05s} .d2{animation-delay:.14s} .d3{animation-delay:.23s}
        .d4{animation-delay:.33s} .d5{animation-delay:.42s}

        /* ── PAGE BANNER (inner pages) ── */
        .banner-glow::after {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 55% 80% at var(--gx,70%) 50%, rgba(0,184,219,.11) 0%, transparent 70%),
                radial-gradient(ellipse 28% 45% at var(--gx2,15%) 85%, rgba(122,207,232,.05) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── REVEAL ANIMATION ── */
        .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1);
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ── DOT GRID ── */
        .dot-grid {
            background-image: radial-gradient(circle, rgba(0,184,219,0.2) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* ── TEXT GRADIENT ── */
        .text-gradient {
            background: linear-gradient(135deg, #00b8db 0%, #7acfe8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── GLASS CARD ── */
        .glass-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(16px);
            border: 1.5px solid rgba(177,236,255,0.3);
        }

        /* ── SHARED SECTION UTILITIES ── */
        .site-section    { padding: 5rem 6rem; }
        .site-section-sm { padding: 3.5rem 6rem; }
        .section-tag-label {
            font-size: .72rem; font-weight: 700; letter-spacing: .1em;
            color: var(--cyan); text-transform: uppercase; margin-bottom: .75rem;
        }
        .section-h2 {
            font-family: var(--font-display);
            font-size: clamp(1.75rem, 2.8vw, 2.5rem);
            font-weight: 800; letter-spacing: -.03em; color: var(--navy); line-height: 1.15;
            margin-bottom: 1rem;
        }
        .section-h2.light { color: var(--white); }

        /* ── SHARED CTA STRIP ── */
        .site-cta-strip {
            background: var(--cyan); padding: 4rem 6rem;
            display: flex; align-items: center; justify-content: space-between;
            gap: 2rem; flex-wrap: wrap;
        }
        .site-cta-strip h2 {
            font-family: var(--font-display); font-size: clamp(1.5rem, 2.5vw, 2rem);
            font-weight: 800; color: var(--white); margin-bottom: .4rem;
            letter-spacing: -.02em;
        }
        .site-cta-strip p { color: rgba(255,255,255,.78); max-width: 460px; font-size: .93rem; }
        .btn-white-solid {
            background: var(--white); color: var(--cyan-deep);
            padding: .9rem 2rem; border-radius: 8px; flex-shrink: 0;
            font-family: var(--font-display); font-size: .93rem; font-weight: 700;
            text-decoration: none; transition: transform .15s; white-space: nowrap; display: inline-block;
        }
        .btn-white-solid:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.15); }

        /* ── VALUE CARD (about page) ── */
        .value-card {
            clip-path: inset(0 0 100% 0);
            transition: clip-path .6s cubic-bezier(.22,1,.36,1), border-color .25s, box-shadow .25s, transform .25s;
        }
        .value-card.visible { clip-path: inset(0 0 0% 0); }
        .value-card:hover {
            border-color: rgba(0,184,219,0.5) !important;
            box-shadow: 0 0 40px rgba(0,184,219,.1), inset 0 0 20px rgba(0,184,219,.04);
            transform: translateY(-2px);
        }

        /* ── TIMELINE ── */
        .tl-track::before {
            content: '';
            position: absolute; left: 0; top: 0;
            width: 1px; height: 0;
            background: linear-gradient(to bottom, rgba(0,184,219,.6) 0%, rgba(0,184,219,.1) 100%);
            transition: height 1.8s cubic-bezier(.22,1,.36,1);
        }
        .tl-track.drawn::before { height: 100%; }

        /* ── SCANLINES ── */
        .scanlines::after {
            content: ''; position: absolute; inset: 0; z-index: 1;
            background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(0,0,0,.03) 3px, rgba(0,0,0,.03) 4px);
            pointer-events: none;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1280px) {
            .site-section { padding: 4rem 3rem; }
            .site-section-sm { padding: 2.5rem 3rem; }
            .site-cta-strip { padding: 3rem; }
        }
        @media (max-width: 1024px) {
            .site-section { padding: 3.5rem 2rem; }
            .site-section-sm { padding: 2rem; }
            .site-cta-strip { padding: 2.5rem 2rem; flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 640px) {
            .site-section { padding: 3rem 1.25rem; }
            .site-section-sm { padding: 1.75rem 1.25rem; }
        }
    </style>
</head>

<body class="bg-surface font-dm text-on-surface antialiased min-h-screen flex flex-col">

{{-- ─── NAVIGATION (Livewire) ───────────────────────────────────── --}}
<livewire:site.site-navigation />

{{-- ─── PAGE CONTENT ──────────────────────────────────────────────── --}}
<main class="flex-grow">
    {{ $slot }}
</main>

{{-- ─── FOOTER (Livewire) ─────────────────────────────────────────── --}}
<livewire:site.site-footer />


<!-- Cookie Banner -->
<div class="fixed bottom-6 left-6 right-6 md:left-auto md:right-8 md:max-w-md z-[100] transform transition-all duration-500" id="cookie-banner">
  <div class="border border-secondary-container/30 p-6 rounded-xl shadow-2xl backdrop-blur-xl" style="background:rgba(0,9,23,.95);">
    <div class="flex items-start gap-4">
      <div class="w-10 h-10 rounded-full bg-secondary-container/20 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-secondary-container">cookie</span></div>
      <div>
        <p class="text-sm text-white mb-4 leading-relaxed">We use essential cookies to ensure our high-fidelity platforms function as intended. View our <a class="text-secondary-fixed underline" href="#">Cookie Policy</a> for more info.</p>
        <div class="flex gap-3">
          <button class="flex-grow bg-secondary-container hover:bg-secondary-fixed-dim text-primary font-bold py-2 px-4 rounded-lg text-xs transition-colors" onclick="dismissCookieBanner()">Accept All</button>
          <button class="px-4 py-2 rounded-lg text-xs font-bold text-white hover:bg-white/5 transition-colors" onclick="dismissCookieBanner()">Preferences</button>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ─── REVEAL SCROLL OBSERVER ────────────────────────────────────── --}}
<script>
(function() {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                if (e.target.classList.contains('tl-track'))  e.target.classList.add('drawn');
                if (e.target.classList.contains('value-card')) e.target.classList.add('visible');
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal, .value-card, .tl-track').forEach(el => obs.observe(el));
})();
</script>

<script>
  (function () {
    const PERIOD_MS = 4000, HIT_DEG = 22, LIT_MS = 900;
    const wrap = document.getElementById('orbitWrap');
    const icons = Array.from(document.querySelectorAll('.orbit-icon'));
    const litUntil = new Array(icons.length).fill(0);
    const angles = icons.map(ic => parseFloat(ic.dataset.angle) || 0);
    function layout() {
      if (!wrap) return;
      const R = wrap.offsetWidth * 0.42;
      icons.forEach((ic, i) => {
        const rad = (angles[i] - 90) * Math.PI / 180;
        ic.style.transform = `translate(${R * Math.cos(rad)}px,${R * Math.sin(rad)}px)`;
      });
    }
    layout();
    window.addEventListener('resize', layout);
    function tick(ts) {
      const beamDeg = ((ts % PERIOD_MS) / PERIOD_MS) * 360;
      angles.forEach((iconDeg, i) => {
        let diff = Math.abs(beamDeg - iconDeg) % 360;
        if (diff > 180) diff = 360 - diff;
        if (diff < HIT_DEG) litUntil[i] = ts + LIT_MS;
        icons[i].classList.toggle('lit', ts < litUntil[i]);
      });
      requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  })();
</script>

<script>
  function closeNotificationBar() {
    const bar = document.getElementById('notification-bar');
    bar.style.maxHeight = bar.offsetHeight + 'px';
    bar.style.overflow = 'hidden';
    requestAnimationFrame(() => {
      bar.style.transition = 'max-height .35s ease, opacity .25s ease';
      bar.style.maxHeight = '0';
      bar.style.opacity = '0';
    });
    setTimeout(() => bar.remove(), 380);
  }

  (function () {
    let hoverTimers = {};
    function openMenu(item) {
      document.querySelectorAll('.mega-menu-item').forEach(el => { if (el !== item) closeMenu(el, true); });
      const menu = item.querySelector('.mega-menu-content');
      if (!menu) return;
      if (hoverTimers[item.dataset.menuId]) clearTimeout(hoverTimers[item.dataset.menuId]);
      menu.style.display = 'grid';
      requestAnimationFrame(() => requestAnimationFrame(() => menu.classList.add('is-visible')));
      item.dataset.open = 'true';
      const chevron = item.querySelector('.mega-trigger .material-symbols-outlined');
      if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
    function closeMenu(item, immediate) {
      const menu = item.querySelector('.mega-menu-content');
      if (!menu) return;
      if (hoverTimers[item.dataset.menuId]) clearTimeout(hoverTimers[item.dataset.menuId]);
      hoverTimers[item.dataset.menuId] = setTimeout(() => {
        menu.classList.remove('is-visible');
        setTimeout(() => { if (!menu.classList.contains('is-visible')) menu.style.display = 'none'; }, 260);
        item.dataset.open = 'false';
        const chevron = item.querySelector('.mega-trigger .material-symbols-outlined');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
      }, immediate ? 0 : 120);
    }
    function toggleMenu(item) { item.dataset.open === 'true' ? closeMenu(item, true) : openMenu(item); }
    document.querySelectorAll('.mega-menu-item').forEach((item, i) => {
      item.dataset.menuId = 'menu-' + i;
      item.dataset.open = 'false';
      const trigger = item.querySelector('.mega-trigger');
      if (!trigger) return;
      trigger.querySelector('.material-symbols-outlined').style.transition = 'transform .25s ease';
      trigger.addEventListener('click', e => { e.stopPropagation(); toggleMenu(item); });
      item.addEventListener('mouseenter', () => { if (hoverTimers[item.dataset.menuId]) clearTimeout(hoverTimers[item.dataset.menuId]); openMenu(item); });
      item.addEventListener('mouseleave', () => closeMenu(item, false));
    });
    document.addEventListener('click', () => document.querySelectorAll('.mega-menu-item').forEach(item => closeMenu(item, true)));
  })();

  function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-overlay');
    const icon = document.getElementById('menu-icon');
    const isClosed = menu.classList.contains('translate-x-full');
    menu.classList.toggle('translate-x-full', !isClosed);
    overlay.classList.toggle('hidden', !isClosed);
    icon.textContent = isClosed ? 'close' : 'menu';
    document.body.style.overflow = isClosed ? 'hidden' : 'auto';
  }

  function toggleAccordion(id) {
    const acc = document.getElementById(id);
    const isHidden = acc.classList.toggle('hidden');
    const chevron = document.getElementById(id.replace('-acc', '-chevron'));
    if (chevron) chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
  }

  function dismissCookieBanner() {
    const banner = document.getElementById('cookie-banner');
    banner.style.opacity = '0';
    banner.style.transform = 'translateY(20px)';
    setTimeout(() => banner.remove(), 500);
  }

  window.addEventListener('scroll', () => {
    const header = document.getElementById('main-header');
    if (!header) return;
    if (window.scrollY > 20) {
      header.classList.add('py-1'); header.classList.remove('py-4');
      header.style.backgroundColor = 'rgba(0,9,23,.98)';
    } else {
      header.classList.add('py-4'); header.classList.remove('py-1');
      header.style.backgroundColor = 'rgba(0,9,23,.95)';
    }
  });
</script>


@livewireScripts
</body>
</html>
