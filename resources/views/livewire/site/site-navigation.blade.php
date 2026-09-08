<div class="sticky top-0 z-50 flex flex-col" id="sticky-shell" x-data="siteNav()">

    {{-- Notification Carousel --}}
    @if (count($notifications) > 0)
        <div class="border-b border-[rgba(0,184,219,0.2)] transition-all duration-300" x-data="{
            autoRotateInterval: null,
            autoRotate() {
                this.autoRotateInterval = setInterval(() => {
                    $wire.nextNotification();
                }, 5000);
            },
            resetAutoRotate() {
                clearInterval(this.autoRotateInterval);
                this.autoRotate();
            }
        }"
            x-init="autoRotate()" @mouseenter="clearInterval(autoRotateInterval)" @mouseleave="resetAutoRotate()"
            x-show="notifOpen" x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="opacity-100 max-h-10"
            x-transition:leave-end="opacity-0 max-h-0 overflow-hidden py-0"
            style="background:linear-gradient(90deg,#08121d 0%,#0d2137 40%,#0a1e30 60%,#08121d 100%);box-shadow:0 1px 0 rgba(0,184,219,.12),inset 0 1px 0 rgba(0,184,219,.06);">

            {{-- Carousel Container --}}
            <div class="relative flex items-center justify-center py-2 px-10">
                <span
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#00b8db] opacity-70 hidden md:block"
                    style="box-shadow:0 0 6px rgba(0,184,219,.8)"></span>

                {{-- Notification Content (Auto-transitions) --}}
                @foreach ($notifications as $index => $notification)
                    <div @if ($index === $currentNotificationIndex) x-transition:enter="transition duration-500 ease-in-out"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0" @endif
                        class="{{ $index === $currentNotificationIndex ? 'block' : 'hidden' }} flex-1">
                        <p class="text-[12px] text-white/85 tracking-wide text-center">
                            <span class="text-[#00b8db] font-semibold mr-1">{{ $notification['message'] }}</span>
                            @if ($notification['button_label'] && $notification['button_url'])
                                <a class="text-[#b1ecff] hover:text-white underline-offset-2 hover:underline transition-colors"
                                    href="{{ $notification['button_url'] }}"
                                    wire:navigate>{{ $notification['button_label'] }}</a>
                            @endif
                        </p>
                    </div>
                @endforeach

                {{-- Navigation Controls --}}
                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                    {{-- Indicator Dots --}}
                    @if (count($notifications) > 1)
                        <div class="flex gap-1.5 mr-3">
                            @foreach ($notifications as $index => $notification)
                                <button wire:click="goToNotification({{ $index }})" @class([
                                    'w-2 h-2 rounded-full transition-all',
                                    'bg-[#00b8db]' => $index === $currentNotificationIndex,
                                    'bg-white/30 hover:bg-white/50' => $index !== $currentNotificationIndex,
                                ])"
                                    aria-label="Go to notification {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Dismiss Button --}}
                    @if ($notifications[$currentNotificationIndex]['is_dismissible'])
                        <button wire:click="dismissNotification"
                            class="w-6 h-6 flex items-center justify-center rounded-full text-white/50 hover:text-white hover:bg-white/10 transition-all"
                            aria-label="Dismiss">
                            <i class="ti ti-x text-base leading-none"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <header class="transition-all duration-500 shadow-sm"
        :class="scrolled ? 'border-b border-[rgba(0,184,219,0.15)]' : 'border-b border-white/10'"
        style="background:rgba(0,9,23,.95);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);">
        <nav class="flex justify-between items-center w-full px-4 md:px-10 lg:px-16 py-3">

            {{-- Brand --}}
            <div class="flex items-center gap-2">
                @php
                    $logoPng = public_path('assets/images/logo cyan.png');
                    $hasLogo = file_exists($logoPng) && filesize($logoPng) > 0;
                @endphp
                @if ($hasLogo)
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/logo cyan.png') }}" alt="Exchosoft Consult"
                            class="h-10 w-auto">
                    </a>
                @else
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                        <i class="ti ti-topology-star-3 text-[#4cd9fd] text-3xl"></i>
                        <span class="font-['Syne'] text-2xl font-bold tracking-tight text-white">Exchosoft
                            Consult</span>
                    </a>
                @endif
            </div>

            {{-- Desktop Navigation --}}
            <ul class="hidden md:flex items-center gap-6 lg:gap-8">

                <li>
                    <a href="{{ route('home') }}" wire:navigate
                        class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('home') ? 'text-[#00b8db]' : 'text-white/70 hover:text-[#b1ecff]' }}">
                        Home
                    </a>
                </li>

                <li>
                    <a href="{{ route('site.about') }}" wire:navigate
                        class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('site.about') ? 'text-[#00b8db]' : 'text-white/70 hover:text-[#b1ecff]' }}">
                        About
                    </a>
                </li>

                {{-- Products Mega Menu (900px, centered) --}}
                <li class="relative group/products" @mouseenter="activeMenu = 'products'"
                    @mouseleave="activeMenu = null">
                    <button
                        class="relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
      {{ request()->routeIs('site.products*') ? 'text-[#00b8db]' : 'text-white/70 group-hover/products:text-[#b1ecff]' }}">
                        Products
                        <i class="ti ti-chevron-down text-sm transition-transform duration-200"
                            :class="activeMenu === 'products' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="activeMenu==='products'" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-[900px] max-w-[calc(100vw-2rem)] z-50">
                        <div class="bg-[#0d2137] border border-[#c4c6cd]/10 rounded-xl shadow-2xl overflow-hidden">
                            <div class="flex flex-col md:flex-row min-h-[380px]">
                                {{-- Left image banner (same pattern as case studies) --}}
                                <div class="w-full md:w-2/5 relative overflow-hidden min-h-[200px] md:min-h-0">
                                    {{-- Use a reliable image URL (replace with your own asset) --}}
                                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&h=500&fit=crop"
                                        alt="Products Showcase" class="absolute inset-0 w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-[#0d2137]/80 via-[#0d2137]/60 to-transparent">
                                    </div>
                                    <div class="absolute inset-0 dot-matrix opacity-20"></div>
                                    <div class="absolute inset-0 bg-gradient-to-l from-[#000917]/70 to-transparent">
                                    </div>
                                    <div class="absolute bottom-8 left-8 right-8">
                                        <div class="h-px w-12 bg-[#4cd9fd] mb-4"></div>
                                        <h5 class="text-white font-bold text-lg font-['Syne']">Operational Excellence
                                        </h5>
                                        <p class="text-[#b1ecff] text-sm">Reliability at every scale.</p>
                                    </div>
                                </div>
                                {{-- Right content --}}
                                <div class="w-full md:w-3/5 p-6 md:p-8">
                                    <div
                                        class="mb-6 flex items-center justify-between border-b border-white/10 pb-4 flex-wrap gap-2">
                                        <span
                                            class="text-xs font-bold text-[#b1ecff] uppercase tracking-widest">Solutions
                                            Portfolio</span>
                                        <a class="text-xs font-bold text-white hover:text-[#4cd9fd] transition-colors flex items-center gap-1"
                                            href="{{ route('site.products') }}" wire:navigate>
                                            Explore All <i class="ti ti-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                                        @forelse($products as $prod)
                                            <a class="group/item flex items-start gap-3 hover:bg-white/5 p-2 -m-2 rounded-lg transition-colors"
                                                href="{{ route('site.products.show', $prod['slug']) }}" wire:navigate>
                                                <i
                                                    class="ti ti-{{ $prod['icon'] ?? 'package' }} text-[#4cd9fd] text-xl mt-0.5 shrink-0"></i>
                                                <div>
                                                    <p
                                                        class="text-sm font-bold text-white group-hover/item:text-[#b1ecff] transition-colors">
                                                        {{ $prod['name'] }}</p>
                                                    @if (!empty($prod['tagline']))
                                                        <p class="text-[11px] text-[#7689a4] mt-0.5">
                                                            {{ Str::limit($prod['tagline'], 45) }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @empty
                                            <div class="col-span-full text-xs text-[#7689a4] text-center py-4">No
                                                products available yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- Services Mega Menu (620px, centered) --}}
                <li class="relative group/services" @mouseenter="activeMenu = 'services'"
                    @mouseleave="activeMenu = null">
                    <button
                        class="relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
              {{ request()->routeIs('site.services') ? 'text-[#00b8db]' : 'text-white/70 group-hover/services:text-[#b1ecff]' }}">
                        Services
                        <i class="ti ti-chevron-down text-sm transition-transform duration-200"
                            :class="activeMenu === 'services' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="activeMenu==='services'" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-[620px] max-w-[calc(100vw-2rem)] z-50">
                        <div
                            class="bg-[#0d2137] border border-[#c4c6cd]/10 rounded-xl shadow-2xl overflow-hidden p-6 md:p-8">
                            <div class="mb-6 flex items-center justify-between border-b border-white/10 pb-4">
                                <span class="text-xs font-bold text-[#b1ecff] uppercase tracking-widest">Our
                                    Expertise</span>
                                <a class="text-xs font-bold text-white hover:text-[#4cd9fd] transition-colors flex items-center gap-1"
                                    href="{{ route('site.services') }}" wire:navigate>
                                    All Services <i class="ti ti-arrow-right text-xs"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                                @foreach ([['code', 'Custom Software Development', 'Built from the ground up for your operations'], ['brain', 'Technology Consulting', 'Strategic guidance based on real-world experience'], ['building-skyscraper', 'System Architecture', 'Offline-first, cloud, or LAN collaboration'], ['transform', 'Digital Transformation', 'Modernization that respects your reality'], ['chart-bar', 'Business Process Analysis', 'Identify bottlenecks and opportunities'], ['headset', 'Ongoing Support & Evolution', 'We stay involved as you grow']] as [$icon, $title, $sub])
                                    <a class="group/item flex items-start gap-3 hover:bg-white/5 p-2 -m-2 rounded-lg transition-colors"
                                        href="{{ route('site.services') }}" wire:navigate>
                                        <i
                                            class="ti ti-{{ $icon }} text-[#4cd9fd] text-xl mt-0.5 shrink-0"></i>
                                        <div>
                                            <p
                                                class="text-sm font-bold text-white group-hover/item:text-[#b1ecff] transition-colors">
                                                {{ $title }}</p>
                                            <p class="text-[11px] text-[#7689a4] mt-0.5">{{ $sub }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </li>

                {{-- Case Studies Mega Menu (800px, centered) --}}
                <li class="relative group/cases" @mouseenter="activeMenu = 'cases'" @mouseleave="activeMenu = null">
                    <button
                        class="relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
              {{ request()->routeIs('site.case-studies*') ? 'text-[#00b8db]' : 'text-white/70 group-hover/cases:text-[#b1ecff]' }}">
                        Case Studies
                        <i class="ti ti-chevron-down text-sm transition-transform duration-200"
                            :class="activeMenu === 'cases' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="activeMenu==='cases'" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-[800px] max-w-[calc(100vw-2rem)] z-50">
                        <div class="bg-[#0d2137] border border-[#c4c6cd]/10 rounded-xl shadow-2xl overflow-hidden">
                            <div class="flex flex-col md:flex-row min-h-[420px]">
                                <div class="w-full md:w-2/5 relative overflow-hidden min-h-[200px] md:min-h-0">
                                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&h=500&fit=crop"
                                        alt="Global Impact" class="absolute inset-0 w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-[#0d2137]/80 via-[#0d2137]/60 to-transparent">
                                    </div>
                                    <div class="absolute inset-0 dot-matrix opacity-20"></div>
                                    <div class="absolute inset-0 bg-gradient-to-l from-[#000917]/70 to-transparent">
                                    </div>
                                    <div class="absolute bottom-8 left-8 right-8">
                                        <div class="h-px w-12 bg-[#4cd9fd] mb-4"></div>
                                        <h5 class="text-white font-bold text-lg font-['Syne']">Real Impact</h5>
                                        <p class="text-[#b1ecff] text-sm">Measurable results across industries</p>
                                    </div>
                                </div>
                                <div class="w-full md:w-3/5 p-6 md:p-8">
                                    <div class="mb-6 flex items-center justify-between border-b border-white/10 pb-4">
                                        <span class="text-xs font-bold text-[#b1ecff] uppercase tracking-widest">Impact
                                            Stories</span>
                                        <a class="text-xs font-bold text-white hover:text-[#4cd9fd] transition-colors flex items-center gap-1"
                                            href="{{ route('site.case-studies') }}" wire:navigate>
                                            View All <i class="ti ti-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                    <div class="space-y-4">
                                        @forelse($caseStudies as $study)
                                            <a class="group/case flex flex-col sm:flex-row items-start gap-4 p-4 rounded-xl bg-white/5 hover:bg-[#4cd9fd]/10 border border-white/5 hover:border-[#4cd9fd]/30 transition-all"
                                                href="{{ route('site.case-studies.show', $study['slug']) }}"
                                                wire:navigate>

                                                <div class="flex-1">
                                                    <p
                                                        class="text-base font-bold text-white group-hover/case:text-[#b1ecff] transition-colors">
                                                        {{ $study['title'] }}</p>


                                                </div>
                                                <i
                                                    class="ti ti-arrow-right text-white/30 group-hover/case:text-[#4cd9fd] transition-colors text-base"></i>
                                            </a>
                                        @empty
                                            <div class="text-xs text-[#7689a4] text-center py-4">No case studies
                                                available yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <a href="{{ route('site.blog') }}" wire:navigate
                        class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('site.blog*') ? 'text-[#00b8db]' : 'text-white/70 hover:text-[#b1ecff]' }}">
                        Insights
                    </a>
                </li>
            </ul>

            {{-- Right Side Actions --}}
            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden md:block relative" x-data="{ uOpen: false }">
                        <button @click="uOpen=!uOpen"
                            class="flex items-center gap-2 bg-white/8 border border-white/12 px-3 py-1.5 rounded-lg text-white/75 text-sm font-['Syne'] font-medium hover:bg-white/12 transition-colors">
                            <span
                                class="w-5 h-5 rounded-full bg-[#00b8db] flex items-center justify-center text-[10px] font-extrabold text-[#0d2137]">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            {{ Str::words(auth()->user()->name, 1, '') }}
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="uOpen" @click.away="uOpen=false"
                            class="absolute right-0 top-full mt-2 bg-white rounded-xl border border-[#00b8db]/15 shadow-xl min-w-[180px] overflow-hidden z-50"
                            style="display:none;">
                            <a href="{{ route('customer.dashboard') }}" wire:navigate @click="uOpen=false"
                                class="block px-4 py-2.5 text-sm text-[#0d2137] hover:bg-[#f4f8fb] transition-colors">My
                                Account</a>
                            <a href="{{ route('customer.orders') }}" wire:navigate @click="uOpen=false"
                                class="block px-4 py-2.5 text-sm text-[#0d2137] hover:bg-[#f4f8fb] transition-colors">My
                                Orders</a>
                            <a href="{{ route('customer.licenses') }}" wire:navigate @click="uOpen=false"
                                class="block px-4 py-2.5 text-sm text-[#0d2137] hover:bg-[#f4f8fb] transition-colors">My
                                Licenses</a>
                            <div class="h-px bg-[#00b8db]/10 my-1"></div>
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}" wire:navigate
                        class="hidden md:inline-flex items-center gap-1 text-sm font-medium text-white/65 hover:text-white border border-white/15 hover:border-white/30 px-4 py-2 rounded-lg transition-colors">
                        Sign In
                    </a>
                @endauth

                <a href="{{ route('site.consulting') }}" wire:navigate
                    class="hidden md:inline-flex items-center gap-2 bg-[#4cd9fd] text-[#000917] font-bold text-sm px-5 py-2.5 rounded-full hover:bg-[#48d7fb] transition-colors shadow-lg shadow-[#4cd9fd]/20">
                    Talk to Us
                </a>

                <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden w-9 h-9 flex items-center justify-center rounded-full text-white hover:bg-white/10 transition-colors"
                    aria-label="Open menu">
                    <i class="ti text-2xl" :class="mobileOpen ? 'ti-x' : 'ti-menu-2'"></i>
                </button>
            </div>
        </nav>
    </header>

    {{-- Mobile Overlay --}}
    <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-[#000917]/80 backdrop-blur-sm md:hidden"></div>

    {{-- Mobile Drawer --}}
    <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 bottom-0 w-[85vw] max-w-sm z-50 md:hidden" style="background:#08121d;">
        <div class="absolute inset-0 dot-matrix opacity-100 pointer-events-none"></div>
        <div
            class="absolute inset-0 bg-gradient-to-br from-[#000917] via-[#000917]/97 to-[#0d2137]/80 pointer-events-none">
        </div>
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-[#4cd9fd]/10 blur-3xl pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col h-full overflow-y-auto px-6 pt-20 pb-10">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                @if ($hasLogo)
                    <img src="{{ asset('assets/images/logo cyan.png') }}" alt="Exchosoft Consult"
                        class="h-8 w-auto">
                @else
                    <div class="flex items-center gap-2">
                        <i class="ti ti-topology-star-3 text-[#4cd9fd] text-2xl"></i>
                        <span class="font-['Syne'] text-lg font-bold text-white">Exchosoft Consult</span>
                    </div>
                @endif
                <button @click="mobileOpen = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>

            <ul class="space-y-1">
                <li><a href="{{ route('home') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors {{ request()->routeIs('home') ? 'text-[#00b8db]' : '' }}"><i
                            class="ti ti-home text-[#4cd9fd] text-2xl"></i>Home</a></li>
                <li><a href="{{ route('site.about') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors {{ request()->routeIs('site.about') ? 'text-[#00b8db]' : '' }}"><i
                            class="ti ti-info-circle text-[#4cd9fd] text-2xl"></i>About</a></li>

                <li class="border-b border-white/8">
                    <button @click="toggleAcc('products')"
                        class="flex items-center justify-between w-full py-4 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors">
                        <span class="flex items-center gap-3"><i
                                class="ti ti-apps text-[#4cd9fd] text-2xl"></i>Products</span>
                        <i class="ti ti-chevron-down text-[#4cd9fd] transition-transform duration-300 text-2xl"
                            :class="mobileAcc === 'products' ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="mobileAcc==='products'" x-collapse class="overflow-hidden" style="display:none;">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10 mt-2">
                            <span class="text-[10px] font-bold text-[#b1ecff] uppercase tracking-widest">Solutions
                                Portfolio</span>
                            <a class="text-[10px] font-bold text-white/60 hover:text-[#b1ecff] flex items-center gap-1"
                                href="{{ route('site.products') }}" wire:navigate @click="mobileOpen = false">All
                                Products <i class="ti ti-arrow-right text-xs"></i></a>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            @forelse($products as $prod)
                                <a href="{{ route('site.products.show', $prod['slug']) }}" wire:navigate
                                    @click="mobileOpen = false"
                                    class="flex flex-col gap-1 p-3 rounded-xl bg-white/5 hover:bg-[#4cd9fd]/10 border border-white/5 hover:border-[#4cd9fd]/30 transition-all">
                                    <i class="ti ti-{{ $prod['icon'] ?? 'package' }} text-[#4cd9fd] text-lg"></i>
                                    <span class="text-sm font-bold text-white">{{ $prod['name'] }}</span>
                                    @if (!empty($prod['tagline']))
                                        <span
                                            class="text-[10px] text-[#7689a4] leading-snug">{{ Str::limit($prod['tagline'], 40) }}</span>
                                    @endif
                                </a>
                            @empty
                                @foreach ([['WashOps', 'washing-machine', 'Industrial laundry management system'], ['ChurchOps', 'building-church', 'Administrative backbone for scaling institutions']] as [$n, $icon, $sub])
                                    <a href="{{ route('site.products') }}" wire:navigate @click="mobileOpen = false"
                                        class="flex flex-col gap-1 p-3 rounded-xl bg-white/5 hover:bg-[#4cd9fd]/10 border border-white/5 hover:border-[#4cd9fd]/30 transition-all">
                                        <i class="ti ti-{{ $icon }} text-[#4cd9fd] text-lg"></i>
                                        <span class="text-sm font-bold text-white">{{ $n }}</span>
                                        <span
                                            class="text-[10px] text-[#7689a4] leading-snug">{{ $sub }}</span>
                                    </a>
                                @endforeach
                            @endforelse
                        </div>
                        <p class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-3">Coming Soon</p>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            @foreach ([['ClinicOps', 'stethoscope', 'HIPAA-ready workflow engine'], ['LabOps', 'flask', 'Automated laboratory pipelines']] as [$n, $icon, $sub])
                                <div
                                    class="flex flex-col gap-1 p-3 rounded-xl bg-white/3 border border-white/5 opacity-50">
                                    <i class="ti ti-{{ $icon }} text-[#48d7fb] text-lg"></i>
                                    <span class="text-sm font-bold text-white">{{ $n }}</span>
                                    <span
                                        class="text-[10px] text-[#7689a4] leading-snug italic">{{ $sub }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>

                <li><a href="{{ route('site.services') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors"><i
                            class="ti ti-tools text-[#4cd9fd] text-2xl"></i>Services</a></li>

                <li class="border-b border-white/8">
                    <button @click="toggleAcc('cases')"
                        class="flex items-center justify-between w-full py-4 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors">
                        <span class="flex items-center gap-3"><i
                                class="ti ti-chart-bar text-[#4cd9fd] text-2xl"></i>Case Studies</span>
                        <i class="ti ti-chevron-down text-[#4cd9fd] transition-transform duration-300 text-2xl"
                            :class="mobileAcc === 'cases' ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="mobileAcc==='cases'" x-collapse class="overflow-hidden" style="display:none;">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10 mt-2">
                            <span class="text-[10px] font-bold text-[#b1ecff] uppercase tracking-widest">Case
                                Studies</span>
                            <a class="text-[10px] font-bold text-white/60 hover:text-[#b1ecff] flex items-center gap-1"
                                href="{{ route('site.case-studies') }}" wire:navigate
                                @click="mobileOpen = false">View All <i class="ti ti-arrow-right text-xs"></i></a>
                        </div>
                        <div class="space-y-3 mb-6">
                            @foreach ([['heart-rate-monitor', 'Healthcare Transformation', 'Optimizing clinical workflows in Accra'], ['world', 'Global Supply Chain', 'End-to-end visibility for intercontinental trade'], ['building-church', 'Faith Community Ops', 'Scaling religious institutions across West Africa']] as [$icon, $title, $sub])
                                <a href="{{ route('site.case-studies') }}" wire:navigate @click="mobileOpen = false"
                                    class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-[#4cd9fd]/10 border border-white/5 hover:border-[#4cd9fd]/30 transition-all">
                                    <i class="ti ti-{{ $icon }} text-[#4cd9fd] text-xl mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $title }}</p>
                                        <p class="text-[10px] text-[#7689a4] mt-0.5">{{ $sub }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </li>

                <li><a href="{{ route('site.blog') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors {{ request()->routeIs('site.blog*') ? 'text-[#00b8db]' : '' }}"><i
                            class="ti ti-article text-[#4cd9fd] text-2xl"></i>Insights</a></li>
                <li><a href="{{ route('site.contact') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-['Syne'] text-2xl font-bold hover:text-[#b1ecff] transition-colors"><i
                            class="ti ti-mail text-[#4cd9fd] text-2xl"></i>Contact</a></li>
            </ul>

            <div class="mt-8 space-y-4">
                <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileOpen = false"
                    class="block w-full bg-[#4cd9fd] text-[#000917] font-bold py-4 rounded-full text-base text-center shadow-lg shadow-[#4cd9fd]/20 hover:bg-[#48d7fb] transition-colors">Talk
                    to Us</a>
                @auth
                    <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileOpen = false"
                        class="block w-full text-center py-4 text-sm text-white/60 border border-white/15 rounded-full hover:text-white hover:border-white/30 transition-colors">My
                        Account</a>
                @else
                    <a href="{{ route('customer.login') }}" wire:navigate @click="mobileOpen = false"
                        class="block w-full text-center py-4 text-sm text-white/60 border border-white/15 rounded-full hover:text-white hover:border-white/30 transition-colors">Sign
                        In</a>
                @endauth
                <div class="flex items-center justify-center gap-4 pt-2">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#4cd9fd] hover:text-[#000917] transition-all text-white/60"><i
                            class="ti ti-world text-base"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#4cd9fd] hover:text-[#000917] transition-all text-white/60"><i
                            class="ti ti-at text-base"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#4cd9fd] hover:text-[#000917] transition-all text-white/60"><i
                            class="ti ti-message-circle text-base"></i></a>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    function siteNav() {
        return {
            notifOpen: true,
            mobileOpen: false,
            activeMenu: null,
            mobileAcc: null,
            scrolled: false,

            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 20;
                }, {
                    passive: true
                });

                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.activeMenu = null;
                        this.mobileOpen = false;
                    }
                });

                this.$watch('mobileOpen', val => {
                    document.body.style.overflow = val ? 'hidden' : '';
                });
            },

            closeNotif() {
                this.notifOpen = false;
                @this.dismissNotification();
            },

            toggleAcc(name) {
                this.mobileAcc = this.mobileAcc === name ? null : name;
            },
        }
    }
</script>
