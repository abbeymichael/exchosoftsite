<!DOCTYPE html>

<html class="scroll-smooth" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Exchosoft Consult — Technology Consultancy Built on Real-World Experience</title>
    <!-- Tailwind CSS v3 with forms and container queries -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#050911',
                            card: '#0c1424',
                            surface: '#111d33',
                            cyan: '#00f2fe',
                            teal: '#00d2b4',
                            blue: '#2563eb',
                            sky: '#38bdf8'
                        }
                    },
                    fontFamily: {
                        tech: ['Space Grotesk', 'Inter', 'sans-serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    backgroundImage: {
                        'grid-pattern': "radial-gradient(rgba(0, 242, 254, 0.12) 1px, transparent 1px)",
                        'cyber-gradient': 'linear-gradient(135deg, #00f2fe 0%, #00d2b4 100%)',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts for High-Tech Aesthetic -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap"
        rel="stylesheet" />
    <style data-purpose="base-styling">
        body {
            background-color: #050911;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        .font-tech {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.02em;
        }

        .grid-bg {
            background-size: 32px 32px;
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }

        .neon-border {
            border: 1px solid rgba(0, 242, 254, 0.18);
        }

        .neon-border-hover:hover {
            border-color: rgba(0, 242, 254, 0.5);
            box-shadow: 0 0 25px rgba(0, 242, 254, 0.12);
        }

        .glow-cyan {
            box-shadow: 0 0 35px -5px rgba(0, 242, 254, 0.35);
        }
    </style>
</head>

<body class="bg-[#050911] text-slate-200 antialiased selection:bg-cyan-500 selection:text-black">
    <!-- BEGIN: MainHeader -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#050911]/85 border-b border-cyan-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a class="flex items-center gap-3 group" data-purpose="logo" href="#">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-teal-400 p-[1.5px] shadow-[0_0_20px_rgba(0,242,254,0.3)] group-hover:shadow-[0_0_30px_rgba(0,242,254,0.6)] transition-all duration-300">
                    <div class="w-full h-full bg-[#070d18] rounded-[10px] flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span
                        class="text-xl font-bold tracking-tight text-white font-tech group-hover:text-cyan-400 transition-colors">Exchosoft<span
                            class="text-cyan-400 font-light">Consult</span></span>
                    <span class="text-[10px] tracking-widest text-slate-400 uppercase font-mono">Software
                        Architecture</span>
                </div>
            </a>
            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-300"
                data-purpose="main-navigation">
                <a class="text-cyan-400 font-semibold transition-colors" href="#hero">Home</a>
                <a class="hover:text-cyan-400 transition-colors" href="#operational-reality">About</a>
                <div class="relative group">
                    <a class="hover:text-cyan-400 flex items-center gap-1.5 transition-colors" href="#products">
                        Products
                        <span
                            class="text-[10px] bg-cyan-500/10 text-cyan-400 px-1.5 py-0.5 rounded border border-cyan-500/30">Suite</span>
                    </a>
                </div>
                <a class="hover:text-cyan-400 transition-colors" href="#services">Services</a>
                <a class="hover:text-cyan-400 transition-colors" href="#industries">Case Studies</a>
                <a class="hover:text-cyan-400 transition-colors" href="#philosophy">Insights</a>
            </nav>
            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                <a class="hidden sm:inline-flex text-xs font-semibold uppercase tracking-wider text-slate-300 hover:text-white px-3 py-2 transition-colors"
                    href="#demo">
                    Sign In
                </a>
                <a class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-xs uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 via-teal-400 to-cyan-300 text-slate-950 shadow-[0_0_20px_rgba(0,242,254,0.35)] hover:shadow-[0_0_30px_rgba(0,242,254,0.6)] hover:scale-[1.02] active:scale-[0.98] transition-all"
                    href="#contact">
                    Talk to Us
                </a>
            </div>
        </div>
    </header>
    <!-- END: MainHeader -->
    <main>
        <!-- BEGIN: HeroSection -->
        <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-hidden grid-bg border-b border-cyan-500/10"
            data-purpose="hero-section" id="hero">
            <!-- Glow ambient lights -->
            <div
                class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-cyan-500/10 blur-[130px] rounded-full pointer-events-none">
            </div>
            <div
                class="absolute top-1/3 right-10 w-[300px] h-[300px] bg-teal-500/10 blur-[120px] rounded-full pointer-events-none">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    <!-- Hero Copy Left (7 cols) -->
                    <div class="lg:col-span-7 text-center lg:text-left">
                        <!-- Region Badge -->
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-950/60 border border-cyan-500/30 text-cyan-300 text-xs font-mono uppercase tracking-widest mb-8">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                            <span class="w-2 h-2 rounded-full bg-cyan-400 -ml-4"></span>
                            Ghana-Based • Africa • Caribbean • Diaspora
                        </div>
                        <h1
                            class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.1] mb-6">
                            Technology <br class="hidden sm:inline" />
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-cyan-300">Consultancy</span>
                            Built on <br />
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-teal-300 to-sky-400">Real-World
                                Experience</span>
                        </h1>
                        <p
                            class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl mx-auto lg:mx-0 mb-10">
                            We're a software development and consultancy firm serving Black businesses across Africa,
                            the Caribbean, and the diaspora—building custom solutions that work in your reality, not
                            just in theory.
                        </p>
                        <!-- CTA Group -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <a class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-wider text-slate-950 bg-gradient-to-r from-cyan-400 to-teal-400 hover:from-cyan-300 hover:to-teal-300 shadow-[0_0_30px_rgba(0,242,254,0.4)] transition-all flex items-center justify-center gap-2"
                                href="#contact">
                                <span>Talk to Us</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2.5"></path>
                                </svg>
                            </a>
                            <a class="w-full sm:w-auto px-8 py-4 rounded-xl font-semibold text-sm uppercase tracking-wider text-slate-200 border border-slate-700 bg-slate-900/60 hover:bg-slate-800/80 hover:border-cyan-500/40 transition-all text-center"
                                href="#philosophy">
                                Our Expertise
                            </a>
                        </div>
                    </div>
                    <!-- Hero Graphic Right: Interactive Concentric Orbital Radar Visualization (5 cols) -->
                    <div class="lg:col-span-5 flex justify-center items-center">
                        <div class="relative w-[340px] h-[340px] sm:w-[440px] sm:h-[440px] flex items-center justify-center"
                            data-purpose="hud-orbital-radar">
                            <!-- Outermost Orbit Track -->
                            <div
                                class="absolute inset-0 rounded-full border border-cyan-500/15 animate-[spin_60s_linear_infinite]">
                            </div>
                            <!-- Middle Orbit Track with Dotted Border -->
                            <div class="absolute inset-8 rounded-full border border-dashed border-cyan-400/25"></div>
                            <!-- Inner Orbit Track with Glowing Pulse -->
                            <div class="absolute inset-20 rounded-full border border-teal-500/30"></div>
                            <!-- Center Core Glowing Hub -->
                            <div
                                class="relative z-20 w-24 h-24 rounded-full bg-gradient-to-br from-[#0c1a2f] to-[#040810] border border-cyan-400/60 shadow-[0_0_40px_rgba(0,242,254,0.35)] flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-cyan-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewbox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                                <div class="text-[9px] font-mono text-cyan-300 uppercase tracking-wider mt-1">Excho-Hub
                                </div>
                            </div>
                            <!-- Orbital Node 1: Mobile Apps -->
                            <div
                                class="absolute top-4 right-14 z-20 flex items-center gap-2 bg-[#0d182b]/90 border border-cyan-500/40 px-3 py-2 rounded-xl backdrop-blur-md shadow-lg shadow-black/60">
                                <div
                                    class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-mono text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                        <rect height="20" rx="2" ry="2" width="14" x="5"
                                            y="2"></rect>
                                        <line x1="12" x2="12.01" y1="18" y2="18"></line>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">Mobile Apps</div>
                                    <div class="text-[10px] text-teal-400 font-mono">Synced Cache</div>
                                </div>
                            </div>
                            <!-- Orbital Node 2: Databases -->
                            <div
                                class="absolute right-0 top-1/2 -translate-y-1/2 z-20 flex items-center gap-2 bg-[#0d182b]/90 border border-cyan-500/40 px-3 py-2 rounded-xl backdrop-blur-md shadow-lg shadow-black/60">
                                <div
                                    class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-mono text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                        <ellipse cx="12" cy="5" rx="9" ry="3">
                                        </ellipse>
                                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">Databases</div>
                                    <div class="text-[10px] text-teal-400 font-mono">Offline-First</div>
                                </div>
                            </div>
                            <!-- Orbital Node 3: Cloud -->
                            <div
                                class="absolute bottom-6 right-20 z-20 flex items-center gap-2 bg-[#0d182b]/90 border border-cyan-500/40 px-3 py-2 rounded-xl backdrop-blur-md shadow-lg shadow-black/60">
                                <div
                                    class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center font-mono text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                        <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">Cloud Grid</div>
                                    <div class="text-[10px] text-teal-400 font-mono">Auto Reconnect</div>
                                </div>
                            </div>
                            <!-- Orbital Node 4: LAN Sync -->
                            <div
                                class="absolute bottom-16 left-6 z-20 flex items-center gap-2 bg-[#0d182b]/90 border border-cyan-500/40 px-3 py-2 rounded-xl backdrop-blur-md shadow-lg shadow-black/60">
                                <div
                                    class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-mono text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                        <rect height="8" rx="2" ry="2" width="20" x="2"
                                            y="2"></rect>
                                        <rect height="8" rx="2" ry="2" width="20" x="2"
                                            y="14"></rect>
                                        <line x1="6" x2="6.01" y1="6" y2="6"></line>
                                        <line x1="6" x2="6.01" y1="18" y2="18"></line>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">LAN Mesh</div>
                                    <div class="text-[10px] text-teal-400 font-mono">Local Comm</div>
                                </div>
                            </div>
                            <!-- Radar Sweeper Overlay SVG -->
                            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-40 animate-[spin_12s_linear_infinite]"
                                viewbox="0 0 400 400">
                                <defs>
                                    <lineargradient id="radarSweep" x1="0%" x2="100%" y1="0%"
                                        y2="100%">
                                        <stop offset="0%" stop-color="#00f2fe" stop-opacity="0.4"></stop>
                                        <stop offset="100%" stop-color="#00f2fe" stop-opacity="0"></stop>
                                    </lineargradient>
                                </defs>
                                <path d="M 200 200 L 200 0 A 200 200 0 0 1 373 100 Z" fill="url(#radarSweep)"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: HeroSection -->
        <!-- BEGIN: KeyMetricsBar -->
        <section class="border-b border-cyan-500/10 bg-[#070d18]" data-purpose="metrics-bar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="border-r border-slate-800 last:border-none pr-4">
                        <div
                            class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300 font-tech">
                            10+</div>
                        <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-slate-400 mt-2">
                            Industries Served</div>
                    </div>
                    <div class="border-r border-slate-800 last:border-none pr-4">
                        <div
                            class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300 font-tech">
                            3</div>
                        <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-slate-400 mt-2">
                            Continents Reached</div>
                    </div>
                    <div class="border-r border-slate-800 last:border-none pr-4">
                        <div
                            class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300 font-tech">
                            100%</div>
                        <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-slate-400 mt-2">
                            Custom Solutions</div>
                    </div>
                    <div>
                        <div
                            class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300 font-tech">
                            Offline</div>
                        <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-slate-400 mt-2">
                            First Architecture</div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: KeyMetricsBar -->
        <!-- BEGIN: OperationalRealitySection -->
        <section class="py-24 bg-[#050911] relative" data-purpose="operational-context" id="operational-reality">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                    <!-- Context Statement (5 cols) -->
                    <div class="lg:col-span-5">
                        <div class="text-xs font-mono text-cyan-400 uppercase tracking-widest mb-3 font-semibold">Who
                            We Are</div>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                            Built for the Conditions You Actually Operate In
                        </h2>
                        <p class="text-slate-300 text-base leading-relaxed mb-6 font-normal">
                            Exchosoft Consult is a Ghana-based technology consultancy and software development company.
                            We've built systems for churches, hospitals, pharmacies, laboratories, laundries, heritage
                            organizations — each one custom-designed for that specific business.
                        </p>
                        <p class="text-slate-400 text-sm leading-relaxed border-l-2 border-cyan-400 pl-4 py-1 italic">
                            "We understand the conditions our clients operate in because we're here too."
                        </p>
                    </div>
                    <!-- Feature Cards Grid (7 cols) -->
                    <div class="lg:col-span-7 grid sm:grid-cols-2 gap-5">
                        <!-- Card 1 -->
                        <div
                            class="bg-[#0b1325] p-6 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all duration-300 group">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-950 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-2.828-5.657m0 0l2.828 2.829m-2.828-2.829L3 3m5.657 5.657a5 5 0 012.829 1.414"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white uppercase tracking-wide mb-2 font-tech">
                                Intermittent Connectivity</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                We build systems that keep working when the internet drops, queuing transactional
                                changes locally.
                            </p>
                        </div>
                        <!-- Card 2 -->
                        <div
                            class="bg-[#0b1325] p-6 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all duration-300 group">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-950 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white uppercase tracking-wide mb-2 font-tech">Power
                                Challenges</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Offline-first architecture means zero data loss during power outages and sudden
                                infrastructure disruptions.
                            </p>
                        </div>
                        <!-- Card 3 -->
                        <div
                            class="bg-[#0b1325] p-6 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all duration-300 group">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-950 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <rect height="20" rx="2" ry="2" width="14" x="5" y="2">
                                    </rect>
                                    <line x1="12" x2="12.01" y1="18" y2="18"></line>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white uppercase tracking-wide mb-2 font-tech">
                                Mobile-First Users</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Designed from the ground up for the hardware, screens, and data consumption profiles
                                your customers actually use.
                            </p>
                        </div>
                        <!-- Card 4 -->
                        <div
                            class="bg-[#0b1325] p-6 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all duration-300 group">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-950 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <rect height="14" rx="2" width="20" x="2" y="5"></rect>
                                    <line x1="2" x2="22" y1="10" y2="10"></line>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white uppercase tracking-wide mb-2 font-tech">Local
                                Payment Systems</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Integrated natively with Mobile Money (MTN, Telecel, AirtelTigo), local bank settlement
                                switches, and USSD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: OperationalRealitySection -->
        <!-- BEGIN: ProductsSuiteSection -->
        <section class="py-24 bg-[#070d18] border-y border-cyan-500/10" data-purpose="products-showcase"
            id="products">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-mono text-cyan-400 uppercase tracking-widest font-semibold">Our
                        Software</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-2 mb-4 font-tech">
                        Products Built for African Businesses
                    </h2>
                    <p class="text-slate-400 text-sm sm:text-base">
                        Turnkey enterprise suites crafted for specialized sectors, engineered with offline resilience
                        and intuitive workflows.
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Product 1: WashOps -->
                    <div
                        class="bg-[#0c1424] rounded-2xl border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_25px_rgba(0,242,254,0.15)] transition-all flex flex-col justify-between group">
                        <div class="p-6">
                            <div
                                class="h-36 rounded-xl bg-gradient-to-br from-[#0e1d38] to-[#080d19] border border-cyan-500/20 flex flex-col items-center justify-center relative mb-6">
                                <span class="text-4xl font-extrabold text-cyan-400 font-tech tracking-wider">WO</span>
                                <span
                                    class="absolute top-3 left-3 text-[10px] font-mono text-cyan-300/80 bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-500/20">WashOps</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">WashOps</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Laundry management platform — orders, tracking, automated status SMS, inventory and
                                financial analytics.
                            </p>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-800/80 mt-4">
                            <span class="text-xs font-semibold text-slate-400">Learn More</span>
                            <a class="text-xs font-bold text-cyan-400 uppercase tracking-wider group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                href="#contact">
                                View Suite →
                            </a>
                        </div>
                    </div>
                    <!-- Product 2: ChurchOps -->
                    <div
                        class="bg-[#0c1424] rounded-2xl border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_25px_rgba(0,242,254,0.15)] transition-all flex flex-col justify-between group">
                        <div class="p-6">
                            <div
                                class="h-36 rounded-xl bg-gradient-to-br from-[#0c262a] to-[#071318] border border-teal-500/20 flex flex-col items-center justify-center relative mb-6">
                                <span class="text-4xl font-extrabold text-teal-300 font-tech tracking-wider">CO</span>
                                <span
                                    class="absolute top-3 left-3 text-[10px] font-mono text-teal-300/80 bg-teal-950/60 px-2 py-0.5 rounded border border-teal-500/20">ChurchOps</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">ChurchOps</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Faith community management — members database, pledges, automated tithes, ministry
                                rosters &amp; SMS broadcast.
                            </p>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-800/80 mt-4">
                            <span class="text-xs font-semibold text-slate-400">Learn More</span>
                            <a class="text-xs font-bold text-teal-400 uppercase tracking-wider group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                href="#contact">
                                View Suite →
                            </a>
                        </div>
                    </div>
                    <!-- Product 3: ClinicOps -->
                    <div
                        class="bg-[#0c1424] rounded-2xl border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_25px_rgba(0,242,254,0.15)] transition-all flex flex-col justify-between group">
                        <div class="p-6">
                            <div
                                class="h-36 rounded-xl bg-gradient-to-br from-[#0f233a] to-[#08121f] border border-sky-500/20 flex flex-col items-center justify-center relative mb-6">
                                <span class="text-4xl font-extrabold text-sky-400 font-tech tracking-wider">CL</span>
                                <span
                                    class="absolute top-3 left-3 text-[10px] font-mono text-sky-300/80 bg-sky-950/60 px-2 py-0.5 rounded border border-sky-500/20">ClinicOps</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">ClinicOps</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Healthcare management — offline-first EMR for clinics, patient histories, triage flow
                                &amp; pharmacy dispensing.
                            </p>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-800/80 mt-4">
                            <span class="text-xs font-semibold text-slate-400">Learn More</span>
                            <a class="text-xs font-bold text-sky-400 uppercase tracking-wider group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                href="#contact">
                                View Suite →
                            </a>
                        </div>
                    </div>
                    <!-- Product 4: LabOps -->
                    <div
                        class="bg-[#0c1424] rounded-2xl border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_25px_rgba(0,242,254,0.15)] transition-all flex flex-col justify-between group">
                        <div class="p-6">
                            <div
                                class="h-36 rounded-xl bg-gradient-to-br from-[#241a10] to-[#120e09] border border-amber-500/20 flex flex-col items-center justify-center relative mb-6">
                                <span class="text-4xl font-extrabold text-amber-400 font-tech tracking-wider">LB</span>
                                <span
                                    class="absolute top-3 left-3 text-[10px] font-mono text-amber-300/80 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-500/20">LabOps</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">LabOps</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Laboratory information systems — sample tracking, diagnostic metrics, batch tests, and
                                verified PDF reports.
                            </p>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-800/80 mt-4">
                            <span class="text-xs font-semibold text-slate-400">Learn More</span>
                            <a class="text-xs font-bold text-amber-400 uppercase tracking-wider group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                href="#contact">
                                View Suite →
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-12 text-center">
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-slate-700 bg-slate-900/80 hover:bg-slate-800 hover:border-cyan-400/50 text-xs uppercase font-bold tracking-wider text-slate-200 transition-all"
                        href="#contact">
                        <span>View All Products &amp; Custom Modules</span>
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
        <!-- END: ProductsSuiteSection -->
        <!-- BEGIN: PhilosophySection -->
        <section class="py-24 bg-[#050911] relative" data-purpose="engineering-philosophy" id="philosophy">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mb-16">
                    <span class="text-xs font-mono text-cyan-400 uppercase tracking-widest font-semibold">Our
                        Approach</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-2 font-tech">
                        What We've Learned Building Software Across Industries
                    </h2>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Pillar 1 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">Every Business Needs Its Own Solution
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Off-the-shelf software forces unacceptable operational compromises. Each business has unique
                            workflows deserving technology engineered precisely for how they operate.
                        </p>
                    </div>
                    <!-- Pillar 2 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v6l4 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">Offline-First When It Matters</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            We pioneered offline-first architecture for clients who can't afford downtime — hospitals,
                            pharmacies, busy churches — complete with automatic bidirectional cloud sync.
                        </p>
                    </div>
                    <!-- Pillar 3 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">Unified Systems, Clear Insights</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            We unify disparate business workflows into cohesive systems that give management complete
                            real-time visibility and actionable executive reporting.
                        </p>
                    </div>
                    <!-- Pillar 4 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <rect height="14" rx="2" ry="2" width="20" x="2" y="3">
                                </rect>
                                <line x1="8" x2="16" y1="21" y2="21"></line>
                                <line x1="12" x2="12" y1="17" y2="21"></line>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">LAN Collaboration</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Real-time collaboration across multiple terminals even when external internet connectivity
                            fails — perfect for fast-paced multi-device internal business floors.
                        </p>
                    </div>
                    <!-- Pillar 5 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">Security &amp; Reliability</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            From financial institutions to regulated healthcare providers, we build with enterprise
                            security baked into the foundation — not retrofitted later.
                        </p>
                    </div>
                    <!-- Pillar 6 -->
                    <div
                        class="p-8 rounded-2xl bg-[#0a1120] border border-slate-800/80 hover:border-cyan-500/30 transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-cyan-950/60 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3 font-tech">Long-Term Partnership</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            We're not just building code; we are building systems that scale with your multi-year
                            expansion. We stay actively involved as your technical advisory partners.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: PhilosophySection -->
        <!-- BEGIN: IndustriesServedSection -->
        <section class="py-24 bg-[#070d18] border-t border-cyan-500/10" data-purpose="industries-grid"
            id="industries">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span
                        class="text-xs font-mono text-cyan-400 uppercase tracking-widest font-semibold">Experience</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2 font-tech">Industries We've Served</h2>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Healthcare &amp; Medical</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Hospital management systems, pharmacy solutions, and laboratory platforms —
                                offline-first, designed to function under unstable bandwidth.
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Faith-Based Organizations</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Comprehensive management systems for churches covering membership, event schedules,
                                contributions, and audited financial transparency.
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Service Industries</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                From commercial laundries to operational services — tracking customer orders, managing
                                multi-tier workflows, and handling dispatch flawlessly.
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Heritage &amp; Cultural</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Partnering with Black History Walks and African Odysseys, building global engagement
                                platforms for cultural education and preservation.
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Financial Services</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Working with institutions like Ghana Union Assurance, building secure, reliable
                                financial tools that meet stringent regional standards.
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#0b1324] p-6 rounded-2xl border border-slate-800 flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 mt-2 shrink-0 shadow-[0_0_8px_#00f2fe]"></div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-1.5 font-tech">Cross-Continental Initiatives
                            </h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Supporting the African Caribbean Summit and AOS — technology bridging communities, trade
                                networks, and institutions across the Atlantic.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: IndustriesServedSection -->
        <!-- BEGIN: ServicesNumberedSection -->
        <section class="py-24 bg-[#050911]" data-purpose="services-directory" id="services">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-16">
                    <span class="text-xs font-mono text-cyan-400 uppercase tracking-widest font-semibold">What We
                        Offer</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-2 font-tech">Our Services</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                    <!-- Service 01 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">01</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">Custom Software Development</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Built from the ground up for your specific operations, workflows, and challenges. No
                                pre-packaged templates; no forcing your business into pre-made molds.
                            </p>
                        </div>
                    </div>
                    <!-- Service 02 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">02</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">Technology Consulting</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Strategic guidance on technology investments, system architecture, and digital
                                transformation grounded in real-world deployment experience.
                            </p>
                        </div>
                    </div>
                    <!-- Service 03 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">03</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">System Architecture &amp; Design
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Offline-first capabilities, secure cloud integration, and LAN collaboration
                                architectures engineered to thrive amid power and ISP volatility.
                            </p>
                        </div>
                    </div>
                    <!-- Service 04 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">04</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">Business Process Analysis</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                We analyze your operational bottlenecks, personnel handoffs, and resource waste to
                                identify maximum-ROI opportunities for automation.
                            </p>
                        </div>
                    </div>
                    <!-- Service 05 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">05</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">Digital Transformation</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Complete operational modernization that respects how your workforce executes, rather
                                than imposing foreign, unusable software frameworks.
                            </p>
                        </div>
                    </div>
                    <!-- Service 06 -->
                    <div
                        class="p-8 rounded-2xl bg-[#09101e] border border-slate-800 hover:border-cyan-500/40 transition-all flex gap-6">
                        <span class="text-2xl font-bold font-mono text-cyan-400 shrink-0">06</span>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-tech">Ongoing Support &amp; Evolution
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Technology needs change as enterprises expand. We provide dedicated support, maintenance
                                sprints, and iterative capability development.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: ServicesNumberedSection -->
        <!-- BEGIN: WhyWorkWithUsSection -->
        <section class="py-24 bg-[#070e1b] border-t border-cyan-500/10" data-purpose="value-proposition">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl mb-16">
                    <span class="text-xs font-mono text-cyan-400 uppercase tracking-widest font-semibold">Why
                        Exchosoft</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-2 font-tech">Why Work With Us
                    </h2>
                </div>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-[#091122] p-8 rounded-2xl border-l-4 border-l-cyan-400 border border-slate-800/70">
                        <h3 class="text-xl font-bold text-white mb-2 font-tech">We Build What You Actually Need</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Not what we think you should need. Not what worked for someone else in Silicon Valley. We
                            listen, understand your boots-on-the-ground operational model, and engineer explicitly for
                            you.
                        </p>
                    </div>
                    <div class="bg-[#091122] p-8 rounded-2xl border-l-4 border-l-teal-400 border border-slate-800/70">
                        <h3 class="text-xl font-bold text-white mb-2 font-tech">We Understand Your Context</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            From Lagos to London, Accra to Atlanta — we comprehend the infrastructural nuances, consumer
                            habits, and logistical realities of running business in our communities.
                        </p>
                    </div>
                    <div class="bg-[#091122] p-8 rounded-2xl border-l-4 border-l-cyan-400 border border-slate-800/70">
                        <h3 class="text-xl font-bold text-white mb-2 font-tech">Offline-First by Default</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Every critical internal terminal assumes connection dropouts. Your staff maintains velocity,
                            records sync instantly upon reconnection, and zero revenue is compromised.
                        </p>
                    </div>
                    <div class="bg-[#091122] p-8 rounded-2xl border-l-4 border-l-teal-400 border border-slate-800/70">
                        <h3 class="text-xl font-bold text-white mb-2 font-tech">Long-Term Relationships</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            We do not disappear after deployment. As your volume multiplies and staff grows, our
                            advisory and engineering teams remain locked-in to scale the platform.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: WhyWorkWithUsSection -->
        <!-- BEGIN: TrustedByBadgesSection -->
        <section class="py-16 bg-[#050911] border-t border-cyan-500/10 text-center"
            data-purpose="social-proof-badges">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400 block mb-8 font-medium">Trusted
                    By</span>
                <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                    <div
                        class="px-6 py-3 rounded-full bg-[#0c1628] border border-cyan-500/20 text-slate-200 text-xs font-semibold tracking-wide hover:border-cyan-400 transition-colors">
                        Healthcare Facilities
                    </div>
                    <div
                        class="px-6 py-3 rounded-full bg-[#0c1628] border border-cyan-500/20 text-slate-200 text-xs font-semibold tracking-wide hover:border-cyan-400 transition-colors">
                        Church Networks
                    </div>
                    <div
                        class="px-6 py-3 rounded-full bg-[#0c1628] border border-cyan-500/20 text-slate-200 text-xs font-semibold tracking-wide hover:border-cyan-400 transition-colors">
                        Laundry &amp; Logistics Businesses
                    </div>
                    <div
                        class="px-6 py-3 rounded-full bg-[#0c1628] border border-cyan-500/20 text-slate-200 text-xs font-semibold tracking-wide hover:border-cyan-400 transition-colors">
                        Financial Institutions
                    </div>
                </div>
            </div>
        </section>
        <!-- END: TrustedByBadgesSection -->
        <!-- BEGIN: LiveDemoBannerSection -->
        <section class="py-20 bg-[#091122] border-y border-cyan-500/10 relative" data-purpose="interactive-demo-card"
            id="demo">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div
                    class="w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-400/40 text-cyan-400 mx-auto flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 font-tech">See Our Software in Action
                </h2>
                <p class="text-slate-300 text-base max-w-xl mx-auto mb-8 font-normal">
                    Book a live demonstration and see how our platforms effortlessly handle intermittent network
                    blackouts, complex multi-branch reconciliation, and rapid POS operations.
                </p>
                <a class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-xs uppercase tracking-wider bg-cyan-400 text-slate-950 hover:bg-cyan-300 shadow-[0_0_30px_rgba(0,242,254,0.35)] transition-all"
                    href="#contact">
                    <span>Book a Free Demo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5">
                        </path>
                    </svg>
                </a>
            </div>
        </section>
        <!-- END: LiveDemoBannerSection -->
        <!-- BEGIN: HighImpactCTABanner -->
        <section
            class="py-20 bg-gradient-to-r from-[#00d2b4] via-[#00f2fe] to-[#38bdf8] text-slate-950 relative overflow-hidden"
            data-purpose="high-impact-cta" id="contact">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-950 mb-6 font-tech">
                    Ready to Build Something That Actually Works?
                </h2>
                <p class="text-base sm:text-lg text-slate-900/90 font-medium max-w-2xl mx-auto mb-10">
                    Tell us what you need. We'll tell you honestly if we can build it, how long it will take, and the
                    precise architecture required.
                </p>
                <a class="inline-block px-10 py-4 rounded-xl font-extrabold text-sm uppercase tracking-wider bg-white text-slate-950 shadow-2xl hover:bg-slate-100 hover:scale-105 active:scale-95 transition-all"
                    href="mailto:contact@exchosoft.com">
                    Start a Conversation
                </a>
            </div>
        </section>
        <!-- END: HighImpactCTABanner -->
    </main>
    <!-- BEGIN: MainFooter -->
    <footer class="bg-[#040810] border-t border-cyan-500/10 text-slate-400 py-16" data-purpose="site-footer">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-16">
                <!-- Brand Column (2 cols on lg) -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-cyan-400 p-[1px] flex items-center justify-center">
                            <div class="w-full h-full bg-[#050911] rounded-[7px] flex items-center justify-center">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewbox="0 0 24 24">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                    <polyline points="2 17 12 22 22 17"></polyline>
                                    <polyline points="2 12 12 17 22 12"></polyline>
                                </svg>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-white font-tech">Exchosoft<span
                                class="text-cyan-400 font-light">Consult</span></span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed mb-6 font-normal">
                        Built from Here. Industrial Reliability meets Cutting-Edge Innovation. Engineering resilient
                        digital ecosystems across Africa, the Caribbean, and global diaspora hubs.
                    </p>
                    <div class="flex items-center gap-4 text-slate-400">
                        <a aria-label="Global Web"
                            class="w-8 h-8 rounded-lg bg-slate-900 hover:text-cyan-400 flex items-center justify-center border border-slate-800 transition-colors"
                            href="#">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" x2="22" y1="12" y2="12"></line>
                                <path
                                    d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                </path>
                            </svg>
                        </a>
                        <a aria-label="Github or Code"
                            class="w-8 h-8 rounded-lg bg-slate-900 hover:text-cyan-400 flex items-center justify-center border border-slate-800 transition-colors"
                            href="#">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <polyline points="16 18 22 12 16 6"></polyline>
                                <polyline points="8 6 2 12 8 18"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <!-- Solutions Column -->
                <div>
                    <h4 class="text-xs font-mono uppercase tracking-widest text-white font-bold mb-4">Solutions</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a class="hover:text-cyan-400 transition-colors" href="#products">WashOps Laundry OS</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#products">ChurchOps Enterprise</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#products">ClinicOps Health EMR</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#products">LabOps Diagnostics</a>
                        </li>
                    </ul>
                </div>
                <!-- Expertise Column -->
                <div>
                    <h4 class="text-xs font-mono uppercase tracking-widest text-white font-bold mb-4">Expertise</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a class="hover:text-cyan-400 transition-colors" href="#services">Custom Development</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#services">Strategic Consulting</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#services">Offline Architecture</a>
                        </li>
                        <li><a class="hover:text-cyan-400 transition-colors" href="#services">Digital
                                Transformation</a></li>
                    </ul>
                </div>
                <!-- Talk to Us / Newsletter Form Column -->
                <div>
                    <h4 class="text-xs font-mono uppercase tracking-widest text-white font-bold mb-4">Talk to Us</h4>
                    <p class="text-xs text-slate-400 mb-3">Subscribe to our industrial technology briefings.</p>
                    <form class="space-y-2" data-purpose="newsletter-subscription"
                        onsubmit="event.preventDefault();">
                        <div class="relative">
                            <input
                                class="w-full bg-[#0c1424] border border-slate-800 rounded-lg px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 transition-colors"
                                placeholder="Enter your business email" required="" type="email" />
                        </div>
                        <button
                            class="w-full bg-cyan-400 text-slate-950 text-xs font-bold uppercase tracking-wider py-2.5 rounded-lg hover:bg-cyan-300 transition-colors"
                            type="submit">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <!-- Bottom Bar -->
            <div
                class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <div>
                    © <span id="year">2026</span> Exchosoft Consult. All rights reserved.
                </div>
                <div class="flex items-center gap-6">
                    <a class="hover:text-cyan-400 transition-colors" href="#">Privacy Policy</a>
                    <a class="hover:text-cyan-400 transition-colors" href="#">Terms of Service</a>
                    <a class="hover:text-cyan-400 transition-colors" href="#">Security Architecture</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- END: MainFooter -->
    <!-- BEGIN: StickyCookieBanner -->
    <div class="fixed bottom-4 right-4 max-w-sm z-50 p-4 rounded-xl bg-[#091122]/95 border border-cyan-500/30 backdrop-blur-md shadow-2xl flex flex-col gap-3 text-xs"
        data-purpose="cookie-consent" id="cookie-banner">
        <div class="flex items-start gap-2 text-slate-300">
            <svg class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewbox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>
            <p>
                We use essential cookies to ensure our high-fidelity platform functions as intended. View our <a
                    class="text-cyan-400 underline" href="#">Cookie Policy</a> for details.
            </p>
        </div>
        <div class="flex items-center justify-end gap-2">
            <button class="px-3 py-1 text-slate-400 hover:text-white transition-colors"
                onclick="document.getElementById('cookie-banner').style.display='none'">
                Preferences
            </button>
            <button
                class="px-4 py-1.5 rounded-lg bg-cyan-400 text-slate-950 font-bold hover:bg-cyan-300 transition-colors"
                onclick="document.getElementById('cookie-banner').style.display='none'">
                Accept All
            </button>
        </div>
    </div>
    <!-- END: StickyCookieBanner -->
    <!-- Current Year Script -->
    <script data-purpose="dynamic-year">
        const yr = document.getElementById('year');
        if (yr) yr.innerText = new Date().getFullYear();
    </script>
</body>

</html>
