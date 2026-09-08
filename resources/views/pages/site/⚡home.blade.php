<?php

use App\Livewire\Concerns\LoadsPageSeo;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component {
    use LoadsPageSeo;

    public array $cms = [];
    public mixed $featuredProducts = null;
    public mixed $latestPosts = null;
    public mixed $featuredCases = null;
    public mixed $featuredWork = null;
    public mixed $services = null;

    public function mount(): void
    {
        $this->loadPageSeo('home', 'Exchosoft Consult — Software Development & Technology Consultancy', "We're a software development and consultancy firm serving Black businesses across Africa, the Caribbean, and the diaspora.");

        $this->featuredProducts = Product::published()->featured()->orderBy('sort_order')->limit(4)->get();
        $this->latestPosts = BlogPost::published()->latest('published_at')->limit(3)->get();
        $this->featuredCases = CaseStudy::published()->featured()->limit(3)->get();
        $this->featuredWork = PortfolioItem::published()->featured()->orderBy('sort_order')->limit(4)->get();
        $this->services = Service::orderBy('created_at')->limit(6)->get();

        $s = SiteSetting::getGroup('homepage');
        $j = fn($key, $default = []) => isset($s[$key]) ? (is_string($s[$key]) ? json_decode($s[$key], true) ?? $default : $s[$key]) : $default;

        $this->cms = [
            'hero_tag' => $this->pageSeo?->banner_subheading ?? ($s['home_hero_tag'] ?? 'Ghana-Based · Africa · Caribbean · Diaspora'),
            'hero_title_raw' => $this->pageSeo?->banner_heading ?? ($s['home_hero_title'] ?? 'Custom Software for Businesses Operating in **the Real World**'),
            'hero_subtitle' => $s['home_hero_subtitle'] ?? 'We design and build production software — APIs, enterprise systems, integrations, and offline-resilient architecture when your business needs it. Built in Ghana for organizations across healthcare, insurance, finance, and beyond.',
            'hero_btn_primary' => $this->pageSeo?->banner_cta_text ?? ($s['home_hero_btn_primary_label'] ?? 'Talk to Us'),
            'hero_btn_secondary' => $s['home_hero_btn_secondary_label'] ?? 'Our Expertise',
            'stats' => $j('home_stats', [['num' => '10+', 'label' => 'Industries served'], ['num' => '3', 'label' => 'Continents reached'], ['num' => '100%', 'label' => 'Custom solutions'], ['num' => 'Offline', 'label' => 'First architecture']]),
            'about_tag' => $s['home_about_tag'] ?? 'The Idea',
            'about_title' => $s['home_about_title'] ?? 'Built for What Your Business Actually Needs',
            'about_content' => $s['home_about_content'] ?? '',
            'about_cards' => $j('home_about_cards', [['title' => 'Intermittent connectivity', 'body' => 'We build systems that keep working when the internet drops.'], ['title' => 'Power challenges', 'body' => 'Offline-first architecture means no data is lost during outages.'], ['title' => 'Mobile-first users', 'body' => 'Designed from the ground up for how your customers actually access technology.'], ['title' => 'Local payment systems', 'body' => 'Integrated with the payment infrastructure your market already uses.']]),
            'products_tag' => $s['home_products_tag'] ?? 'Our Software',
            'products_title' => $s['home_products_title'] ?? 'Products Built for African Businesses',
            'industries_tag' => $s['home_industries_tag'] ?? 'Experience',
            'industries_title' => $s['home_industries_title'] ?? "Industries We've Served",
            'industries_cards' => $j('home_industries_cards', []),
            'why_tag' => $s['home_why_tag'] ?? 'Our Approach',
            'why_title' => $s['home_why_title'] ?? 'Range, Applied Deliberately',
            'why_items' => $j('home_why_items', []),
            'trust_tag' => $s['home_trust_tag'] ?? 'Trusted By',
            'trust_title' => $s['home_trust_title'] ?? 'Organisations That Trust Exchosoft',
            'trust_subtitle' => $s['home_trust_subtitle'] ?? '',
            'trust_clients' => $j('home_trust_clients', ['Ghana Union Assurance', 'Revna Biosciences', 'ADS Central Services', 'Black History Walks', 'African Odysseys']),
            'founder_tag' => $s['home_founder_tag'] ?? 'From the Founder',
            'founder_quote' => $s['home_founder_quote'] ?? "I don't build one kind of software — I've shipped laboratory systems during a pandemic, national insurance middleware, licensing infrastructure, and offline-first tools for businesses that can't rely on the grid. What ties it together is that each one was built for the specific reality of the client, not a template. That's the standard I hold every project to.",
            'founder_name' => $s['home_founder_name'] ?? 'Michael Abbey',
            'founder_title' => $s['home_founder_title'] ?? 'Founder & Lead Engineer, Exchosoft',
            'founder_photo' => $s['home_founder_photo'] ?? null,
            'founder_credentials' => $j('home_founder_credentials', ['COVID-19 LIS · Pandemic Response', 'National Insurance Middleware', 'Laravel · PostgreSQL · HL7']),
            'cta_title' => $s['home_cta_title'] ?? 'Ready to Build Something That Actually Works?',
            'cta_subtitle' => $s['home_cta_subtitle'] ?? "Tell us what you need. We'll tell you honestly if we can build it.",
            'cta_btn' => $s['home_cta_btn'] ?? 'Start a Conversation',
            'cta_email_note' => $s['home_cta_email_note'] ?? '',
            'demo_cta_title' => $s['home_demo_cta_title'] ?? 'See Our Software in Action',
            'demo_cta_subtitle' => $s['home_demo_cta_subtitle'] ?? "Book a live demonstration and see how our platforms handle your specific industry's challenges.",
        ];
    }
}; ?>

<div>

    {{-- ══════════════════════════════════════════════
     HERO  —  v5 full-bleed, two-column radar design
══════════════════════════════════════════════ --}}
    <section class="relative min-h-screen grid grid-cols-1 lg:grid-cols-2 overflow-hidden" style="background:#08121d;">

        {{-- BG effects --}}
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(circle at 70% 50%,rgba(0,184,219,.06) 0%,transparent 60%),radial-gradient(circle at 20% 80%,rgba(122,207,232,.03) 0%,transparent 50%);">
        </div>
        <div class="absolute inset-0 pointer-events-none opacity-[.03] grid-lines"></div>

        {{-- Text column --}}
        <div class="relative z-10 flex flex-col justify-center px-8 md:px-16 lg:pl-24 py-32 pt-20">

            @php
                $heroTitle = preg_replace_callback(
                    '/\*\*(.+?)\*\*/',
                    fn($m) => '<em class="text-cyan not-italic">' . e($m[1]) . '</em>',
                    $cms['hero_title_raw'],
                );
            @endphp

            <div
                class="animate-fade-up delay-1 flex items-center gap-2 bg-cyan/10 border border-cyan/25 text-cyan px-4 py-1.5 rounded-full text-xs font-medium tracking-widest mb-8 w-fit uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan"></span>
                {{ $cms['hero_tag'] }}
            </div>

            <h1
                class="animate-fade-up delay-2 font-display text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tighter mb-6">
                {!! $heroTitle !!}
            </h1>

            <p class="animate-fade-up delay-3 text-body-lg text-white/60 max-w-lg mb-10 font-light leading-relaxed">
                {{ $cms['hero_subtitle'] }}
            </p>

            <div class="animate-fade-up delay-4 flex flex-wrap gap-4">
                <a href="{{ route('site.consulting') }}" wire:navigate
                    class="inline-flex items-center gap-2 font-display text-base font-semibold px-8 py-3.5 rounded-lg bg-cyan text-white hover:bg-cyan-dark hover:-translate-y-0.5 transition-all">
                    {{ $cms['hero_btn_primary'] }}
                </a>
                <a href="#services"
                    class="inline-flex items-center gap-2 font-display text-base font-semibold px-8 py-3.5 rounded-lg bg-transparent text-white border border-white/20 hover:border-cyan hover:bg-cyan/10 transition-all">
                    {{ $cms['hero_btn_secondary'] }}
                </a>
            </div>
        </div>

        {{-- Radar / Orbit column --}}
        <div class="hidden lg:flex relative z-10 items-center justify-center p-16 animate-fade-up delay-4">
            <div class="relative flex items-center justify-center" id="orbitWrap" style="width:560px;height:560px;">

                {{-- Radar disc + rings --}}
                <div class="radar-disc absolute inset-0 rounded-full pointer-events-none" style="z-index:1;">
                    @foreach ([100, 72, 46, 22] as $pct)
                        <div class="absolute rounded-full"
                            style="top:50%;left:50%;transform:translate(-50%,-50%);width:{{ $pct }}%;height:{{ $pct }}%;border:1px solid rgba(76,217,253,.12);">
                        </div>
                    @endforeach
                    <div class="radar-beam"></div>
                </div>

                {{-- Core hub --}}
                <div class="relative flex items-center justify-center rounded-full"
                    style="z-index:20;width:160px;height:160px;background:rgba(8,18,29,.95);border:1px solid rgba(0,184,219,.2);box-shadow:0 0 50px rgba(0,184,219,.1);">
                    @php
                        $logoPath = public_path('assets/images/logo.svg');
                        $hasLogo = file_exists($logoPath) && filesize($logoPath) > 0;
                    @endphp
                    @if ($hasLogo)
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="Exchosoft"
                            style="width:88px;height:auto;position:relative;z-index:2;">
                    @else
                        <span class="material-symbols-outlined text-secondary-container"
                            style="font-size:3rem;font-variation-settings:'FILL' 1;">hub</span>
                    @endif
                </div>

                {{-- Orbit icons --}}
                @foreach ([[0, 'Web Dev', '<circle cx="12" cy="12" r="9"/><path d="M12 3c0 0-4 3.5-4 9s4 9 4 9 4-3.5 4-9-4-9-4-9z"/><line x1="3" x2="21" y1="12" y2="12"/>'], [45, 'Mobile Apps', '<rect height="20" rx="2" width="10" x="7" y="2"/><line x1="12" x2="12" y1="18" y2="18"/>'], [90, 'Databases', '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>'], [135, 'Cloud', '<path d="M18 10a6 6 0 0 0-12 0 4 4 0 0 0 0 8h12a4 4 0 0 0 0-8z"/>'], [180, 'Custom Dev', '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>'], [225, 'Security', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'], [270, 'Analytics', '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>'], [315, 'API & Integrations', '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3m8 0h3a2 2 0 0 0 2-2v-3"/><circle cx="12" cy="12" r="3"/>']] as [$deg, $label, $paths])
                    <div class="orbit-icon absolute flex items-center justify-center"
                        style="width:52px;height:52px;border-radius:14px;z-index:30;cursor:default;backdrop-filter:blur(4px);background:rgba(8,18,29,.88);border:1px solid rgba(76,217,253,.18);top:50%;left:50%;margin-left:-26px;margin-top:-26px;"
                        data-angle="{{ $deg }}" data-label="{{ $label }}">
                        <svg class="w-[22px] h-[22px] opacity-60" fill="none" viewBox="0 0 24 24"
                            style="stroke:#4cd9fd;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;">
                            {!! $paths !!}
                        </svg>
                    </div>
                @endforeach

            </div>
        </div>
    </section>




    {{-- ══ STATS BAR ═══════════════════════════════════════════════════ --}}
    <div class="border-b px-8 md:px-24 py-10 grid gap-8"
        style="background:#f0f4f7;border-color:rgba(196,198,205,.3);grid-template-columns:repeat({{ count($cms['stats']) }},1fr);">
        @foreach ($cms['stats'] as $stat)
            <div class="text-center">
                <div class="font-syne text-3xl font-extrabold tracking-tight"
                    style="color:#00677c;font-family:'Syne',sans-serif;">{{ $stat['num'] ?? '' }}</div>
                <div class="text-xs mt-1 uppercase tracking-wider font-semibold" style="color:#44474d;">
                    {{ $stat['label'] ?? '' }}</div>
            </div>
        @endforeach
    </div>

    {{-- ══ WHO WE ARE ══════════════════════════════════════════════════ --}}
    <section class="site-section grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center" id="about"
        style="background:#f6fafd;">
        <div class="space-y-5">
            <p class="section-tag-label">{{ $cms['about_tag'] }}</p>
            <h2 class="section-h2">{{ $cms['about_title'] }}</h2>
            @if ($cms['about_content'])
                @foreach (array_filter(explode("\n\n", $cms['about_content'])) as $para)
                    <p style="color:var(--text-secondary);line-height:1.8;">{{ strip_tags($para) }}</p>
                @endforeach
            @else
                <p style="color:var(--text-secondary);line-height:1.8;">Exchosoft Consult is a Ghana-based software
                    development and consultancy company. We've built laboratory information systems, national
                    insurance middleware, licensing and device-management infrastructure, payment integrations, and —
                    where a client's environment calls for it — offline-first systems that keep running through
                    power and connectivity issues.</p>
                <p style="color:var(--text-secondary);line-height:1.8;">The common thread isn't one technique. It's
                    that we build for the specific business in front of us, not a template. We understand the
                    conditions our clients operate in because we're here too.</p>
            @endif
        </div>
        <div class="flex flex-col gap-4">
            @foreach ($cms['about_cards'] as $card)
                <div class="border-l-[3px] p-5 rounded-r-lg" style="background:var(--ice);border-color:#4cd9fd;">
                    <strong class="block font-syne font-bold text-sm mb-1 uppercase tracking-wide"
                        style="color:var(--navy);">{{ $card['title'] ?? '' }}</strong>
                    <span class="text-sm" style="color:var(--text-secondary);">{{ $card['body'] ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══ FEATURED PRODUCTS ════════════════════════════════════════════ --}}
    <section class="site-section" id="products" style="background:var(--ice);">
        <p class="section-tag-label">{{ $cms['products_tag'] }}</p>
        <h2 class="section-h2">{{ $cms['products_title'] }}</h2>

        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $featuredProducts->isNotEmpty() ? min(count($featuredProducts), 4) : 3 }} gap-5 mt-10">
            @if ($featuredProducts->isNotEmpty())
                @foreach ($featuredProducts as $product)
                    <a href="{{ route('site.products.show', $product->slug) }}" wire:navigate
                        class="reveal block rounded-2xl overflow-hidden text-decoration-none transition-all hover:-translate-y-1"
                        style="background:#fff;border:1px solid rgba(0,184,219,.15);text-decoration:none;"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 32px rgba(0,184,219,.12)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.15)';this.style.boxShadow='none'">
                        <div class="h-40 flex items-center justify-center relative overflow-hidden"
                            style="background:linear-gradient(135deg,#0d2137,#162d47);">
                            @if ($product->cover_image)
                                <img src="{{ asset('storage/' . $product->cover_image) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="font-syne font-extrabold text-3xl"
                                    style="color:rgba(0,184,219,.45);">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                            @endif
                            <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                                style="background:rgba(0,184,219,.15);border:1px solid rgba(0,184,219,.3);color:#00b8db;">{{ $product->category }}</span>
                            @if ($product->is_on_sale)
                                <span class="absolute top-3 right-3 text-[11px] font-bold px-2 py-0.5 rounded-full"
                                    style="background:#ef4444;color:#fff;">SALE</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <p class="font-syne font-bold text-base mb-1" style="color:var(--navy);">
                                {{ $product->name }}</p>
                            @if ($product->tagline)
                                <p class="text-sm" style="color:var(--text-secondary);">{{ $product->tagline }}</p>
                            @endif
                        </div>
                        <div class="px-5 pb-4 flex items-center justify-between border-t"
                            style="border-color:rgba(0,184,219,.1);padding-top:.75rem;">
                            <div>
                                @if ($product->is_on_sale)
                                    <div class="text-xs line-through" style="color:var(--text-muted);">GHS
                                        {{ number_format($product->price, 2) }}</div>
                                    <div class="font-syne font-extrabold" style="color:#16a34a;">GHS
                                        {{ number_format($product->sale_price, 2) }}</div>
                                @else
                                    <div class="font-syne font-extrabold" style="color:var(--navy);">GHS
                                        {{ number_format($product->price, 2) }}</div>
                                @endif
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest"
                                style="color:#00b8db;font-family:'Syne',sans-serif;">View →</span>
                        </div>
                    </a>
                @endforeach
            @else
                @foreach ([['WashOps', 'WO', 'Laundry management platform — orders, kanban, analytics.', '#08121d'], ['ChurchOps', 'CO', 'Faith community management — members, finance, SMS.', '#0f2d1f'], ['ClinicOps', 'CL', 'Healthcare management — offline-first for clinics.', '#08121d'], ['LabOps', 'LB', 'Laboratory systems — samples, results, reporting.', '#1a1000']] as [$n, $abbr, $tagline, $bg])
                    <a href="{{ route('site.products') }}" wire:navigate
                        class="reveal block rounded-2xl overflow-hidden transition-all hover:-translate-y-1"
                        style="background:#fff;border:1px solid rgba(0,184,219,.15);text-decoration:none;"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 32px rgba(0,184,219,.12)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.15)';this.style.boxShadow='none'">
                        <div class="h-40 flex items-center justify-center relative"
                            style="background:{{ $bg }};">
                            <span class="font-syne font-extrabold text-3xl"
                                style="color:rgba(76,217,253,.4);">{{ $abbr }}</span>
                            <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                                style="background:rgba(0,184,219,.15);border:1px solid rgba(0,184,219,.3);color:#4cd9fd;">{{ $n }}</span>
                        </div>
                        <div class="p-5">
                            <p class="font-syne font-bold text-base mb-1" style="color:var(--navy);">
                                {{ $n }}</p>
                            <p class="text-sm" style="color:var(--text-secondary);">{{ $tagline }}</p>
                        </div>
                        <div class="px-5 pb-4 flex items-center justify-between border-t"
                            style="border-color:rgba(0,184,219,.1);padding-top:.75rem;">
                            <div class="font-syne font-bold text-sm" style="color:#00b8db;">Learn More</div>
                            <span class="text-xs font-bold uppercase tracking-widest"
                                style="color:#00b8db;font-family:'Syne',sans-serif;">View →</span>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('site.products') }}" wire:navigate
                class="inline-flex items-center gap-2 font-syne font-semibold text-sm px-6 py-3 rounded-lg transition-colors"
                style="background:transparent;color:var(--navy);border:1px solid rgba(0,184,219,.25);"
                onmouseover="this.style.borderColor='#00b8db';this.style.background='rgba(0,184,219,.05)'"
                onmouseout="this.style.borderColor='rgba(0,184,219,.25)';this.style.background='transparent'">
                View All Products <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </section>

    {{-- ══ INDUSTRIES ════════════════════════════════════════════════════ --}}
    <section class="site-section" id="industries" style="background:var(--ice);">
        <p class="section-tag-label">{{ $cms['industries_tag'] }}</p>
        <h2 class="section-h2">{{ $cms['industries_title'] }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-12">
            @if (count($cms['industries_cards']) > 0)
                @foreach ($cms['industries_cards'] as $card)
                    <div class="reveal rounded-xl p-7 transition-all"
                        style="background:#fff;border:1px solid rgba(0,184,219,.2);"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 32px rgba(0,184,219,.1)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.2)';this.style.boxShadow='none'">
                        <div class="w-2 h-2 rounded-full mb-4" style="background:#4cd9fd;"></div>
                        <h3 class="font-syne font-bold text-base mb-2" style="color:var(--navy);">
                            {{ $card['title'] ?? '' }}</h3>
                        <p class="text-sm leading-relaxed" style="color:var(--text-secondary);">
                            {{ $card['body'] ?? '' }}</p>
                    </div>
                @endforeach
            @else
                @foreach ([['Healthcare & Medical', 'Hospital management systems, pharmacy solutions, and laboratory platforms — offline-first, designed to work when connectivity doesn\'t.'], ['Faith-Based Organizations', 'Comprehensive management systems for churches covering membership, events, donations, and complete financial transparency.'], ['Service Industries', 'From laundry management to operational platforms — tracking orders, managing workflows, and handling billing seamlessly.'], ['Heritage & Cultural', 'Partnering with Black History Walks and African Odysseys, building platforms for cultural preservation and diaspora engagement.'], ['Financial Services', 'Working with institutions like Ghana Union Assurance, building secure, reliable financial systems that meet institutional standards.'], ['Cross-Continental Initiatives', 'Supporting the African Caribbean Summit and ACIS — technology that bridges communities across continents.']] as [$title, $body])
                    <div class="reveal rounded-xl p-7 transition-all"
                        style="background:#fff;border:1px solid rgba(0,184,219,.2);"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 32px rgba(0,184,219,.1)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.2)';this.style.boxShadow='none'">
                        <div class="w-2 h-2 rounded-full mb-4" style="background:#4cd9fd;"></div>
                        <h3 class="font-syne font-bold text-base mb-2" style="color:var(--navy);">{{ $title }}
                        </h3>
                        <p class="text-sm leading-relaxed" style="color:var(--text-secondary);">{{ $body }}
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- ══ CASE STUDIES (if any) ═══════════════════════════════════════ --}}
    @if ($featuredCases->isNotEmpty())
        <section class="site-section" id="case-studies" style="background:#0d2137;">
            <p class="section-tag-label" style="color:#7acfe8;">Proof</p>
            <h2 class="section-h2 light">Case Studies</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10">
                @foreach ($featuredCases as $case)
                    <a href="{{ route('site.case-studies.show', $case->slug) }}" wire:navigate
                        class="reveal block rounded-2xl overflow-hidden transition-all hover:-translate-y-1"
                        style="background:rgba(255,255,255,.04);border:1px solid rgba(0,184,219,.15);text-decoration:none;"
                        onmouseover="this.style.borderColor='rgba(0,184,219,.4)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.15)'">
                        <div class="h-36 flex items-center justify-center" style="background:rgba(0,0,0,.2);">
                            @if ($case->cover_image)
                                <img src="{{ asset('storage/' . $case->cover_image) }}"
                                    alt="{{ $case->client_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="font-syne font-extrabold text-2xl"
                                    style="color:rgba(76,217,253,.4);">{{ strtoupper(substr($case->client_name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:#4cd9fd;">
                                {{ $case->client_name }}{{ $case->client_industry ? ' · ' . $case->client_industry : '' }}
                            </div>
                            <div class="font-syne font-bold text-base leading-tight mb-1.5" style="color:#fff;">
                                {{ $case->title }}</div>
                            @if ($case->metrics)
                                <div class="flex flex-wrap gap-3 mt-3">
                                    @foreach (array_slice($case->metrics, 0, 2) as $metric)
                                        <div class="text-xs" style="color:rgba(255,255,255,.6);">
                                            <span class="font-syne font-bold"
                                                style="color:#4cd9fd;">{{ $metric['value'] ?? '' }}</span>
                                            {{ $metric['label'] ?? '' }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('site.case-studies') }}" wire:navigate
                    class="inline-flex items-center gap-2 font-syne font-semibold text-sm px-6 py-3 rounded-lg transition-colors"
                    style="background:transparent;color:#fff;border:1px solid rgba(0,184,219,.3);">
                    View All Case Studies <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </section>
    @endif

    {{-- ══ FEATURED WORK / PORTFOLIO (if any) ══════════════════════════ --}}
    @if ($featuredWork->isNotEmpty())
        <section class="site-section" id="portfolio" style="background:var(--ice);">
            <p class="section-tag-label">Portfolio</p>
            <h2 class="section-h2">Selected Work</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-10">
                @foreach ($featuredWork as $item)
                    <a href="{{ route('site.portfolio.show', $item->slug) }}" wire:navigate
                        class="reveal block rounded-2xl overflow-hidden transition-all hover:-translate-y-1"
                        style="background:#fff;border:1px solid rgba(0,184,219,.15);text-decoration:none;"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 32px rgba(0,184,219,.12)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.15)';this.style.boxShadow='none'">
                        <div class="h-32 flex items-center justify-center"
                            style="background:linear-gradient(135deg,#0d2137,#162d47);">
                            @if ($item->cover_image)
                                <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="font-syne font-extrabold text-2xl"
                                    style="color:rgba(0,184,219,.45);">{{ strtoupper(substr($item->title, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="p-4">
                            @if ($item->client_industry)
                                <div class="text-[11px] font-bold uppercase tracking-widest mb-1"
                                    style="color:#00b8db;">
                                    {{ $item->client_industry }}</div>
                            @endif
                            <p class="font-syne font-bold text-sm" style="color:var(--navy);">{{ $item->title }}</p>
                            @if ($item->client_name)
                                <p class="text-xs mt-1" style="color:var(--text-secondary);">{{ $item->client_name }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ══ OUR SERVICES ════════════════════════════════════════════════ --}}
    <section class="site-section" id="services" style="background:#f6fafd;">
        <p class="section-tag-label">What We Offer</p>
        <h2 class="section-h2">Our Services</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-12">
            @if ($services->isNotEmpty())
                @foreach ($services as $i => $service)
                    <div class="reveal flex gap-6 items-start p-8 rounded-xl border transition-colors"
                        style="border-color:rgba(196,198,205,.3);" onmouseover="this.style.borderColor='#4cd9fd'"
                        onmouseout="this.style.borderColor='rgba(196,198,205,.3)'">
                        <div class="font-syne font-bold text-2xl leading-none flex-shrink-0"
                            style="color:#4cd9fd;font-family:'Syne',sans-serif;">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div>
                            @if ($service->tag)
                                <span class="text-[11px] font-bold uppercase tracking-widest"
                                    style="color:#00b8db;">{{ $service->tag }}</span>
                            @endif
                            <h3 class="font-syne font-bold text-lg mb-2" style="color:var(--navy);">
                                {{ $service->name }}</h3>
                            <p class="text-sm leading-relaxed" style="color:var(--text-secondary);">
                                {{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ([['01', 'Custom Software Development', 'APIs, integrations, and enterprise systems built from the ground up for your specific operations — not adapted from a template.'], ['02', 'Technology Consulting', 'Strategic guidance on technology investments, system architecture, and digital transformation based on real-world experience.'], ['03', 'System Architecture & Design', 'Cloud-native, offline-first, or LAN-based — we design the architecture to match your actual constraints, not a default.'], ['04', 'Integrations & APIs', 'Payments, clinical data (HL7), licensing infrastructure, and third-party systems — connected reliably.'], ['05', 'Digital Transformation', 'Complete operational modernization that respects how your business actually works, rather than forcing you into someone else\'s model.'], ['06', 'Ongoing Support & Evolution', 'Technology needs change as businesses grow. We provide continued consultation and development as your needs evolve over time.']] as [$num, $title, $body])
                    <div class="reveal flex gap-6 items-start p-8 rounded-xl border transition-colors"
                        style="border-color:rgba(196,198,205,.3);" onmouseover="this.style.borderColor='#4cd9fd'"
                        onmouseout="this.style.borderColor='rgba(196,198,205,.3)'">
                        <div class="font-syne font-bold text-2xl leading-none flex-shrink-0"
                            style="color:#4cd9fd;font-family:'Syne',sans-serif;">{{ $num }}</div>
                        <div>
                            <h3 class="font-syne font-bold text-lg mb-2" style="color:var(--navy);">
                                {{ $title }}
                            </h3>
                            <p class="text-sm leading-relaxed" style="color:var(--text-secondary);">
                                {{ $body }}
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- ══ WHY US ════════════════════════════════════════════════════════ --}}
    <section class="site-section" style="background:#0d2137;">
        <p class="section-tag-label" style="color:#7acfe8;">{{ $cms['why_tag'] }}</p>
        <h2 class="section-h2 light">{{ $cms['why_title'] }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
            @if (count($cms['why_items']) > 0)
                @foreach ($cms['why_items'] as $item)
                    <div class="reveal flex gap-5">
                        <div class="w-[3px] rounded-full flex-shrink-0" style="background:#4cd9fd;"></div>
                        <div>
                            <h3 class="font-syne font-bold text-lg mb-2" style="color:#fff;">
                                {{ $item['title'] ?? '' }}</h3>
                            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,.5);">
                                {{ $item['body'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ([['Full-Range Development', 'APIs, integrations, enterprise systems, web and desktop applications — built on whatever stack the problem actually calls for.'], ['We Match Architecture to Constraint', 'Cloud-native where that\'s right, offline-first or LAN-based where connectivity or cost make that the smarter call — like the systems we\'ve built for hospitals, pharmacies, and churches that can\'t afford downtime.'], ['Deep Domain Experience', 'Laboratory and healthcare systems (including HL7 clinical integrations), insurance operations, licensing infrastructure, and payments — mobile money and card processing.'], ['Long-Term Relationships', 'We stay involved. As your business grows and your needs change, we\'re there to evolve the systems we\'ve built together.']] as [$title, $body])
                    <div class="reveal flex gap-5">
                        <div class="w-[3px] rounded-full flex-shrink-0" style="background:#4cd9fd;"></div>
                        <div>
                            <h3 class="font-syne font-bold text-lg mb-2" style="color:#fff;">{{ $title }}</h3>
                            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,.5);">
                                {{ $body }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- ══ FOUNDER NOTE ══════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden" style="background:#08121d;">
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(circle at 15% 30%,rgba(0,184,219,.08) 0%,transparent 55%),radial-gradient(circle at 85% 80%,rgba(76,217,253,.05) 0%,transparent 50%);">
        </div>
        <div
            class="relative z-10 site-section grid grid-cols-1 lg:grid-cols-[340px_1fr] gap-14 lg:gap-20 items-center">

            {{-- Photo column --}}
            <div class="reveal flex flex-col items-center lg:items-start">
                <div class="relative flex items-center justify-center rounded-full flex-shrink-0"
                    style="width:220px;height:220px;background:rgba(0,184,219,.06);border:1px solid rgba(0,184,219,.25);box-shadow:0 0 80px rgba(0,184,219,.12);">
                    <div class="rounded-full overflow-hidden flex items-center justify-center"
                        style="width:188px;height:188px;background:#0d2137;border:2px solid rgba(76,217,253,.4);">
                        @if ($cms['founder_photo'])
                            <img src="{{ asset('storage/' . $cms['founder_photo']) }}"
                                alt="{{ $cms['founder_name'] }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined"
                                style="color:#4cd9fd;font-size:4.5rem;opacity:.7;">person</span>
                        @endif
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap gap-2 justify-center lg:justify-start">
                    @foreach ($cms['founder_credentials'] ?? [] as $tag)
                        <span class="text-[11px] font-semibold uppercase tracking-wider px-3 py-1.5 rounded-full"
                            style="background:rgba(0,184,219,.1);border:1px solid rgba(0,184,219,.25);color:#7acfe8;">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Quote column --}}
            <div class="reveal">
                <p class="section-tag-label" style="color:#7acfe8;">{{ $cms['founder_tag'] }}</p>
                <blockquote class="font-syne font-bold leading-[1.25] tracking-tight mt-4"
                    style="color:#fff;font-size:clamp(1.5rem,2.6vw,2.25rem);">
                    <span style="color:#4cd9fd;">&ldquo;</span>{{ $cms['founder_quote'] }}<span
                        style="color:#4cd9fd;">&rdquo;</span>
                </blockquote>

                {{-- Signature mark --}}
                <div class="flex items-center gap-4 mt-8">
                    <svg viewBox="0 0 180 60" style="width:150px;height:auto;flex-shrink:0;" fill="none">
                        <path
                            d="M6 42c8-4 14-18 20-18 5 0 4 16 9 16s10-24 17-24 6 22 12 22 14-30 22-30 3 26 10 26 16-14 22-14c5 0 2 11 8 11 7 0 16-9 24-9 6 0-2 10 5 10 6 0 12-6 19-6"
                            stroke="#4cd9fd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                            style="opacity:.9;" />
                    </svg>
                    <div class="w-px self-stretch" style="background:rgba(255,255,255,.15);"></div>
                    <div>
                        <div class="font-syne font-bold text-sm" style="color:#fff;">{{ $cms['founder_name'] }}</div>
                        <div class="text-xs mt-0.5" style="color:#7acfe8;">{{ $cms['founder_title'] }}</div>
                    </div>
                </div>

                <a href="{{ route('site.about') }}" wire:navigate
                    class="inline-flex items-center gap-2 font-syne font-semibold text-sm mt-8 transition-colors"
                    style="color:#4cd9fd;">
                    More on our story <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ══ TRUST / CLIENTS ════════════════════════════════════════════ --}}
    <section class="site-section text-center" style="background:var(--ice);">
        <p class="section-tag-label">{{ $cms['trust_tag'] }}</p>
        <h2 class="section-h2">{{ $cms['trust_title'] }}</h2>
        @if ($cms['trust_subtitle'])
            <p class="mt-2 mb-8" style="color:var(--text-secondary);">{{ $cms['trust_subtitle'] }}</p>
        @endif
        <div class="flex flex-wrap justify-center gap-3 mt-8">
            @foreach ($cms['trust_clients'] as $client)
                <div class="px-5 py-2.5 rounded-full text-sm font-medium transition-colors"
                    style="background:#fff;border:1px solid rgba(0,184,219,.18);color:var(--text-secondary);"
                    onmouseover="this.style.borderColor='#00b8db';this.style.color='#006d82'"
                    onmouseout="this.style.borderColor='rgba(0,184,219,.18)';this.style.color='var(--text-secondary)'">
                    {{ $client }}
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══ BLOG POSTS (if any) ══════════════════════════════════════════ --}}
    @if ($latestPosts->isNotEmpty())
        <section class="site-section" style="background:#f6fafd;">
            <p class="section-tag-label">Stay Informed</p>
            <h2 class="section-h2">From the Blog</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10">
                @foreach ($latestPosts as $post)
                    <a href="{{ route('site.blog.show', $post->slug) }}" wire:navigate
                        class="reveal block rounded-2xl overflow-hidden transition-all hover:-translate-y-0.5"
                        style="background:#fff;border:1px solid rgba(0,184,219,.15);text-decoration:none;"
                        onmouseover="this.style.borderColor='#00b8db';this.style.boxShadow='0 8px 24px rgba(0,184,219,.1)'"
                        onmouseout="this.style.borderColor='rgba(0,184,219,.15)';this.style.boxShadow='none'">
                        <div class="h-36 flex items-center justify-center" style="background:var(--ice);">
                            @if ($post->cover_image)
                                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-4xl"
                                    style="color:var(--text-muted);">article</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:#00b8db;">
                                {{ $post->category }}</div>
                            <div class="font-syne font-bold text-base leading-tight mb-1.5"
                                style="color:var(--navy);">{{ $post->title }}</div>
                            @if ($post->excerpt)
                                <div class="text-sm leading-relaxed" style="color:var(--text-secondary);">
                                    {{ Str::limit($post->excerpt, 100) }}</div>
                            @endif
                            <div class="text-xs mt-3" style="color:var(--text-muted);">
                                {{ $post->published_at?->format('d M Y') }} · {{ $post->read_time_minutes }} min read
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('site.blog') }}" wire:navigate
                    class="inline-flex items-center gap-2 font-syne font-semibold text-sm px-6 py-3 rounded-lg transition-colors"
                    style="background:transparent;color:var(--navy);border:1px solid rgba(0,184,219,.25);"
                    onmouseover="this.style.borderColor='#00b8db';this.style.background='rgba(0,184,219,.05)'"
                    onmouseout="this.style.borderColor='rgba(0,184,219,.25)';this.style.background='transparent'">
                    Read More Posts <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </section>
    @endif

    {{-- ══ MAIN CTA ══════════════════════════════════════════════════════ --}}
    <section class="site-cta-strip text-center" id="cta" style="justify-content:center;flex-direction:column;">
        <h2>{{ $cms['cta_title'] }}</h2>
        <p class="mx-auto">{{ $cms['cta_subtitle'] }}</p>
        <div class="flex flex-wrap gap-4 justify-center mt-2">
            <a class="btn-white-solid" href="{{ route('site.consulting') }}" wire:navigate>{{ $cms['cta_btn'] }}</a>
            <a href="{{ route('site.book-demo') }}" wire:navigate
                class="inline-flex items-center gap-2 font-syne font-semibold px-6 py-3.5 rounded-lg border border-white/40 text-white hover:bg-white/10 transition-all">
                Book a Free Demo
            </a>
        </div>
        @if ($cms['cta_email_note'])
            <span class="mt-4 text-sm" style="color:rgba(255,255,255,.7);">{{ $cms['cta_email_note'] }}</span>
        @endif
    </section>

</div>
