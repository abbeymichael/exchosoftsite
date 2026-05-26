<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Exchosoft Consult — Software Development & Technology Consultancy' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Exchosoft Consult is a Ghana-based technology consultancy and software development company serving Black businesses across Africa, the Caribbean, and the diaspora.' }}">
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
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

        /* ── SITE NAV ── */
        .site-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 1.1rem 4rem;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(13,33,55,0.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,184,219,0.1);
            transition: padding 0.3s;
        }
        .site-nav-logo {
            display: flex; align-items: center; gap: 0.75rem;
            font-family: var(--font-display); font-size: 1rem; font-weight: 700;
            color: var(--white); text-decoration: none; letter-spacing: -0.01em;
            flex-shrink: 0;
        }
        .site-nav-logo img { height: 32px; width: auto; }
        .site-nav-logo .logo-fallback {
            height: 32px; width: 32px; border-radius: 8px;
            background: var(--cyan); display: flex; align-items: center;
            justify-content: center; font-size: 0.9rem; font-weight: 800; color: var(--white);
        }
        .site-nav-links { display: flex; align-items: center; gap: 0.25rem; }
        .site-nav-links a {
            font-size: 0.82rem; font-weight: 500; color: rgba(255,255,255,0.65);
            text-decoration: none; letter-spacing: 0.01em;
            padding: 0.45rem 0.9rem; border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }
        .site-nav-links a:hover { color: var(--white); background: rgba(255,255,255,0.08); }
        .site-nav-links a.active { color: var(--cyan); }
        .site-nav-right { display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; }
        .nav-btn-ghost {
            background: transparent; color: rgba(255,255,255,0.65);
            padding: 0.45rem 1rem; border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.15);
            font-family: var(--font-display); font-size: 0.8rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s; white-space: nowrap;
        }
        .nav-btn-ghost:hover { color: var(--white); border-color: rgba(255,255,255,0.3); }
        .nav-btn-cta {
            background: var(--cyan); color: var(--white);
            padding: 0.5rem 1.2rem; border-radius: 6px;
            font-family: var(--font-display); font-size: 0.8rem; font-weight: 600;
            text-decoration: none; transition: background 0.2s; white-space: nowrap;
        }
        .nav-btn-cta:hover { background: var(--cyan-dark); }
        .nav-user-menu { position: relative; }
        .nav-user-btn {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
            padding: 0.35rem 0.85rem 0.35rem 0.5rem; border-radius: 6px; cursor: pointer;
            font-size: 0.8rem; color: rgba(255,255,255,0.75); font-family: var(--font-display); font-weight: 500;
            transition: background 0.2s;
        }
        .nav-user-btn:hover { background: rgba(255,255,255,0.12); }
        .nav-user-avatar {
            width: 22px; height: 22px; border-radius: 50%;
            background: var(--cyan); display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 800; color: var(--white);
        }
        .nav-user-dropdown {
            position: absolute; right: 0; top: calc(100% + 0.5rem);
            background: var(--white); border: 1px solid var(--border);
            border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            min-width: 180px; overflow: hidden; z-index: 200;
        }
        .nav-user-dropdown a, .nav-user-dropdown button {
            display: block; width: 100%; text-align: left;
            padding: 0.65rem 1rem; font-size: 0.82rem; color: var(--text-secondary);
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
            padding: 1rem 2rem 1.5rem;
        }
        .mobile-menu.open { display: block; }
        .mobile-menu a {
            display: block; padding: 0.65rem 0; font-size: 0.9rem; font-weight: 500;
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
            font-size: 0.85rem; color: rgba(255,255,255,0.45); line-height: 1.8;
            margin-top: 0.75rem; max-width: 280px;
        }
        .footer-brand-name {
            font-family: var(--font-display); font-weight: 700; font-size: 1.05rem;
            color: var(--white); letter-spacing: -0.01em;
        }
        .footer-brand-tag { font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-top: 0.2rem; }
        .footer-col h4 {
            font-family: var(--font-display); font-size: 0.82rem; font-weight: 700;
            color: var(--white); letter-spacing: 0.04em; text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .footer-col ul { list-style: none; margin: 0; padding: 0; }
        .footer-col ul li { margin-bottom: 0.6rem; }
        .footer-col ul li a {
            font-size: 0.83rem; color: rgba(255,255,255,0.45);
            text-decoration: none; transition: color 0.2s;
        }
        .footer-col ul li a:hover { color: var(--cyan); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 1.75rem;
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap;
        }
        .footer-bottom p { font-size: 0.78rem; color: rgba(255,255,255,0.3); margin: 0; }
        .footer-location {
            font-size: 0.78rem; color: rgba(255,255,255,0.3);
            display: flex; align-items: center; gap: 0.4rem;
        }
        .footer-location-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--cyan); flex-shrink: 0; }

        @media (max-width: 1024px) {
            .site-nav { padding: 1rem 2rem; }
            .site-nav-links { display: none; }
            .mobile-toggle { display: flex; }
            .site-footer { padding: 3rem 2rem 2rem; }
            .site-footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }
        @media (max-width: 640px) {
            .site-nav { padding: 1rem 1.25rem; }
            .site-footer { padding: 2.5rem 1.25rem 2rem; }
            .site-footer-grid { grid-template-columns: 1fr; gap: 1.75rem; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
        }

        /* ── PAGE PADDING for fixed nav ── */
        .page-body { padding-top: 62px; }
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
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Exchosoft Consult">
        @else
            <div class="logo-fallback">E</div>
        @endif
        Exchosoft Consult
    </a>

    {{-- Desktop Nav Links --}}
    <div class="site-nav-links">
        <a href="{{ route('home') }}" wire:navigate class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('site.about') }}" wire:navigate class="{{ request()->routeIs('site.about') ? 'active' : '' }}">About</a>
        <a href="{{ route('site.products') }}" wire:navigate class="{{ request()->routeIs('site.products*') ? 'active' : '' }}">Products</a>
        <a href="{{ route('site.portfolio') }}" wire:navigate class="{{ request()->routeIs('site.portfolio*') ? 'active' : '' }}">Portfolio</a>
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
                    <svg style="width:12px;height:12px;margin-left:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
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
        <a href="{{ route('site.book-demo') }}" wire:navigate class="nav-btn-cta">Talk to Us</a>

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
        <a href="{{ route('site.portfolio') }}" wire:navigate @click="mobileOpen=false">Portfolio</a>
        <a href="{{ route('site.case-studies') }}" wire:navigate @click="mobileOpen=false">Case Studies</a>
        <a href="{{ route('site.white-papers') }}" wire:navigate @click="mobileOpen=false">White Papers</a>
        <a href="{{ route('site.blog') }}" wire:navigate @click="mobileOpen=false">Blog</a>
        <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileOpen=false">Consulting</a>
        <div class="mobile-actions">
            @auth
                <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-ghost">My Account</a>
            @else
                <a href="{{ route('customer.login') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-ghost">Sign In</a>
            @endauth
            <a href="{{ route('site.book-demo') }}" wire:navigate @click="mobileOpen=false" class="nav-btn-cta">Talk to Us</a>
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
            <div class="footer-brand-tag">Software Development & Technology Consultancy</div>
            <p>We build custom software for Black businesses across Africa, the Caribbean, and the diaspora — designed for the conditions you actually operate in.</p>
        </div>

        {{-- Products --}}
        <div class="footer-col">
            <h4>Products</h4>
            <ul>
                <li><a href="{{ route('site.products') }}" wire:navigate>All Products</a></li>
                <li><a href="{{ route('site.products') }}#washops" wire:navigate>WashOps</a></li>
                <li><a href="{{ route('site.products') }}#churchops" wire:navigate>ChurchOps</a></li>
                <li><a href="{{ route('site.book-demo') }}" wire:navigate>Book a Demo</a></li>
            </ul>
        </div>

        {{-- Resources --}}
        <div class="footer-col">
            <h4>Resources</h4>
            <ul>
                <li><a href="{{ route('site.blog') }}" wire:navigate>Tech Blog</a></li>
                <li><a href="{{ route('site.white-papers') }}" wire:navigate>White Papers</a></li>
                <li><a href="{{ route('site.case-studies') }}" wire:navigate>Case Studies</a></li>
                <li><a href="{{ route('site.portfolio') }}" wire:navigate>Portfolio</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="{{ route('site.about') }}" wire:navigate>About Us</a></li>
                <li><a href="{{ route('site.consulting') }}" wire:navigate>Consulting</a></li>
                <li><a href="{{ route('customer.register') }}" wire:navigate>Create Account</a></li>
                <li><a href="mailto:contact@exchosoft.com">contact@exchosoft.com</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Exchosoft Consult. All rights reserved.</p>
        <div class="footer-location">
            <span class="footer-location-dot"></span>
            Accra, Ghana &mdash; Serving Africa, the Caribbean &amp; the Diaspora
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
