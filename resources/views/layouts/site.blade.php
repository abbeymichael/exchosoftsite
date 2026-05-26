<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ExchoSoft — Innovative Software Solutions' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'ExchoSoft builds innovative software products, offers consulting, and delivers enterprise solutions for businesses across Africa and beyond.' }}">
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-white text-slate-900" x-data="{ mobileMenu: false, userMenu: false }">

{{-- ─── NAVBAR ──────────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Logo --}}
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5">
                @php
                    $iconPath = public_path('assets/images/logo.svg');
                    $hasIcon  = file_exists($iconPath) && filesize($iconPath) > 0;
                @endphp
                @if($hasIcon)
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="ExchoSoft" class="h-8 w-8 rounded-lg object-cover">
                @else
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-600 text-white font-bold text-sm">E</div>
                @endif
                <span class="text-lg font-bold text-slate-900">ExchoSoft</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('site.products') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.products*') ? 'bg-slate-100 text-slate-900' : '' }}">Products</a>
                <a href="{{ route('site.portfolio') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.portfolio*') ? 'bg-slate-100 text-slate-900' : '' }}">Portfolio</a>
                <a href="{{ route('site.case-studies') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.case-studies*') ? 'bg-slate-100 text-slate-900' : '' }}">Case Studies</a>
                <a href="{{ route('site.white-papers') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.white-papers*') ? 'bg-slate-100 text-slate-900' : '' }}">White Papers</a>
                <a href="{{ route('site.blog') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.blog*') ? 'bg-slate-100 text-slate-900' : '' }}">Blog</a>
                <a href="{{ route('site.consulting') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors {{ request()->routeIs('site.consulting*') ? 'bg-slate-100 text-slate-900' : '' }}">Consulting</a>
            </nav>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">
                @auth
                    {{-- Logged in customer --}}
                    <div class="relative hidden sm:block" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-cyan-500 text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open=false" class="absolute right-0 mt-2 w-44 rounded-xl bg-white shadow-lg ring-1 ring-slate-100 py-1 z-50">
                            <a href="{{ route('customer.dashboard') }}" wire:navigate @click="open=false" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">My Account</a>
                            <a href="{{ route('customer.orders') }}" wire:navigate @click="open=false" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">My Orders</a>
                            <a href="{{ route('customer.licenses') }}" wire:navigate @click="open=false" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">My Licenses</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}" wire:navigate class="hidden sm:inline-flex rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Sign In</a>
                    <a href="{{ route('customer.register') }}" wire:navigate class="inline-flex rounded-xl bg-cyan-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-sm">Get Started</a>
                @endauth

                {{-- Mobile menu toggle --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden rounded-lg p-2 text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-transition class="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('site.products') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Products</a>
        <a href="{{ route('site.portfolio') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Portfolio</a>
        <a href="{{ route('site.case-studies') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Case Studies</a>
        <a href="{{ route('site.white-papers') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">White Papers</a>
        <a href="{{ route('site.blog') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Blog</a>
        <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileMenu=false" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Consulting</a>
        <div class="border-t border-slate-100 pt-2 mt-2">
            @auth
                <a href="{{ route('customer.dashboard') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">My Account</a>
            @else
                <a href="{{ route('customer.login') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Sign In</a>
                <a href="{{ route('customer.register') }}" wire:navigate class="block rounded-xl bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-700 mt-1 text-center">Get Started</a>
            @endauth
        </div>
    </div>
</header>

{{-- ─── PAGE CONTENT ─────────────────────────────────────────────────────── --}}
<main>
    {{ $slot }}
</main>

{{-- ─── FOOTER ──────────────────────────────────────────────────────────── --}}
<footer class="bg-slate-900 text-white mt-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Brand --}}
            <div class="col-span-1 lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    @if($hasIcon ?? false)
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="ExchoSoft" class="h-8 w-8 rounded-lg object-cover">
                    @else
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-600 text-white font-bold text-sm">E</div>
                    @endif
                    <span class="text-lg font-bold">ExchoSoft</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">Building innovative software solutions, enterprise tools, and consulting services for businesses in Africa and beyond.</p>
            </div>

            {{-- Products --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Products</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('site.products') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">All Products</a></li>
                    <li><a href="{{ route('site.products') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">CoreOps</a></li>
                    <li><a href="{{ route('site.products') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Michelle POS</a></li>
                    <li><a href="{{ route('site.products') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">PGOps</a></li>
                    <li><a href="{{ route('site.products') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Luvora</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Resources</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('site.blog') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Tech Blog</a></li>
                    <li><a href="{{ route('site.white-papers') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">White Papers</a></li>
                    <li><a href="{{ route('site.case-studies') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Case Studies</a></li>
                    <li><a href="{{ route('site.portfolio') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Portfolio</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Company</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('site.consulting') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Consulting</a></li>
                    <li><a href="{{ route('site.book-demo') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Book a Demo</a></li>
                    <li><a href="{{ route('customer.register') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Create Account</a></li>
                    <li><a href="{{ route('customer.login') }}" wire:navigate class="text-sm text-slate-400 hover:text-white transition-colors">Customer Login</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} ExchoSoft. All rights reserved. Accra, Ghana.</p>
            <div class="flex items-center gap-4">
                <span class="text-xs text-slate-600 bg-slate-800 px-2 py-1 rounded-full">Currency: GHS</span>
            </div>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
