<?php
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;

new #[Layout('layouts.site')] #[Title('Product')] class extends Component
{
    public Product $product;
    public int $activeGallery = 0;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->with(['caseStudies', 'whitepapers'])
            ->firstOrFail();
    }

    public function setGallery(int $i): void { $this->activeGallery = $i; }
};
?>

<div x-data="{ scrolled: false }"
     x-init="
       window.addEventListener('scroll', () => scrolled = window.scrollY > 80);
       document.querySelectorAll('[data-fade]').forEach(el => {
         new IntersectionObserver(([e]) => {
           if (e.isIntersecting) el.classList.add('animate-[fadeUp_.6s_ease_both]');
         }, { threshold: 0.1 }).observe(el);
       });
     "
     class="font-sans bg-background text-on-surface overflow-x-hidden">

{{-- ══════════════════════════════════════════════
     HERO
══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-primary
                grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20
                items-center px-6 md:px-16 pt-24 pb-20">

    <div class="hero-bg-pattern"></div>
    <div class="hero-grid-lines opacity-60"></div>
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-cyan/30 to-transparent"></div>

    {{-- LEFT: Text --}}
    <div class="relative z-10" data-fade>

        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-6
                    bg-secondary/10 border border-secondary/25 text-secondary-container text-label-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
            {{ $this->product->is_featured ? 'Featured Product' : ucfirst($this->product->category ?? 'Enterprise Software') }}
        </div>

        @if($this->product->platform)
        <p class="text-label-sm text-on-primary/40 mb-2 font-display tracking-[.2em] uppercase">
            {{ $this->product->platform }} · v{{ $this->product->current_version ?? '1.0' }}
        </p>
        @endif

        @php $words = explode(' ', $this->product->name); @endphp
        <h1 class="font-display text-display-md text-on-primary leading-[.92] tracking-tight mb-5">
            @if(count($words) > 1)
                {{ implode(' ', array_slice($words, 0, -1)) }}<br>
                <span class="text-secondary-container">{{ last($words) }}</span>
            @else
                <span class="text-secondary-container">{{ $this->product->name }}</span>
            @endif
        </h1>

        @if($this->product->tagline)
        <p class="text-body-lg text-on-primary/60 max-w-lg mb-8">{{ $this->product->tagline }}</p>
        @endif

        <div class="flex flex-wrap gap-3 mb-8">
            <a href="{{ route('site.consulting') }}" wire:navigate
               class="inline-flex items-center gap-2 bg-secondary text-on-secondary font-display
                      font-semibold text-body-md px-7 py-3.5 rounded-full
                      hover:-translate-y-1 hover:shadow-cyan transition-all">
                <span class="material-symbols-outlined text-lg">verified</span>
                Secure Your License
            </a>
            <a href="#pd-content"
               class="inline-flex items-center gap-2 border border-on-primary/15 text-on-primary/60
                      font-display font-medium text-body-md px-6 py-3.5 rounded-full
                      hover:border-secondary hover:text-secondary-container hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined text-lg">arrow_downward</span> Learn More
            </a>
            @if($this->product->demo_url)
            <a href="{{ $this->product->demo_url }}" target="_blank"
               class="inline-flex items-center gap-2 border border-secondary/25 text-secondary-container
                      font-display text-body-md px-5 py-3.5 rounded-full
                      hover:bg-secondary/10 hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined text-lg">play_circle</span> Live Demo
            </a>
            @endif
        </div>

        <div class="flex flex-wrap gap-5 pt-5 border-t border-on-primary/[.07]">
            @foreach([['verified_user','SOC-2 Ready'],['wifi_off','Offline-First'],['security','AES-256'],['support_agent','24/7 Support']] as [$ico,$lbl])
            <span class="inline-flex items-center gap-1.5 text-label-sm text-on-primary/35">
                <span class="material-symbols-outlined text-secondary-container text-base">{{ $ico }}</span>{{ $lbl }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Gallery --}}
    <div class="relative z-10" x-data="{ active: @entangle('activeGallery') }">
        @php
            $gallery = collect($this->product->gallery ?? []);
            if ($this->product->cover_image) $gallery->prepend($this->product->cover_image);
            $gallery = $gallery->unique()->values();
        @endphp

        <div class="relative rounded-2xl overflow-hidden border border-secondary/15 bg-primary-container"
             style="aspect-ratio:4/3;box-shadow:0 30px 80px rgba(0,9,23,.7)">
            <div class="flex items-center gap-1.5 px-4 py-2.5 border-b border-on-primary/[.08] bg-navy-deepest">
                <span class="w-2.5 h-2.5 rounded-full bg-error/45"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-tertiary-container/45"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-secondary/45"></span>
                <span class="ml-2 text-label-sm text-secondary/30 font-display">
                    {{ $this->product->app_identifier ?? $this->product->slug }}.app
                </span>
            </div>
            @if($gallery->count())
                @foreach($gallery as $i => $img)
                <img src="{{ asset(Storage::url($img)) }}"
                     alt="{{ $this->product->name }} screenshot {{ $i+1 }}"
                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                     style="padding-top:38px"
                     :style="active === {{ $i }} ? 'opacity:1' : 'opacity:0'">
                @endforeach
            @else
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-3" style="padding-top:38px">
                    <span class="material-symbols-outlined text-on-primary/10" style="font-size:3.5rem">deployed_code</span>
                    <span class="text-label-sm text-on-primary/25 font-display">{{ $this->product->name }}</span>
                </div>
            @endif
            <div class="absolute bottom-3 right-3 flex items-center gap-1.5 px-2.5 py-1.5
                        rounded-lg border border-secondary/20 backdrop-blur-md text-label-sm text-secondary-container font-display"
                 style="background:rgba(0,9,23,.7)">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
                Live Preview
            </div>
        </div>

        @if($gallery->count() > 1)
        <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
            @foreach($gallery as $i => $img)
            <img src="{{ asset(Storage::url($img)) }}" alt="Thumb {{ $i+1 }}"
                 class="w-14 h-10 rounded-lg object-cover cursor-pointer shrink-0 border-2 transition-all hover:scale-105"
                 :class="active === {{ $i }} ? 'border-secondary-container opacity-100' : 'border-transparent opacity-50'"
                 @click="active = {{ $i }}; $wire.setGallery({{ $i }})">
            @endforeach
        </div>
        @endif
    </div>
</section>


{{-- ══════════════════════════════════════════════
     THREE-COLUMN BODY
     Left: on-page nav  |  Centre: content  |  Right: buy card
══════════════════════════════════════════════ --}}
<div id="pd-content" class="max-w-[1440px] mx-auto px-6 md:px-16 py-16">
    <div class="flex gap-10 items-start">

        {{-- ── LEFT NAV SIDEBAR ──────────────────── --}}
        <aside class="hidden xl:block w-44 shrink-0 self-start sticky top-[72px]">
            <p class="text-label-sm text-on-surface/35 uppercase tracking-widest font-display mb-3 px-2">
                On this page
            </p>
            <nav class="flex flex-col">
                @php
                    $navItems = [
                        ['pd-overview',  'Overview'],
                        ['pd-resources', 'Case Studies'],
                    ];
                    if($this->product->tech_stack && count($this->product->tech_stack))
                        $navItems[] = ['pd-tech', 'Tech Stack'];
                @endphp
                @foreach($navItems as [$anchor, $label])
                <a href="#{{ $anchor }}"
                   class="text-label-md font-display px-2 py-2 border-l-2 border-transparent
                          text-on-surface/40 hover:text-secondary hover:border-secondary/40
                          transition-all no-underline leading-snug">
                    {{ $label }}
                </a>
                @endforeach
            </nav>

            @if($this->product->documentation_url || $this->product->demo_url || $this->product->download_url)
            <div class="mt-6 pt-5 border-t border-outline-variant/30 flex flex-col gap-0.5">
                @if($this->product->documentation_url)
                <a href="{{ $this->product->documentation_url }}" target="_blank"
                   class="flex items-center gap-2 text-label-sm text-on-surface/40 hover:text-secondary
                          px-2 py-1.5 rounded-lg hover:bg-secondary/5 transition-all no-underline">
                    <span class="material-symbols-outlined text-sm">description</span> Docs
                </a>
                @endif
                @if($this->product->demo_url)
                <a href="{{ $this->product->demo_url }}" target="_blank"
                   class="flex items-center gap-2 text-label-sm text-on-surface/40 hover:text-secondary
                          px-2 py-1.5 rounded-lg hover:bg-secondary/5 transition-all no-underline">
                    <span class="material-symbols-outlined text-sm">play_circle</span> Live demo
                </a>
                @endif
                @if($this->product->download_url)
                <a href="{{ $this->product->download_url }}" target="_blank"
                   class="flex items-center gap-2 text-label-sm text-on-surface/40 hover:text-secondary
                          px-2 py-1.5 rounded-lg hover:bg-secondary/5 transition-all no-underline">
                    <span class="material-symbols-outlined text-sm">download</span> Download trial
                </a>
                @endif
                <a href="{{ route('site.contact') }}" wire:navigate
                   class="flex items-center gap-2 text-label-sm text-on-surface/40 hover:text-secondary
                          px-2 py-1.5 rounded-lg hover:bg-secondary/5 transition-all no-underline">
                    <span class="material-symbols-outlined text-sm">chat</span> Contact sales
                </a>
            </div>
            @endif
        </aside>

        {{-- ── CENTRE: MARKDOWN CONTENT ──────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Overview / full description --}}
            <section id="pd-overview" class="scroll-mt-24 mb-16">
                @if($this->product->description)
                <p class="text-body-lg text-on-surface/70 mb-8 max-w-prose">{{ $this->product->description }}</p>
                @endif

                @if($this->product->full_description)
                <div class="prose prose-lg max-w-none
                            prose-headings:font-display prose-headings:tracking-tight
                            prose-h2:text-headline-xl prose-h2:text-on-surface prose-h2:mt-12 prose-h2:mb-4
                            prose-h3:text-headline-md prose-h3:text-on-surface prose-h3:mt-8 prose-h3:mb-3
                            prose-p:text-on-surface/70 prose-p:leading-relaxed
                            prose-li:text-on-surface/70
                            prose-strong:text-on-surface prose-strong:font-semibold
                            prose-a:text-secondary prose-a:no-underline hover:prose-a:underline
                            prose-code:text-secondary prose-code:bg-secondary/10 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:before:content-none prose-code:after:content-none
                            prose-pre:bg-primary prose-pre:text-on-primary/80 prose-pre:border prose-pre:border-secondary/15 prose-pre:rounded-2xl
                            prose-hr:border-outline-variant/30
                            prose-blockquote:border-secondary prose-blockquote:text-on-surface/60">
                    {!! \Illuminate\Support\Str::markdown($this->product->full_description) !!}
                </div>
                @endif
            </section>

            {{-- Case studies & whitepapers --}}
            @php
                $cases  = $this->product->caseStudies()->latest()->take(4)->get();
                $papers = $this->product->whitepapers()->latest()->take(4)->get();
            @endphp

            @if($cases->count() || $papers->count())
            <section id="pd-resources" class="scroll-mt-24 mb-16">
                <hr class="border-outline-variant/30 mb-12">

                @if($cases->count())
                <div class="{{ $papers->count() ? 'mb-12' : '' }}">
                    <p class="text-label-sm text-secondary mb-2 uppercase tracking-widest font-display">Real-World Results</p>
                    <h2 class="font-display text-headline-xl text-on-surface tracking-tight mb-8">Case Studies</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($cases as $case)
                        <a href="{{ route('site.case-studies.show', $case->slug) }}" wire:navigate
                           class="group flex flex-col bg-surface-container border border-outline-variant/30 rounded-2xl
                                  overflow-hidden no-underline hover:border-secondary/50 hover:-translate-y-1 hover:shadow-lg transition-all">
                            <div class="h-36 overflow-hidden bg-surface-container-high flex items-center justify-center shrink-0">
                                @if($case->cover_image ?? $case->image ?? false)
                                    <img src="{{ asset(Storage::url($case->cover_image ?? $case->image)) }}"
                                         alt="{{ $case->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <span class="material-symbols-outlined text-on-surface/10" style="font-size:2.5rem">bar_chart_4_bars</span>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                <p class="text-label-sm text-secondary mb-2 uppercase tracking-wider font-display">Case Study</p>
                                <h3 class="font-display text-headline-sm text-on-surface mb-2 leading-snug
                                           group-hover:text-secondary transition-colors flex-1">{{ $case->title }}</h3>
                                @if($case->summary ?? $case->excerpt ?? false)
                                <p class="text-body-sm text-on-surface/40 mb-3 line-clamp-2">{{ $case->summary ?? $case->excerpt }}</p>
                                @endif
                                <span class="inline-flex items-center gap-1 text-body-sm text-secondary font-display">
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span> Read Full Study
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($papers->count())
                <div>
                    <p class="text-label-sm text-on-tertiary-container mb-2 uppercase tracking-widest font-display">Research & Insights</p>
                    <h2 class="font-display text-headline-xl text-on-surface tracking-tight mb-8">Whitepapers</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($papers as $paper)
                        <a href="{{ $paper->file_url ?? $paper->download_url ?? '#' }}" target="_blank"
                           class="group flex flex-col bg-surface-container border border-outline-variant/30 rounded-2xl
                                  overflow-hidden no-underline hover:border-on-tertiary-container/40
                                  hover:-translate-y-1 hover:shadow-lg transition-all">
                            <div class="h-36 overflow-hidden bg-surface-container-high flex items-center justify-center shrink-0">
                                @if($paper->cover_image ?? false)
                                    <img src="{{ asset(Storage::url($paper->cover_image)) }}" alt="{{ $paper->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <span class="material-symbols-outlined text-on-surface/10" style="font-size:2.5rem">description</span>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                <p class="text-label-sm text-on-tertiary-container mb-2 uppercase tracking-wider font-display">Whitepaper</p>
                                <h3 class="font-display text-headline-sm text-on-surface mb-2 leading-snug
                                           group-hover:text-on-tertiary-container transition-colors flex-1">{{ $paper->title }}</h3>
                                @if($paper->description ?? false)
                                <p class="text-body-sm text-on-surface/40 mb-3 line-clamp-2">{{ $paper->description }}</p>
                                @endif
                                <span class="inline-flex items-center gap-1 text-body-sm text-on-tertiary-container font-display">
                                    <span class="material-symbols-outlined text-sm">download</span> Download PDF
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </section>
            @endif

            {{-- Tech stack --}}
            @if($this->product->tech_stack && count($this->product->tech_stack))
            <section id="pd-tech" class="scroll-mt-24">
                <hr class="border-outline-variant/30 mb-12">
                <p class="text-label-sm text-secondary mb-2 uppercase tracking-widest font-display">Under the Hood</p>
                <h2 class="font-display text-headline-xl text-on-surface tracking-tight mb-6">Tech Stack</h2>
                <div class="flex flex-wrap gap-2.5 mb-8">
                    @foreach($this->product->tech_stack as $tech)
                    <span class="px-3.5 py-1.5 rounded-lg text-body-sm font-display
                                 bg-secondary/10 border border-secondary/20 text-secondary">{{ $tech }}</span>
                    @endforeach
                </div>
                <div class="rounded-2xl overflow-hidden border border-secondary/15 shadow-navy bg-primary">
                    <div class="flex items-center gap-1.5 px-4 py-3 border-b border-on-primary/[.07] bg-navy-deepest">
                        <span class="w-2.5 h-2.5 rounded-full bg-error/45"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-tertiary-container/45"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary/45"></span>
                        <span class="ml-2.5 text-label-sm text-secondary/30 font-display">system_diagnostics.sh</span>
                    </div>
                    <div class="p-6 space-y-1.5 text-body-sm font-display" style="line-height:1.9">
                        <div class="text-on-primary/35">[INIT] Verifying local SQL ledger...</div>
                        <div><span class="text-sky">[OK]</span> <span class="text-on-primary/55">Integrity check passed (hash: d8a1)</span></div>
                        <div class="text-on-primary/35">[SYNC] Establishing P2P handshake...</div>
                        <div><span class="text-secondary-container">[OK]</span> <span class="text-on-primary/55">7 nodes discovered on LAN</span></div>
                        <div class="text-on-primary/35">[AUTH] Validating session token...</div>
                        <div><span class="text-sky">[ACCESS]</span> <span class="text-on-primary/55">Level: SYS_ADMIN</span></div>
                        <div><span class="text-secondary-container">[READY]</span> <span class="text-on-primary/55">Monitoring {{ $this->product->max_devices ?? 'N' }} units</span></div>
                        <div class="text-secondary-container flex items-center gap-1">$
                            <span class="inline-block w-1.5 h-3.5 bg-secondary-container animate-pulse"></span>
                        </div>
                    </div>
                </div>
            </section>
            @endif

        </div>{{-- /centre --}}

        {{-- ── RIGHT: BUY CARD ───────────────────── --}}
        <aside class="hidden lg:block w-64 shrink-0 self-start sticky top-[72px]">

            {{-- Price --}}
            <div class="rounded-2xl bg-surface-container border border-outline-variant/40 p-6 mb-4">

                @if($this->product->is_on_sale)
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full mb-4
                            bg-tertiary-container/10 border border-tertiary-container/25
                            text-on-tertiary-container text-label-sm font-bold font-display">
                    🔥 {{ $this->product->discount_percent }}% off — <span id="countdown" class="ml-1">soon</span>
                </div>
                @endif

                <div class="mb-1">
                    @if($this->product->pricing_type === 'free')
                        <span class="font-display text-display-sm text-on-surface">Free</span>
                    @else
                        <span class="font-display text-display-sm text-on-surface">
                            {{ $this->product->currency }} {{ number_format($this->product->effective_price, 2) }}
                        </span>
                        @if($this->product->is_on_sale)
                        <span class="font-display text-headline-md text-on-surface/30 line-through ml-2">
                            {{ number_format($this->product->price, 2) }}
                        </span>
                        @endif
                    @endif
                </div>
                <p class="text-body-sm text-on-surface/40 mb-5">
                    @if($this->product->pricing_type === 'subscription') Per year, billed annually
                    @elseif($this->product->pricing_type === 'free') Free forever
                    @else One-time · perpetual license @endif
                </p>

                <a href="{{ route('site.consulting') }}" wire:navigate
                   class="flex items-center justify-center gap-2 w-full px-5 py-3.5 rounded-xl mb-2
                          bg-secondary text-on-secondary font-display font-semibold text-body-md
                          hover:-translate-y-0.5 hover:shadow-cyan transition-all no-underline">
                    <span class="material-symbols-outlined text-lg">verified</span> Secure License
                </a>
                <a href="{{ route('site.consulting') }}" wire:navigate
                   class="flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl
                          border border-outline-variant/40 text-on-surface/60 font-display text-body-sm
                          hover:border-secondary hover:text-secondary transition-all no-underline">
                    <span class="material-symbols-outlined text-base">event</span> Book a Demo
                </a>

                @if($this->product->support_email)
                <div class="mt-4 pt-4 border-t border-outline-variant/30 text-center">
                    <a href="mailto:{{ $this->product->support_email }}"
                       class="inline-flex items-center gap-1.5 text-label-sm text-on-surface/40
                              hover:text-secondary transition-colors no-underline">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        {{ $this->product->support_email }}
                    </a>
                </div>
                @endif
            </div>

            {{-- Technical specs --}}
            <div class="rounded-2xl bg-surface-container border border-outline-variant/40 p-5 mb-4">
                <p class="text-label-sm text-on-surface-variant mb-3 uppercase tracking-widest font-display">Details</p>
                <dl class="space-y-0">
                    @foreach([
                        ['Version',  $this->product->current_version ? 'v'.$this->product->current_version : null, true],
                        ['Platform', $this->product->platform, false],
                        ['Billing',  $this->product->pricing_type ? ucwords(str_replace('_',' ',$this->product->pricing_type)) : null, false],
                        ['Devices',  $this->product->max_devices ? $this->product->max_devices.' max' : null, false],
                        ['Offline',  $this->product->offline_ttl_hours ? $this->product->offline_ttl_hours.'h TTL' : null, false],
                        ['License',  $this->product->default_duration_days !== null ? ($this->product->default_duration_days == 0 ? 'Perpetual' : $this->product->default_duration_days.'d') : null, false],
                    ] as [$k, $v, $highlight])
                        @if(!$v) @continue @endif
                        <div class="flex justify-between items-center py-2 border-b border-outline-variant/20 last:border-0">
                            <dt class="text-body-sm text-on-surface/50">{{ $k }}</dt>
                            <dd class="text-body-sm {{ $highlight ? 'text-secondary font-semibold' : 'text-on-surface' }} font-display">{{ $v }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Trust items --}}
            <div class="rounded-2xl bg-surface-container border border-outline-variant/40 p-5">
                @foreach([
                    ['verified_user', 'SOC-2 Ready'],
                    ['wifi_off',      'Offline-first'],
                    ['security',      'AES-256 encrypted'],
                    ['support_agent', '24/7 support'],
                    ['refresh',       '30-day refund'],
                ] as [$ico, $lbl])
                <div class="flex items-center gap-2.5 py-2 border-b border-outline-variant/20 last:border-0">
                    <span class="material-symbols-outlined text-secondary text-base shrink-0">{{ $ico }}</span>
                    <span class="text-body-sm text-on-surface/60">{{ $lbl }}</span>
                </div>
                @endforeach
                <div class="mt-4">
                    <a href="{{ route('site.contact') }}" wire:navigate
                       class="flex items-center justify-center gap-1.5 w-full text-body-sm text-on-surface/40
                              hover:text-secondary transition-colors no-underline py-1">
                        Volume / custom deal →
                    </a>
                </div>
            </div>

        </aside>

    </div>
</div>


{{-- ══════════════════════════════════════════════
     RELATED PRODUCTS
══════════════════════════════════════════════ --}}
@php
    $related = \App\Models\Product::published()
        ->where('id', '!=', $this->product->id)
        ->when($this->product->category, fn($q) => $q->inCategory($this->product->category))
        ->take(3)->get();
@endphp
@if($related->count())
<div class="border-t border-outline-variant/30 bg-surface-container py-16 px-6 md:px-16">
    <div class="max-w-[1440px] mx-auto">
        <p class="text-label-sm text-secondary mb-1 uppercase tracking-widest font-display">More Products</p>
        <h2 class="font-display text-headline-lg text-on-surface tracking-tight mb-8">Related Solutions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($related as $rel)
            <a href="{{ route('site.products.show', $rel->slug) }}" wire:navigate
               class="group bg-surface border border-outline-variant/30 rounded-2xl overflow-hidden
                      no-underline hover:border-secondary/50 hover:-translate-y-1 hover:shadow-lg transition-all">
                <div class="h-32 overflow-hidden bg-surface-container flex items-center justify-center">
                    @if($rel->cover_image)
                        <img src="{{ asset(Storage::url($rel->cover_image)) }}" alt="{{ $rel->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @elseif($rel->logo)
                        <img src="{{ asset(Storage::url($rel->logo)) }}" alt="{{ $rel->name }}"
                             class="w-12 h-12 object-contain opacity-40 group-hover:opacity-70 transition-opacity">
                    @else
                        <span class="material-symbols-outlined text-on-surface/10" style="font-size:2.5rem">deployed_code</span>
                    @endif
                </div>
                <div class="p-5">
                    <h3 class="font-display text-headline-sm text-on-surface mb-1 group-hover:text-secondary transition-colors">{{ $rel->name }}</h3>
                    @if($rel->tagline)
                    <p class="text-body-sm text-on-surface/40 mb-3 line-clamp-2">{{ $rel->tagline }}</p>
                    @endif
                    <span class="inline-flex items-center gap-1 text-body-sm text-secondary">
                        <span class="material-symbols-outlined text-sm">arrow_forward</span> View product
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif


{{-- ══════════════════════════════════════════════
     FINAL CTA
══════════════════════════════════════════════ --}}
<section class="banner-glow relative overflow-hidden text-center py-24 px-6 md:px-16 bg-primary"
         style="--gx:50%;--gx2:20%">
    <div class="hero-bg-pattern opacity-60"></div>
    <div class="hero-grid-lines opacity-30"></div>
    <div class="relative z-10">
        <p class="text-label-sm text-secondary-container mb-4 uppercase tracking-widest font-display">Ready to Deploy?</p>
        <h2 class="font-display text-display-md text-on-primary tracking-tight mb-4">
            Deploy {{ $this->product->name }}<br>
            <span class="text-secondary-container">in Minutes</span>
        </h2>
        <p class="text-body-lg text-on-primary/60 max-w-md mx-auto mb-10">
            License in under 5 minutes. Offline-first from day one. Full support included.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('site.consulting') }}" wire:navigate
               class="inline-flex items-center gap-2 bg-secondary text-on-secondary
                      font-display font-semibold text-body-md px-9 py-4 rounded-full
                      hover:-translate-y-1 hover:shadow-cyan transition-all">
                <span class="material-symbols-outlined">bolt</span> Buy License Now
            </a>
            <a href="{{ route('site.contact') }}" wire:navigate
               class="inline-flex items-center gap-2 border border-on-primary/15 text-on-primary/70
                      font-display text-body-md px-8 py-4 rounded-full
                      hover:border-secondary-container hover:text-secondary-container hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined">chat</span> Talk to Us
            </a>
        </div>
    </div>
</section>


<script>
(function () {
    var s = 2 * 86400 + 14 * 3600 + 32 * 60;
    setInterval(function () {
        if (s <= 0) return;
        var d = Math.floor(s / 86400), h = Math.floor((s % 86400) / 3600), m = Math.floor((s % 3600) / 60);
        var el = document.getElementById('countdown');
        if (el) el.textContent = d + 'd ' + h + 'h ' + m + 'm';
        s--;
    }, 1000);
})();
</script>

</div>
