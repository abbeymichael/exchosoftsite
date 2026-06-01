{{--
  site-navigation.blade.php
  Livewire 4 component — notification bar + sticky mega-menu header + mobile drawer
  No render() view needed — this IS the view.
--}}

{{-- ── Sticky shell ───────────────────────────────────────────── --}}
<div class="sticky top-0 z-50 flex flex-col" id="sticky-shell" x-data="siteNav()">

  {{-- Notification Bar --}}
  @if($notificationVisible)
  <div
    class="border-b border-cyan/20 transition-all duration-300"
    id="notification-bar"
    style="background:linear-gradient(90deg,#08121d 0%,#0d2137 40%,#0a1e30 60%,#08121d 100%);box-shadow:0 1px 0 rgba(0,184,219,.12),inset 0 1px 0 rgba(0,184,219,.06);"
    x-show="notifOpen"
    x-transition:leave="transition duration-200"
    x-transition:leave-start="opacity-100 max-h-12"
    x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
  >
    <div class="relative flex items-center justify-center py-2 px-10">
      <span class="absolute left-4 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-cyan opacity-70 hidden md:block"
            style="box-shadow:0 0 6px rgba(0,184,219,.8)"></span>
      <p class="text-[12px] text-white/85 tracking-wide text-center">
        <span class="text-cyan font-semibold mr-1">{{ $notificationText }}</span> —
        <a class="text-secondary-fixed hover:text-white underline-offset-2 hover:underline transition-colors"
           href="{{ $notificationLink }}"
           wire:navigate>{{ $notificationLabel }}</a>
      </p>
      <button
        @click="closeNotif()"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full text-white/50 hover:text-white hover:bg-white/10 transition-all"
        aria-label="Dismiss notification"
      >
        <span class="material-symbols-outlined text-base leading-none">close</span>
      </button>
    </div>
  </div>
  @endif

  {{-- Header --}}
  <header
    class="border-b border-white/10 shadow-sm transition-all duration-500"
    id="main-header"
    style="background:rgba(0,9,23,.95);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);"
    :class="scrolled ? 'border-cyan/15' : 'border-white/10'"
  >
    <nav class="flex justify-between items-center w-full px-4 md:px-10 lg:px-16 py-3">

      {{-- ── Brand ──────────────────────────────────────────── --}}
      <div class="flex items-center gap-2">
        @php
          $logoPng = public_path('assets/images/logo cyan.png');
          $hasLogo  = file_exists($logoPng) && filesize($logoPng) > 0;
        @endphp
        @if($hasLogo)
          <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
            <img src="{{ asset('assets/images/logo cyan.png') }}" alt="Exchosoft Consult" class="h-10 w-auto">
          </a>
        @else
          <span class="material-symbols-outlined text-secondary-container text-3xl" style="font-variation-settings:'FILL' 1;">hub</span>
          <a class="font-syne text-2xl font-bold tracking-tight text-white" href="{{ route('home') }}" wire:navigate>
            Exchosoft Consult
          </a>
        @endif
      </div>

      {{-- ── Desktop Nav Links ──────────────────────────────── --}}
      <ul class="hidden md:flex items-center gap-6 lg:gap-8">

        {{-- Home --}}
        <li>
          <a href="{{ route('home') }}" wire:navigate
             class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('home') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            Home
          </a>
        </li>

        {{-- About --}}
        <li>
          <a href="{{ route('site.about') }}" wire:navigate
             class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('site.about') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            About
          </a>
        </li>

        {{-- Products mega menu --}}
        <li class="relative" @mouseenter="openMenu('products')" @mouseleave="closeMenu('products')">
          <button class="mega-trigger relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
            {{ request()->routeIs('site.products*') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            Products <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="activeMenu==='products' ? 'rotate-180' : ''">keyboard_arrow_down</span>
          </button>
          {{-- Mega panel --}}
          <div
            x-show="activeMenu==='products'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-[-8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-8px]"
            class="absolute top-full -left-64 w-[900px] mt-4 bg-primary-container border border-outline-variant/10 rounded-xl shadow-2xl overflow-hidden z-50"
            @mouseenter="openMenu('products')" @mouseleave="closeMenu('products')"
            style="display:none;"
          >
            <div class="flex min-h-[380px]">
              {{-- Left banner --}}
              <div class="w-2/5 relative overflow-hidden group/banner">
                <div class="w-full h-full bg-gradient-to-br from-navy via-navy-mid to-primary-container flex items-end">
                  <div class="absolute inset-0" style="background-image:radial-gradient(#c4c6cd .5px,transparent .5px);background-size:24px 24px;opacity:.06;"></div>
                  <div class="absolute inset-0 bg-gradient-to-l from-primary/80 to-transparent"></div>
                </div>
                <div class="absolute bottom-8 left-8 right-8">
                  <div class="h-px w-12 bg-secondary-container mb-4"></div>
                  <h5 class="text-white font-bold text-lg font-syne">Operational Excellence</h5>
                  <p class="text-secondary-fixed text-sm">Reliability at every scale.</p>
                </div>
              </div>
              {{-- Right items --}}
              <div class="w-3/5 p-8">
                <div class="mb-6 flex items-center justify-between border-b border-white/10 pb-4">
                  <span class="text-xs font-bold text-secondary-fixed uppercase tracking-widest">Solutions Portfolio</span>
                  <a class="text-xs font-bold text-white hover:text-secondary-container transition-colors flex items-center gap-1"
                     href="{{ route('site.products') }}" wire:navigate>
                    Explore All Products <span class="material-symbols-outlined text-xs">arrow_forward</span>
                  </a>
                </div>
                <div class="grid grid-cols-2 gap-x-8 gap-y-5">
                  @if(count($products) > 0)
                    @foreach($products as $prod)
                    <a class="group/item flex items-start gap-3 hover:bg-white/5 p-2 -m-2 rounded-lg transition-colors"
                       href="{{ route('site.products.show', $prod['slug']) }}" wire:navigate>
                      <span class="material-symbols-outlined text-secondary-container text-xl mt-0.5 shrink-0"
                            style="font-variation-settings:'FILL' 1;">deployed_code</span>
                      <div>
                        <p class="text-sm font-bold text-white group-hover/item:text-secondary-fixed transition-colors">{{ $prod['name'] }}</p>
                        @if($prod['tagline'])<p class="text-[11px] text-on-primary-container mt-0.5">{{ Str::limit($prod['tagline'], 45) }}</p>@endif
                      </div>
                    </a>
                    @endforeach
                  @else
                    {{-- Fallback static items --}}
                    @foreach([
                      ['WashOps','local_laundry_service','Laundry management','products'],
                      ['ChurchOps','church','Faith community management','products'],
                      ['ClinicOps','monitor_heart','Healthcare management','products'],
                      ['LabOps','biotech','Laboratory systems','products'],
                    ] as [$n,$icon,$sub,$route])
                    <a class="group/item flex items-start gap-3 hover:bg-white/5 p-2 -m-2 rounded-lg transition-colors"
                       href="{{ route('site.'.$route) }}" wire:navigate>
                      <span class="material-symbols-outlined text-secondary-container text-xl mt-0.5 shrink-0"
                            style="font-variation-settings:'FILL' 1;">{{ $icon }}</span>
                      <div>
                        <p class="text-sm font-bold text-white group-hover/item:text-secondary-fixed transition-colors">{{ $n }}</p>
                        <p class="text-[11px] text-on-primary-container mt-0.5">{{ $sub }}</p>
                      </div>
                    </a>
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
          </div>
        </li>

        {{-- Services mega menu --}}
        <li class="relative" @mouseenter="openMenu('services')" @mouseleave="closeMenu('services')">
          <button class="mega-trigger relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
            {{ request()->routeIs('site.services') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            Services <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="activeMenu==='services' ? 'rotate-180' : ''">keyboard_arrow_down</span>
          </button>
          <div
            x-show="activeMenu==='services'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-[-8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-8px]"
            class="absolute top-full -left-32 w-[620px] mt-4 bg-primary-container border border-outline-variant/10 rounded-xl shadow-2xl overflow-hidden z-50"
            @mouseenter="openMenu('services')" @mouseleave="closeMenu('services')"
            style="display:none;"
          >
            <div class="p-8">
              <div class="mb-6 flex items-center justify-between border-b border-white/10 pb-4">
                <span class="text-xs font-bold text-secondary-fixed uppercase tracking-widest">Our Expertise</span>
                <a class="text-xs font-bold text-white hover:text-secondary-container transition-colors flex items-center gap-1"
                   href="{{ route('site.services') }}" wire:navigate>
                  All Services <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </a>
              </div>
              <div class="grid grid-cols-2 gap-x-8 gap-y-5">
                @foreach([
                  ['code','Custom Development','Built for your exact workflows'],
                  ['psychology','Technology Consulting','Strategic guidance, real-world lens'],
                  ['architecture','System Architecture','Offline-first, cloud-ready designs'],
                  ['transform','Digital Transformation','Modernization on your terms'],
                  ['analytics','Business Process Analysis','Identify bottlenecks, unlock growth'],
                  ['support_agent','Ongoing Support','We stay involved as you grow'],
                ] as [$icon, $title, $sub])
                <a class="group/item flex items-start gap-3 hover:bg-white/5 p-2 -m-2 rounded-lg transition-colors"
                   href="{{ route('site.services') }}" wire:navigate>
                  <span class="material-symbols-outlined text-secondary-container text-xl mt-0.5 shrink-0"
                        style="font-variation-settings:'FILL' 1;">{{ $icon }}</span>
                  <div>
                    <p class="text-sm font-bold text-white group-hover/item:text-secondary-fixed transition-colors">{{ $title }}</p>
                    <p class="text-[11px] text-on-primary-container mt-0.5">{{ $sub }}</p>
                  </div>
                </a>
                @endforeach
              </div>
            </div>
          </div>
        </li>

        {{-- Case Studies mega menu --}}
        <li class="relative" @mouseenter="openMenu('cases')" @mouseleave="closeMenu('cases')">
          <button class="mega-trigger relative flex items-center gap-1 text-sm font-medium tracking-widest uppercase transition-colors
            {{ request()->routeIs('site.case-studies*') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            Case Studies <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="activeMenu==='cases' ? 'rotate-180' : ''">keyboard_arrow_down</span>
          </button>
          <div
            x-show="activeMenu==='cases'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-[-8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-8px]"
            class="absolute top-full -left-48 w-[480px] mt-4 bg-primary-container border border-outline-variant/10 rounded-xl shadow-2xl overflow-hidden z-50"
            @mouseenter="openMenu('cases')" @mouseleave="closeMenu('cases')"
            style="display:none;"
          >
            <div class="p-8">
              <div class="mb-5 flex items-center justify-between border-b border-white/10 pb-4">
                <span class="text-[10px] font-bold text-secondary-fixed uppercase tracking-widest">Impact Stories</span>
                <a class="text-[10px] font-bold text-white/60 hover:text-secondary-fixed flex items-center gap-1"
                   href="{{ route('site.case-studies') }}" wire:navigate>
                  View All <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </a>
              </div>
              <div class="space-y-3">
                @foreach([
                  ['monitor_heart','Healthcare Transformation','Optimizing clinical workflows in Accra.'],
                  ['language','Global Supply Chain','End-to-end visibility for intercontinental trade.'],
                  ['church','Faith Community Ops','Scaling religious institutions across West Africa.'],
                ] as [$icon,$title,$sub])
                <a class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-secondary-container/10 border border-white/5 hover:border-secondary-container/30 transition-all"
                   href="{{ route('site.case-studies') }}" wire:navigate>
                  <span class="material-symbols-outlined text-secondary-container text-xl mt-0.5">{{ $icon }}</span>
                  <div>
                    <p class="text-sm font-bold text-white">{{ $title }}</p>
                    <p class="text-[10px] text-on-primary-container mt-0.5">{{ $sub }}</p>
                  </div>
                </a>
                @endforeach
              </div>
            </div>
          </div>
        </li>

        {{-- Insights --}}
        <li>
          <a href="{{ route('site.blog') }}" wire:navigate
             class="text-sm font-medium tracking-widest uppercase transition-colors {{ request()->routeIs('site.blog*') ? 'text-cyan' : 'text-white/70 hover:text-secondary-fixed' }}">
            Insights
          </a>
        </li>

      </ul>

      {{-- ── Right: CTA + Auth + Mobile hamburger ──────────────── --}}
      <div class="flex items-center gap-3">

        @auth
          <div class="hidden md:block relative" x-data="{ uOpen: false }">
            <button @click="uOpen=!uOpen"
                    class="flex items-center gap-2 bg-white/8 border border-white/12 px-3 py-1.5 rounded-lg text-white/75 text-sm font-syne font-medium hover:bg-white/12 transition-colors">
              <span class="w-5 h-5 rounded-full bg-cyan flex items-center justify-center text-[10px] font-extrabold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
              </span>
              {{ Str::words(auth()->user()->name, 1, '') }}
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="uOpen" @click.away="uOpen=false"
                 class="absolute right-0 top-full mt-2 bg-white rounded-xl border border-cyan/15 shadow-xl min-w-[180px] overflow-hidden z-50">
              <a href="{{ route('customer.dashboard') }}" wire:navigate @click="uOpen=false"
                 class="block px-4 py-2.5 text-sm text-navy hover:bg-ice transition-colors">My Account</a>
              <a href="{{ route('customer.orders') }}" wire:navigate @click="uOpen=false"
                 class="block px-4 py-2.5 text-sm text-navy hover:bg-ice transition-colors">My Orders</a>
              <a href="{{ route('customer.licenses') }}" wire:navigate @click="uOpen=false"
                 class="block px-4 py-2.5 text-sm text-navy hover:bg-ice transition-colors">My Licenses</a>
              <div class="h-px bg-cyan/10 my-1"></div>
              <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">Sign Out</button>
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
           class="hidden md:inline-flex items-center gap-2 bg-secondary-container text-primary font-bold text-sm px-5 py-2.5 rounded-full hover:bg-secondary-fixed-dim transition-colors">
          Talk to Us
        </a>

        {{-- Mobile hamburger --}}
        <button @click="mobileOpen=!mobileOpen"
                class="md:hidden w-9 h-9 flex items-center justify-center rounded-full text-white hover:bg-white/10 transition-colors"
                aria-label="Open menu">
          <span class="material-symbols-outlined" x-text="mobileOpen ? 'close' : 'menu'">menu</span>
        </button>
      </div>

    </nav>
  </header>

  {{-- ── Mobile drawer overlay ──────────────────────────────── --}}
  <div
    x-show="mobileOpen"
    @click="mobileOpen=false"
    x-transition:enter="transition duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 z-40 md:hidden"
    style="display:none;"
  ></div>

  {{-- ── Mobile drawer panel ────────────────────────────────── --}}
  <div
    x-show="mobileOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 h-full w-[85vw] max-w-sm z-50 overflow-y-auto md:hidden"
    style="background:rgba(0,9,23,.98);backdrop-filter:blur(20px);display:none;"
  >
    {{-- Header --}}
    <div class="flex items-center justify-between p-6 border-b border-white/10">
      <span class="font-syne text-lg font-bold text-white">Exchosoft Consult</span>
      <button @click="mobileOpen=false"
              class="w-8 h-8 flex items-center justify-center rounded-full text-white/70 hover:text-white hover:bg-white/10 transition-all">
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>

    {{-- Nav items --}}
    <ul class="p-6 space-y-1">
      <li>
        <a href="{{ route('home') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">home</span>Home
        </a>
      </li>
      <li>
        <a href="{{ route('site.about') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">info</span>About
        </a>
      </li>
      <li>
        <button @click="toggleAcc('products')"
                class="w-full flex items-center justify-between py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">deployed_code</span>Products
          </span>
          <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="mobileAcc==='products' ? 'rotate-180' : ''">expand_more</span>
        </button>
        <div x-show="mobileAcc==='products'" class="pl-10 pb-4 space-y-3" style="display:none;">
          @if(count($products) > 0)
            @foreach($products as $prod)
            <a href="{{ route('site.products.show', $prod['slug']) }}" wire:navigate @click="mobileOpen=false"
               class="block py-2 text-sm text-on-primary-container hover:text-white transition-colors">{{ $prod['name'] }}</a>
            @endforeach
          @else
            @foreach(['WashOps','ChurchOps','ClinicOps','LabOps'] as $p)
            <a href="{{ route('site.products') }}" wire:navigate @click="mobileOpen=false"
               class="block py-2 text-sm text-on-primary-container hover:text-white transition-colors">{{ $p }}</a>
            @endforeach
          @endif
          <a href="{{ route('site.products') }}" wire:navigate @click="mobileOpen=false"
             class="block py-2 text-sm text-secondary-fixed font-semibold hover:text-white transition-colors">View All Products →</a>
        </div>
      </li>
      <li>
        <a href="{{ route('site.services') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">build</span>Services
        </a>
      </li>
      <li>
        <a href="{{ route('site.case-studies') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">bar_chart_4_bars</span>Case Studies
        </a>
      </li>
      <li>
        <a href="{{ route('site.blog') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">article</span>Insights
        </a>
      </li>
      <li>
        <a href="{{ route('site.contact') }}" wire:navigate @click="mobileOpen=false"
           class="flex items-center gap-3 py-4 border-b border-white/8 text-white font-syne text-lg hover:text-secondary-fixed transition-colors">
          <span class="material-symbols-outlined text-secondary-container text-xl" style="font-variation-settings:'FILL' 1;">mail</span>Contact
        </a>
      </li>
    </ul>

    {{-- Mobile bottom actions --}}
    <div class="mt-4 px-6 space-y-4 pb-10">
      <a href="{{ route('site.consulting') }}" wire:navigate @click="mobileOpen=false"
         class="block w-full bg-secondary-container text-primary font-bold py-4 rounded-full text-base text-center shadow-lg hover:bg-secondary-fixed-dim transition-colors">
        Talk to Us
      </a>
      @auth
      <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileOpen=false"
         class="block w-full text-center py-3 text-sm text-white/60 border border-white/15 rounded-full hover:text-white hover:border-white/30 transition-colors">
        My Account
      </a>
      @else
      <a href="{{ route('customer.login') }}" wire:navigate @click="mobileOpen=false"
         class="block w-full text-center py-3 text-sm text-white/60 border border-white/15 rounded-full hover:text-white hover:border-white/30 transition-colors">
        Sign In
      </a>
      @endauth
    </div>
  </div>

</div>

{{-- Alpine.js controller for this nav --}}
<script>
function siteNav() {
  return {
    notifOpen:  true,
    mobileOpen: false,
    activeMenu: null,
    mobileAcc:  null,
    scrolled:   false,
    _timers:    {},

    init() {
      window.addEventListener('scroll', () => {
        this.scrolled = window.scrollY > 20;
      }, { passive: true });
    },

    closeNotif() {
      this.notifOpen = false;
      @this.dismissNotification();
    },

    openMenu(name) {
      clearTimeout(this._timers[name]);
      this.activeMenu = name;
    },
    closeMenu(name) {
      this._timers[name] = setTimeout(() => {
        if (this.activeMenu === name) this.activeMenu = null;
      }, 120);
    },

    toggleAcc(name) {
      this.mobileAcc = this.mobileAcc === name ? null : name;
    },
  }
}
</script>
