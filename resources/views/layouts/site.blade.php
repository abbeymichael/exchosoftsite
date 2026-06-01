<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
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
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --cyan: #00b8db;
            --cyan-dark: #0091ad;
            --cyan-deep: #006d82;
            --sky: #7acfe8;
            --sky-light: #e5f7fb;
            --navy: #0d2137;
            --navy-mid: #162d47;
            --ice: #f4f8fb;
            --white: #ffffff;
            --text-primary: #0d2137;
            --text-secondary: #3a5a72;
            --text-muted: #7a9ab0;
            --border: rgba(0,184,219,0.15);
            --font-display: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            color: var(--text-primary);
            background: var(--white);
            overflow-x: hidden;
            line-height: 1.7;
            margin: 0; padding: 0;
        }

        /* ── MATERIAL SYMBOLS ── */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* ── SITE NAV ── */
        .site-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 0.9rem 3rem;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(13,33,55,0.98);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,184,219,0.12);
            transition: padding 0.3s, background 0.3s;
        }
        .site-nav-logo {
            display: flex; align-items: center; gap: 0.65rem;
            font-family: var(--font-display); font-size: 0.95rem; font-weight: 700;
            color: var(--white); text-decoration: none; letter-spacing: -0.01em;
            flex-shrink: 0;
        }
        .site-nav-logo img { height: 60px; width: auto; }
        .site-nav-logo .logo-fallback {
            height: 30px; width: 75px; border-radius: 7px;
            background: var(--cyan); display: flex; align-items: center;
            justify-content: center; font-size: 0.85rem; font-weight: 800; color: var(--white);
        }
        .site-nav-links { display: flex; align-items: center; gap: 0.15rem; }
        .site-nav-links a {
            font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.6);
            text-decoration: none; letter-spacing: 0.01em;
            padding: 0.4rem 0.8rem; border-radius: 6px;
            transition: color 0.2s, background 0.2s;
            white-space: nowrap;
        }
        .site-nav-links a:hover { color: var(--white); background: rgba(255,255,255,0.08); }
        .site-nav-links a.active { color: var(--cyan); background: rgba(0,184,219,0.08); }
        .site-nav-right { display: flex; align-items: center; gap: 0.65rem; flex-shrink: 0; }
        .nav-btn-ghost {
            background: transparent; color: rgba(255,255,255,0.65);
            padding: 0.4rem 0.9rem; border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.15);
            font-family: var(--font-display); font-size: 0.78rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s; white-space: nowrap;
        }
        .nav-btn-ghost:hover { color: var(--white); border-color: rgba(255,255,255,0.3); }
        .nav-btn-cta {
            background: var(--cyan); color: var(--white);
            padding: 0.45rem 1.1rem; border-radius: 6px;
            font-family: var(--font-display); font-size: 0.78rem; font-weight: 600;
            text-decoration: none; transition: background 0.2s; white-space: nowrap;
        }
        .nav-btn-cta:hover { background: var(--cyan-dark); }
        .nav-user-menu { position: relative; }
        .nav-user-btn {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
            padding: 0.3rem 0.75rem 0.3rem 0.4rem; border-radius: 6px; cursor: pointer;
            font-size: 0.78rem; color: rgba(255,255,255,0.75); font-family: var(--font-display); font-weight: 500;
            transition: background 0.2s;
        }
        .nav-user-btn:hover { background: rgba(255,255,255,0.12); }
        .nav-user-avatar {
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--cyan); display: flex; align-items: center; justify-content: center;
            font-size: 0.6rem; font-weight: 800; color: var(--white);
        }
        .nav-user-dropdown {
            position: absolute; right: 0; top: calc(100% + 0.5rem);
            background: var(--white); border: 1px solid var(--border);
            border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            min-width: 180px; overflow: hidden; z-index: 200;
        }
        .nav-user-dropdown a, .nav-user-dropdown button {
            display: block; width: 100%; text-align: left;
            padding: 0.6rem 1rem; font-size: 0.8rem; color: var(--text-secondary);
            text-decoration: none; background: none; border: none; cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .nav-user-dropdown a:hover, .nav-user-dropdown button:hover { background: var(--ice); color: var(--navy); }
        .nav-user-dropdown .divider { height: 1px; background: var(--border); margin: 0.25rem 0; }
        .nav-user-dropdown .danger { color: #dc2626; }
        .nav-user-dropdown .danger:hover { background: #fef2f2; color: #dc2626; }

        /* Mobile toggle */
        .mobile-toggle {
            display: none; background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.7); padding: 0.4rem;
        }
        .mobile-menu {
            display: none; position: absolute; top: 100%; left: 0; right: 0;
            background: var(--navy); border-top: 1px solid rgba(0,184,219,0.1);
            padding: 1rem 1.5rem 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .mobile-menu.open { display: block; }
        .mobile-menu a {
            display: block; padding: 0.65rem 0; font-size: 0.88rem; font-weight: 500;
            color: rgba(255,255,255,0.7); text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: color 0.2s;
        }
        .mobile-menu a:hover { color: var(--cyan); }
        .mobile-menu .mobile-actions { margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap; }

        /* ── SITE FOOTER ── */
        .site-footer {
            background: var(--navy); color: var(--white);
            padding: 4rem 6rem 2rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            margin-top: 0;
        }
        .site-footer-grid {
            display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 3rem;
            margin-bottom: 3rem;
        }
        .footer-brand p {
            font-size: 0.83rem; color: rgba(255,255,255,0.42); line-height: 1.8;
            margin-top: 0.75rem; max-width: 280px;
        }
        .footer-brand-name {
            font-family: var(--font-display); font-weight: 700; font-size: 1rem;
            color: var(--white); letter-spacing: -0.01em;
        }
        .footer-brand-tag { font-size: 0.72rem; color: rgba(255,255,255,0.35); margin-top: 0.2rem; }
        .footer-social { display: flex; gap: 0.65rem; margin-top: 1.25rem; }
        .footer-social a {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s, border-color 0.2s;
        }
        .footer-social a:hover { background: rgba(0,184,219,0.15); border-color: rgba(0,184,219,0.3); }
        .footer-social svg { width: 14px; height: 14px; fill: rgba(255,255,255,0.5); }
        .footer-col h4 {
            font-family: var(--font-display); font-size: 0.75rem; font-weight: 700;
            color: var(--white); letter-spacing: 0.06em; text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .footer-col ul { list-style: none; margin: 0; padding: 0; }
        .footer-col ul li { margin-bottom: 0.55rem; }
        .footer-col ul li a {
            font-size: 0.82rem; color: rgba(255,255,255,0.42);
            text-decoration: none; transition: color 0.2s;
        }
        .footer-col ul li a:hover { color: var(--cyan); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap;
        }
        .footer-bottom p { font-size: 0.75rem; color: rgba(255,255,255,0.28); margin: 0; }
        .footer-location {
            font-size: 0.75rem; color: rgba(255,255,255,0.28);
            display: flex; align-items: center; gap: 0.4rem;
        }
        .footer-location-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--cyan); flex-shrink: 0; }

        /* ── SHARED UTILITY CLASSES ── */
        .site-section { padding: 5rem 6rem; }
        .site-section-sm { padding: 3.5rem 6rem; }
        .section-tag-label {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
            color: var(--cyan); text-transform: uppercase; margin-bottom: 0.75rem;
        }
        .section-tag-label.sky { color: var(--sky); }
        .section-h2 {
            font-family: var(--font-display); font-size: clamp(1.75rem, 2.8vw, 2.5rem);
            font-weight: 800; letter-spacing: -0.03em; color: var(--navy); line-height: 1.15;
            margin-bottom: 1rem;
        }
        .section-h2.light { color: var(--white); }

        /* ── PAGE BANNER (shared across interior pages) ── */
        .page-banner {
            min-height: 420px; background: var(--navy);
            position: relative; overflow: hidden;
            display: flex; align-items: center;
        }
        .page-banner-dots {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(0,184,219,0.18) 1px, transparent 1px);
            background-size: 30px 30px; pointer-events: none;
        }
        .page-banner-glow {
            position: absolute; inset: 0;
            background:
                radial-gradient(circle at 72% 45%, rgba(0,184,219,0.12) 0%, transparent 55%),
                radial-gradient(circle at 20% 80%, rgba(122,207,232,0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        .page-banner-accent {
            position: absolute; left: 5.5rem; top: 0; bottom: 0;
            width: 2px; background: var(--cyan); opacity: 0.3;
        }
        .page-banner-content {
            position: relative; z-index: 2;
            padding: 4.5rem 6rem; max-width: 740px;
        }
        .page-breadcrumb {
            display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.75rem;
        }
        .page-breadcrumb a {
            font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s;
        }
        .page-breadcrumb a:hover { color: var(--cyan); }
        .page-breadcrumb .sep { color: rgba(255,255,255,0.18); font-size: 0.75rem; }
        .page-breadcrumb .current { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
        .page-banner-tag {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
            color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px;
            font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .page-banner h1 {
            font-family: var(--font-display);
            font-size: clamp(2.1rem, 3.8vw, 3.2rem);
            font-weight: 800; color: var(--white);
            line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1.1rem;
        }
        .page-banner h1 em { color: var(--cyan); font-style: normal; }
        .page-banner-sub {
            font-size: 1rem; color: rgba(255,255,255,0.55);
            max-width: 560px; line-height: 1.75; font-weight: 300;
        }
        .page-banner-stats {
            display: flex; gap: 2.5rem; margin-top: 2.5rem; flex-wrap: wrap;
        }
        .pbs-item .pbs-num {
            font-family: var(--font-display); font-size: 1.6rem; font-weight: 800;
            color: var(--cyan); letter-spacing: -0.03em;
        }
        .pbs-item .pbs-label {
            font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-top: 0.15rem;
        }

        /* ── CTA SHARED ── */
        .site-cta-strip {
            background: var(--cyan); padding: 4rem 6rem;
            display: flex; align-items: center; justify-content: space-between;
            gap: 2rem; flex-wrap: wrap;
        }
        .site-cta-strip h2 {
            font-family: var(--font-display); font-size: clamp(1.5rem, 2.5vw, 2rem);
            font-weight: 800; color: var(--white); margin-bottom: 0.4rem;
            letter-spacing: -0.02em;
        }
        .site-cta-strip p { color: rgba(255,255,255,0.78); max-width: 460px; font-size: 0.93rem; }
        .btn-white-solid {
            background: var(--white); color: var(--cyan-deep);
            padding: 0.9rem 2rem; border-radius: 8px; flex-shrink: 0;
            font-family: var(--font-display); font-size: 0.93rem; font-weight: 700;
            text-decoration: none; transition: transform 0.15s; white-space: nowrap; display: inline-block;
        }
        .btn-white-solid:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }

        /* ── REVEAL ANIMATION ── */
        .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1);
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

        /* ── SCANLINES ── */
        .scanlines::after {
            content: '';
            position: absolute; inset: 0; z-index: 1;
            background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(0,0,0,0.03) 3px, rgba(0,0,0,0.03) 4px);
            pointer-events: none;
        }

        /* ── GLASS CARD ── */
        .glass-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(16px);
            border: 1.5px solid rgba(177,236,255,0.3);
        }

        /* ── VALUE CARD (about page) ── */
        .value-card {
            clip-path: inset(0 0 100% 0);
            transition: clip-path 0.6s cubic-bezier(0.22,1,0.36,1), border-color 0.25s, box-shadow 0.25s, transform 0.25s;
        }
        .value-card.visible { clip-path: inset(0 0 0% 0); }
        .value-card:hover {
            border-color: rgba(0,184,219,0.5) !important;
            box-shadow: 0 0 40px rgba(0,184,219,0.1), inset 0 0 20px rgba(0,184,219,0.04);
            transform: translateY(-2px);
        }

        /* ── TIMELINE ── */
        .tl-track::before {
            content: '';
            position: absolute; left: 0; top: 0;
            width: 1px; height: 0;
            background: linear-gradient(to bottom, rgba(0,184,219,0.6) 0%, rgba(0,184,219,0.1) 100%);
            transition: height 1.8s cubic-bezier(0.22,1,0.36,1);
        }
        .tl-track.drawn::before { height: 100%; }

        @media (max-width: 1280px) {
            .site-nav { padding: 0.9rem 2rem; }
            .site-section { padding: 4rem 3rem; }
            .site-section-sm { padding: 2.5rem 3rem; }
            .page-banner-content { padding: 3.5rem 3rem; }
            .site-cta-strip { padding: 3rem 3rem; }
        }
        @media (max-width: 1024px) {
            .site-nav-links { display: none; }
            .mobile-toggle { display: flex; }
            .site-footer { padding: 3rem 2rem 2rem; }
            .site-footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .site-section { padding: 3.5rem 2rem; }
            .site-section-sm { padding: 2rem 2rem; }
            .page-banner-content { padding: 3rem 2rem; }
            .page-banner-accent { display: none; }
            .site-cta-strip { padding: 2.5rem 2rem; flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 640px) {
            .site-nav { padding: 0.85rem 1.25rem; }
            .site-footer { padding: 2.5rem 1.25rem 2rem; }
            /* 2-column footer on mobile: brand takes full width, rest 2-col */
            .site-footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 1.75rem 1.25rem;
            }
            .footer-brand {
                grid-column: 1 / -1; /* brand spans full width */
            }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
            .page-banner-content { padding: 2.5rem 1.25rem; }
            .page-banner h1 { font-size: clamp(1.7rem,8vw,2.5rem); }
            .page-banner-stats { gap: 1.5rem; }
        }

        /* ── PAGE PADDING for fixed nav ── */
        .page-body { padding-top: 58px; }
    </style>
</head>
<body>

{{-- ─── NAVBAR ──────────────────────────────────────────────────────────── --}}
<nav class="site-nav" id="siteNav" x-data="{ mobileOpen: false, userOpen: false }">
    {{-- Logo --}}
    <a href="{{ route('home') }}" wire:navigate class="site-nav-logo">
        @php
            $logoPath = public_path('assets/images/logo.svg');
            $hasLogo  = file_exists($logoPath) && filesize($logoPath) > 0;
        @endphp
        @if($hasLogo)
            <img src="{{ asset('assets/images/logo cyan.png') }}" alt="Exchosoft Consult">
        @else
            <div class="logo-fallback">E</div>
        @endif
    </a>

    {{-- Desktop Nav Links --}}
    <div class="site-nav-links">
        <a href="{{ route('home') }}" wire:navigate class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('site.about') }}" wire:navigate class="{{ request()->routeIs('site.about') ? 'active' : '' }}">About</a>
        <a href="{{ route('site.products') }}" wire:navigate class="{{ request()->routeIs('site.products*') ? 'active' : '' }}">Products</a>
        <a href="{{ route('site.services') }}" wire:navigate class="{{ request()->routeIs('site.services') ? 'active' : '' }}">Services</a>
        <a href="{{ route('site.case-studies') }}" wire:navigate class="{{ request()->routeIs('site.case-studies*') ? 'active' : '' }}">Case Studies</a>
        <a href="{{ route('site.blog') }}" wire:navigate class="{{ request()->routeIs('site.blog*') ? 'active' : '' }}">Blog</a>
        <a href="{{ route('site.consulting') }}" wire:navigate class="{{ request()->routeIs('site.consulting') ? 'active' : '' }}">Consulting</a>
    </div>

    {{-- Right Actions --}}
    <div class="site-nav-right">
        @auth
            <div class="nav-user-menu" x-data="{ open: false }">
                <button @click="open = !open" class="nav-user-btn">
                    <div class="nav-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>
                    <svg style="width:11px;height:11px;margin-left:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open=false" class="nav-user-dropdown">
                    <a href="{{ route('customer.dashboard') }}" wire:navigate @click="open=false">My Account</a>
                    <a href="{{ route('customer.orders') }}" wire:navigate @click="open=false">My Orders</a>
                    <a href="{{ route('customer.licenses') }}" wire:navigate @click="open=false">My Licenses</a>
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit" class="danger">Sign Out</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('customer.login') }}" wire:navigate class="nav-btn-ghost">Sign In</a>
        @endauth
        <a href="{{ route('site.consulting') }}" wire:navigate class="nav-btn-cta">Talk to Us</a>

        {{-- Mobile toggle --}}
        <button @click="mobileOpen = !mobileOpen" class="mobile-toggle" aria-label="Menu">
            <svg style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div :class="mobileOpen ? 'open' : ''" class="mobile-menu">
        <a href="{{ route('home') }}" wire:navigate @click="mobileOpen=false">Home</a>
        <a href="{{ route('site.about') }}" wire:navigate @click="mobileOpen=false">About</a>
        <a href="{{ route('site.products') }}" wire:navigate @click="mobileOpen=false">Products</a>
        <a href="{{ route('site.services') }}" wire:navigate @click="mobileOpen=false">Services</a>
        <a href="{{ route('site.case-studies') }}" wire:navigate @click="mobileOpen=false">Case Studies</a>
        <a href="{{ route('site.white-papers') }}" wire:navigate @click="mobileOpen=false">White Papers</a>
        <a href="{{ route('site.blog') }}" wire:navigate @click="mobileOpen=false">Blog</a>
        <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileOpen=false">Consulting</a>
        <a href="{{ route('site.contact') }}" wire:navigate @click="mobileOpen=false">Contact</a>
        <div class="mobile-actions">
            @auth
                <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-ghost">My Account</a>
            @else
                <a href="{{ route('customer.login') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-ghost">Sign In</a>
            @endauth
            <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-cta">Talk to Us</a>
        </div>
    </div>
</nav>

{{-- ─── PAGE CONTENT ─────────────────────────────────────────────────────── --}}
<main class="page-body">
    {{ $slot }}
</main>

{{-- ─── FOOTER ──────────────────────────────────────────────────────────── --}}
<footer class="site-footer">
    <div class="site-footer-grid">
        {{-- Brand --}}
        <div class="footer-brand">
            <div class="footer-brand-name">Exchosoft Consult</div>
            <div class="footer-brand-tag">Software Development &amp; Technology Consultancy</div>
            <p>We build custom software for Black businesses across Africa, the Caribbean, and the diaspora — designed for the conditions you actually operate in.</p>
            <div class="footer-social">
                <a href="mailto:contact@exchosoft.com" title="Email">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </a>
                <a href="#" title="LinkedIn">
                    <svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
            </div>
        </div>

        {{-- Services --}}
        <div class="footer-col">
            <h4>Services</h4>
            <ul>
                <li><a href="{{ route('site.services') }}" wire:navigate>All Services</a></li>
                <li><a href="{{ route('site.services') }}#custom-dev" wire:navigate>Custom Development</a></li>
                <li><a href="{{ route('site.services') }}#consulting" wire:navigate>Tech Consulting</a></li>
                <li><a href="{{ route('site.consulting') }}" wire:navigate>Book Consultation</a></li>
            </ul>
        </div>

        {{-- Resources --}}
        <div class="footer-col">
            <h4>Resources</h4>
            <ul>
                <li><a href="{{ route('site.blog') }}" wire:navigate>Tech Blog</a></li>
                <li><a href="{{ route('site.white-papers') }}" wire:navigate>White Papers</a></li>
                <li><a href="{{ route('site.case-studies') }}" wire:navigate>Case Studies</a></li>
                <li><a href="{{ route('site.products') }}" wire:navigate>Products</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="{{ route('site.about') }}" wire:navigate>About Us</a></li>
                <li><a href="{{ route('site.contact') }}" wire:navigate>Contact</a></li>
                <li><a href="{{ route('customer.register') }}" wire:navigate>Create Account</a></li>
                <li><a href="mailto:contact@exchosoft.com">contact@exchosoft.com</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Exchosoft Consult. All rights reserved.</p>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('site.privacy-policy') }}" wire:navigate style="font-size:0.72rem;color:rgba(255,255,255,0.28);text-decoration:none;" class="hover:text-cyan-400">Privacy</a>
            <a href="{{ route('site.terms-of-service') }}" wire:navigate style="font-size:0.72rem;color:rgba(255,255,255,0.28);text-decoration:none;" class="hover:text-cyan-400">Terms</a>
            <a href="{{ route('site.cookie-policy') }}" wire:navigate style="font-size:0.72rem;color:rgba(255,255,255,0.28);text-decoration:none;" class="hover:text-cyan-400">Cookies</a>
        </div>
        <div class="footer-location">
            <span class="footer-location-dot"></span>
            Accra, Ghana &mdash; Serving Africa, the Caribbean &amp; the Diaspora
        </div>
    </div>
</footer>

{{-- ─── REVEAL SCRIPT ─────────────────────────────────────────────────────── --}}
<script>
(function() {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                if (e.target.classList.contains('tl-track')) e.target.classList.add('drawn');
                if (e.target.classList.contains('value-card')) e.target.classList.add('visible');
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal, .value-card, .tl-track').forEach(el => obs.observe(el));
})();
</script>

@livewireScripts
</body>
</html>
