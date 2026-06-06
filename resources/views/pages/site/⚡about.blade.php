<?php

use App\Livewire\Concerns\LoadsPageSeo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component {
    use LoadsPageSeo;

    public function mount(): void
    {
        $this->loadPageSeo('about', 'About Us — Exchosoft Consult', 'Learn about Exchosoft Consult — a Ghana-based software development firm specialising in offline-first systems for Africa, the Caribbean, and the diaspora.');
    }
}; ?>

<div>

    {{-- ── HERO BANNER ── --}}
    <x-page-banner class="text-center flex flex-col items-center justify-center" tag="Our Story" tagIcon="info"
        title='Built for <span style="color:#00b8db;">Africa</span>,<br>by People Who Know It'
        subtitle="We're a Ghana-based technology consultancy building software that works in the real conditions of Africa, the Caribbean, and the diaspora."
        glowX="72%" glowX2="12%">
        <svg slot="ornament"
            class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none"
            viewBox="0 0 180 180" fill="none">
            <circle cx="90" cy="90" r="88" stroke="#00b8db" stroke-width="1" />
            <circle cx="90" cy="90" r="60" stroke="#00b8db" stroke-width="1" />
            <circle cx="90" cy="90" r="32" stroke="#00b8db" stroke-width="1" />
            <line x1="2" y1="90" x2="178" y2="90" stroke="#00b8db" stroke-width=".5" />
            <line x1="90" y1="2" x2="90" y2="178" stroke="#00b8db" stroke-width=".5" />
            <circle cx="90" cy="90" r="4" fill="#00b8db" />
        </svg>
    </x-page-banner>

    {{-- ── THE CHALLENGE / MISSION SECTION ── --}}
    <section class="py-20 px-4 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-[#00b8db] bg-[#00b8db]/10 px-3 py-1 rounded-full">
                    The Reality Check
                </span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Why traditional cloud software breaks down in the real world.
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                    Most enterprise software assumes a perfect world: constant ultra-fast internet, zero power grid fluctuations, and endless cloud budgets. But for a bustling market stall in Accra, a warehouse in Kumasi, or a shop in Bridgetown, a dropped connection shouldn't mean a halted business.
                </p>
                <p class="text-slate-600 dark:text-slate-400">
                    We saw local retail shops, laundromats, and farms losing hours of productivity to spinning loading wheels. That is why we stopped building for an idealistic cloud infrastructure and started designing for resilient, local network topologies.
                </p>
            </div>

            <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-900/60 rounded-3xl p-8 border border-slate-200/60 dark:border-slate-800 space-y-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-[#00b8db]/5 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-500/10 rounded-xl text-red-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white">Zero Cloud Reliance</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">If the ISP cable cuts or the fiber goes down, your point-of-sale and data stay completely operational.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="p-3 bg-[#00b8db]/10 rounded-xl text-[#00b8db] shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s8-1.79-8-4" /></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white">Local Server Infrastructure</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Run complex business mechanics, databases, and assets directly on a single, low-power device inside your physical building.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ── CORE PHILOSOPHIES / HOW WE BUILD ── --}}
    <section class="bg-slate-900 text-white py-20 px-4 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-semibold uppercase tracking-widest text-[#00b8db]">Our Engineering Principles</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight">Software Architectures Engineered for Resilience</h2>
                <p class="text-slate-400">We design software with local network boundaries, giving business owners full autonomy over their operations and data without monthly cloud tolls.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-800 hover:border-[#00b8db]/40 transition-colors duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#00b8db]/10 text-[#00b8db] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Offline-First by Default</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Data writes instantly to local hardware endpoints. If internet is present, it uses optimized background sync engines to orchestrate with backup servers—silently, smoothly, and without interrupting operations.
                    </p>
                </div>

                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-800 hover:border-[#00b8db]/40 transition-colors duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#00b8db]/10 text-[#00b8db] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">LAN Orchestration</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        We deploy self-contained software nodes running directly inside local area networks. Multiple devices (phones, registers, desktop endpoints) sync automatically via your standard local Wi-Fi router.
                    </p>
                </div>

                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-800 hover:border-[#00b8db]/40 transition-colors duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#00b8db]/10 text-[#00b8db] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Data Sovereignty</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Your transaction logs, customer relationships, and inventory assets shouldn't live trapped behind an expensive third-party web subscription model. Your database remains wholly yours.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ── STATS / METRICS SECTION ── --}}
    <section class="py-16 px-4 bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/60 dark:border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

                <div class="space-y-1">
                    <div class="text-4xl font-extrabold text-[#00b8db]">100%</div>
                    <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Local Uptime Guaranteed</div>
                </div>

                <div class="space-y-1">
                    <div class="text-4xl font-extrabold text-slate-900 dark:text-white">0ms</div>
                    <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">ISP Latency During Sales</div>
                </div>

                <div class="space-y-1">
                    <div class="text-4xl font-extrabold text-slate-900 dark:text-white">3+</div>
                    <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Targeted Trade Markets</div>
                </div>

                <div class="space-y-1">
                    <div class="text-4xl font-extrabold text-[#00b8db]">Pure</div>
                    <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Hardware Autonomy</div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── CALL TO ACTION ── --}}
    <section class="py-20 px-4 max-w-5xl mx-auto text-center space-y-8">
        <div class="w-16 h-16 rounded-2xl bg-[#00b8db]/10 text-[#00b8db] flex items-center justify-center mx-auto shadow-sm">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
            Ready to stabilize your local operations?
        </h2>
        <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg">
            Let us deploy resilient, offline-capable configurations tailored specifically to your localized business ecosystem. No connectivity drops, no unnecessary overheads.
        </p>
        <div>
            <a href="#contact" class="inline-flex items-center justify-center bg-[#00b8db] hover:bg-[#009cb8] text-white font-medium px-8 py-3 rounded-xl transition-colors shadow-sm gap-2">
                Get in Touch
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>
    </section>

</div>
