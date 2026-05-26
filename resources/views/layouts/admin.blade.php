<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ExchoSoft Admin' }}</title>
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false, profileOpen: false }">

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
     @click="sidebarOpen = false"></div>

{{-- Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 flex flex-col transition-transform duration-300 ease-in-out
              -translate-x-full lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Brand --}}
    <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-700/50">
        @php
            $logoPath = public_path('assets/images/logo.png');
            $iconPath = public_path('assets/images/icon.png');
            $hasLogo  = file_exists($logoPath) && filesize($logoPath) > 0;
            $hasIcon  = file_exists($iconPath) && filesize($iconPath) > 0;
        @endphp

        @if($hasIcon)
            <img src="{{ asset('assets/images/icon.png') }}" alt="ExchoLicense Icon" class="h-9 w-9 rounded-xl object-cover shadow-lg">
        @else
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-600 shadow-lg">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
        @endif

        <div>
            @if($hasLogo)
                <img src="{{ asset('assets/images/logo.png') }}" alt="ExchoSoft" class="h-5 w-auto brightness-0 invert">
            @else
                <p class="text-sm font-bold text-white tracking-wide">ExchoSoft</p>
            @endif
            <p class="text-xs text-slate-400">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">

        <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Overview</p>

        <a href="{{ route('admin.dashboard') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- ── WEBSITE SECTION ─────────────────────────────────────────────── --}}
        <p class="px-3 pt-4 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Website</p>

        <a href="{{ route('admin.shop-products') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.shop-products*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Shop Products
        </a>

        <a href="{{ route('admin.orders') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.orders*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Orders
        </a>

        <a href="{{ route('admin.demo-bookings') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.demo-bookings*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Demo Bookings
        </a>

        <a href="{{ route('admin.consulting') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.consulting*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Consulting & Gigs
        </a>

        {{-- ── CONTENT SECTION ─────────────────────────────────────────────── --}}
        <p class="px-3 pt-4 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Content</p>

        <a href="{{ route('admin.blog-posts') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.blog-posts*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Tech Blog
        </a>

        <a href="{{ route('admin.white-papers') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.white-papers*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            White Papers
        </a>

        <a href="{{ route('admin.case-studies') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.case-studies*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Case Studies
        </a>

        <a href="{{ route('admin.portfolio') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.portfolio*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Portfolio
        </a>

        {{-- ── LICENSE MANAGEMENT SECTION ──────────────────────────────────── --}}
        <p class="px-3 pt-4 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Licensing</p>

        <a href="{{ route('admin.products') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.products*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            License Products
        </a>

        <a href="{{ route('admin.licenses') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.licenses*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Licenses
        </a>

        <a href="{{ route('admin.customers') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.customers*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            License Customers
        </a>

        <a href="{{ route('admin.activations') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.activations*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
            </svg>
            Activations
        </a>

        <a href="{{ route('admin.batches') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.batches*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Batches
        </a>

        <p class="px-3 pt-4 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Developer</p>

        <a href="{{ route('admin.api-tokens') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.api-tokens*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            API Tokens
        </a>

        <a href="{{ route('admin.audit-logs') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.audit-logs*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Audit Logs
        </a>

        <a href="{{ route('admin.api-docs') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.api-docs*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            API Docs
        </a>

        <p class="px-3 pt-4 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Settings</p>

        <a href="{{ route('admin.profile') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.profile*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            My Profile
        </a>

        @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('admin.admins') }}"
           wire:navigate
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('admin.admins*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Admin Users
            @if(auth()->user()?->is_main_admin)
                <span class="ml-auto inline-flex items-center rounded-full bg-violet-500/20 px-2 py-0.5 text-xs font-semibold text-violet-300">ROOT</span>
            @endif
        </a>
        @endif

    </nav>

    {{-- User profile footer --}}
    <div class="border-t border-slate-700/50 px-4 py-4">
        <div class="flex items-center gap-3">
            @if(auth()->user()?->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                     class="h-9 w-9 rounded-full object-cover flex-shrink-0 ring-2 ring-slate-600"
                     alt="{{ auth()->user()->name }}">
            @else
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-500 text-white text-sm font-bold flex-shrink-0">
                    {{ auth()->user()?->initials() ?? 'A' }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-slate-400 truncate">
                    {{ auth()->user()->is_main_admin ? 'Main Admin' : ucwords(str_replace('_', ' ', auth()->user()->role ?? 'Admin')) }}
                </p>
            </div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2">
            <a href="{{ route('admin.profile') }}" wire:navigate
               class="flex items-center justify-center gap-1.5 rounded-xl px-2 py-1.5 text-xs font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all duration-150">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 rounded-xl px-2 py-1.5 text-xs font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all duration-150">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Main content --}}
<div class="lg:pl-64 flex flex-col min-h-full">

    {{-- Top bar --}}
    <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/95 backdrop-blur px-4 lg:px-6 shadow-sm">
        {{-- Mobile menu button --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page title slot --}}
        <div class="flex-1">
            @isset($heading)
                <h1 class="text-lg font-semibold text-slate-900">{{ $heading }}</h1>
            @endisset
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-3">
            {{-- Quick links --}}
            <a href="{{ route('admin.api-docs') }}" wire:navigate
               class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:border-cyan-300 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                API Docs
            </a>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    @if(auth()->user()?->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="h-5 w-5 rounded-full object-cover" alt="">
                    @else
                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-cyan-500 text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <span class="hidden sm:inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-slate-100 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                     {{ auth()->user()->is_main_admin ? 'bg-violet-100 text-violet-700' : 'bg-cyan-50 text-cyan-700' }}">
                            {{ auth()->user()->is_main_admin ? 'Main Admin' : ucwords(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}
                        </span>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.profile') }}" wire:navigate @click="open = false"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                        @if(auth()->user()?->isSuperAdmin())
                        <a href="{{ route('admin.admins') }}" wire:navigate @click="open = false"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Manage Admins
                        </a>
                        @endif
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- App version badge --}}
            <span class="hidden sm:inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-600/20">
                v2.1
            </span>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-4 lg:p-6">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 px-6 py-4">
        <p class="text-xs text-slate-400 text-center">
            &copy; {{ date('Y') }} ExchoSoft &mdash; Business Management Platform &mdash; Currency: GHS (Ghana Cedis)
        </p>
    </footer>
</div>

@livewireScripts
</body>
</html>
