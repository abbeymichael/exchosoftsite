<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Our Services — Exchosoft Consult')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('pages.site.services');
    }
}; ?>

<div>
<style>
        .dot-matrix {
            background-image: radial-gradient(circle, #c4c6cd 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(16px);
            border: 1.5px solid rgba(177, 236, 255, 0.3);
        }

        .data-stream-particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: #4cd9fd;
            border-radius: 50%;
            filter: blur(1px);
            pointer-events: none;
        }

        .radar-container {
            position: relative;
            width: 600px;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radar-sweep-v2 {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(76, 217, 253, 0.4) 0deg, transparent 90deg);
            animation: rotateRadar 6s linear infinite;
            z-index: 2;
        }

        .radar-line {
            position: absolute;
            width: 50%;
            height: 2px;
            background: #4cd9fd;
            top: 50%;
            left: 50%;
            transform-origin: left center;
            box-shadow: 0 0 15px #4cd9fd;
            z-index: 3;
            animation: rotateRadar 6s linear infinite;
        }

        .radar-ring {
            position: absolute;
            border: 1px solid rgba(76, 217, 253, 0.2);
            border-radius: 50%;
        }

        @keyframes rotateRadar {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .service-node {
            position: absolute;
            width: 48px;
            height: 48px;
            background: #ffffff;
            border: 1px solid rgba(0, 103, 124, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .service-node.active {
            border-color: #4cd9fd;
            box-shadow: 0 0 20px rgba(76, 217, 253, 0.4);
            transform: scale(1.1);
            background: #f0faff;
        }

        .service-tooltip {
            position: absolute;
            bottom: -32px;
            left: 50%;
            transform: translateX(-50%);
            background: #000917;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 20;
        }

        .service-node.active .service-tooltip {
            opacity: 1;
        }

        .nav-underline-active {
            position: relative;
        }
        .nav-underline-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #00677c;
            animation: radarUnderline 2s infinite;
        }
        @keyframes radarUnderline {
            0% { transform: scaleX(0); opacity: 1; }
            50% { transform: scaleX(1); opacity: 0.5; }
            100% { transform: scaleX(1); opacity: 0; }
        }
    </style>

<!-- Hero Section with High-Fidelity Radar -->
<section class="relative overflow-hidden min-h-[800px] flex items-center bg-primary pt-20">
<!-- Background Elements -->
<div class="absolute inset-0 dot-matrix opacity-20"></div>
<div class="absolute inset-0 pointer-events-none" id="particle-container"></div>
<div class="relative z-10 max-w-container-max mx-auto px-margin-desktop w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
<div class="text-left">
<div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-container/20 border border-secondary-container/40 rounded-full mb-8">
<span class="material-symbols-outlined text-secondary text-[18px]">engineering</span>
<span class="text-label-md text-secondary font-bold tracking-widest uppercase">Precision Engineering</span>
</div>
<h1 class="font-display-lg text-display-lg text-primary mb-6 leading-tight">
                    <span class="text-white">Consultancy</span> Built for <span class="text-secondary">Real-World Complexity</span>
                </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mb-10">
                    We design and deliver technical architectures that solve the infrastructure and operational challenges of emerging markets.
                </p>
<div class="flex flex-wrap gap-4">
<button class="bg-primary text-on-primary px-8 py-4 rounded-lg font-label-md text-lg hover:scale-[1.02] transition-transform shadow-lg">
                        Explore Our Architecture
                    </button>
<button class="bg-surface-container-high text-primary px-8 py-4 rounded-lg font-label-md text-lg hover:bg-surface-container-highest transition-colors border border-outline-variant/30">
                        View Methodology
                    </button>
</div>
</div>
<!-- Radar Visualization -->
<div class="hidden lg:flex items-center justify-center relative">
<div class="radar-container" id="radar-system">
<!-- Dynamic Rings -->
<div class="radar-ring w-[100px] h-[100px]"></div>
<div class="radar-ring w-[200px] h-[200px]"></div>
<div class="radar-ring w-[300px] h-[300px]"></div>
<div class="radar-ring w-[400px] h-[400px]"></div>
<div class="radar-ring w-[500px] h-[500px]"></div>
<!-- Sweep -->
<div class="radar-sweep-v2"></div>
<div class="radar-line"></div>
<!-- Core Center Icon -->
<div class="w-20 h-20 bg-primary rounded-xl flex items-center justify-center z-20 shadow-2xl border border-secondary/30">
<span class="material-symbols-outlined text-secondary-container text-4xl">hub</span>
</div>
<!-- Service Nodes -->
<!-- Data is handled via JS to calculate positions around the ring -->
</div>
</div>
</div>
<!-- Grid Line Accent -->
<div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-outline-variant/50 to-transparent"></div>
</section>
<!-- Services Section -->
<section class="py-24 bg-surface relative">
<div class="max-w-container-max mx-auto px-margin-desktop">
<div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
<div class="max-w-2xl">
<h2 class="font-headline-xl text-headline-xl text-primary mb-4">Core Service Pillars</h2>
<p class="font-body-md text-on-surface-variant">High-velocity technology meets architectural stability. Our services are engineered to endure.</p>
</div>
<div class="text-right">
<span class="font-code-snippet text-secondary-container bg-primary px-4 py-2 rounded-full">v4.2 STABLE_RELEASE</span>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<!-- Service 1 -->
<div class="glass-card p-10 rounded-xl relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-secondary/5 hover:-translate-y-1">
<div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-[120px] text-secondary">developer_mode</span>
</div>
<div class="flex flex-col h-full">
<span class="material-symbols-outlined text-secondary text-[40px] mb-6">developer_mode</span>
<h3 class="font-headline-lg text-headline-lg text-primary mb-4">Custom Software Development</h3>
<p class="font-body-md text-on-surface-variant mb-8 flex-grow">
                            Focus on offline-first, enterprise-grade desktop and hybrid systems. We build resilient applications that maintain data integrity regardless of connectivity.
                        </p>
<div class="flex items-center gap-4 text-secondary font-bold cursor-pointer group/link">
<span>Learn more</span>
<span class="material-symbols-outlined transition-transform group-hover/link:translate-x-1">arrow_forward</span>
</div>
</div>
</div>
<!-- Service 2 -->
<div class="glass-card p-10 rounded-xl relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-secondary/5 hover:-translate-y-1">
<div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-[120px] text-secondary">query_stats</span>
</div>
<div class="flex flex-col h-full">
<span class="material-symbols-outlined text-secondary text-[40px] mb-6">query_stats</span>
<h3 class="font-headline-lg text-headline-lg text-primary mb-4">Strategic Tech Consulting</h3>
<p class="font-body-md text-on-surface-variant mb-8 flex-grow">
                            Expert guidance on digital transformation and system modernization. We align your technical trajectory with long-term business goals.
                        </p>
<div class="flex items-center gap-4 text-secondary font-bold cursor-pointer group/link">
<span>Learn more</span>
<span class="material-symbols-outlined transition-transform group-hover/link:translate-x-1">arrow_forward</span>
</div>
</div>
</div>
<!-- Service 3 -->
<div class="glass-card p-10 rounded-xl relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-secondary/5 hover:-translate-y-1">
<div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-[120px] text-secondary">account_tree</span>
</div>
<div class="flex flex-col h-full">
<span class="material-symbols-outlined text-secondary text-[40px] mb-6">account_tree</span>
<h3 class="font-headline-lg text-headline-lg text-primary mb-4">System Architecture &amp; Design</h3>
<p class="font-body-md text-on-surface-variant mb-8 flex-grow">
                            Specializing in LAN collaboration, cloud synchronization, and resilient infrastructures designed for architectural permanence.
                        </p>
<div class="flex items-center gap-4 text-secondary font-bold cursor-pointer group/link">
<span>Learn more</span>
<span class="material-symbols-outlined transition-transform group-hover/link:translate-x-1">arrow_forward</span>
</div>
</div>
</div>
<!-- Service 4 -->
<div class="glass-card p-10 rounded-xl relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-secondary/5 hover:-translate-y-1">
<div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-[120px] text-secondary">analytics</span>
</div>
<div class="flex flex-col h-full">
<span class="material-symbols-outlined text-secondary text-[40px] mb-6">analytics</span>
<h3 class="font-headline-lg text-headline-lg text-primary mb-4">Business Process Analysis</h3>
<p class="font-body-md text-on-surface-variant mb-8 flex-grow">
                            Identifying bottlenecks and optimizing workflows with technical solutions that bridge the gap between human operation and machine efficiency.
                        </p>
<div class="flex items-center gap-4 text-secondary font-bold cursor-pointer group/link">
<span>Learn more</span>
<span class="material-symbols-outlined transition-transform group-hover/link:translate-x-1">arrow_forward</span>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Engagement Process Section -->
<section class="py-24 bg-primary text-on-primary relative overflow-hidden">
<div class="absolute inset-0 opacity-10 dot-matrix"></div>
<div class="max-w-container-max mx-auto px-margin-desktop relative z-10">
<div class="text-center mb-20">
<h2 class="font-headline-xl text-headline-xl mb-4">The Methodology</h2>
<p class="font-body-lg text-on-primary-container max-w-2xl mx-auto">A rigorous, four-phase approach to solving industrial-scale technical challenges.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 relative">
<!-- Connector line for desktop -->
<div class="hidden lg:block absolute top-1/2 left-0 w-full h-px bg-on-primary-container/20 -translate-y-1/2"></div>
<!-- Step 1 -->
<div class="relative group">
<div class="bg-primary-container border border-on-primary-container/20 p-8 rounded-xl relative z-20 transition-all duration-300 hover:border-secondary-container">
<div class="w-12 h-12 bg-secondary-container text-primary flex items-center justify-center rounded-lg font-bold mb-6">01</div>
<h4 class="font-headline-md text-headline-md mb-3">Discovery</h4>
<p class="font-body-md text-on-primary-container">In-depth immersion into your environment to identify real-world constraints and operational goals.</p>
</div>
</div>
<!-- Step 2 -->
<div class="relative group">
<div class="bg-primary-container border border-on-primary-container/20 p-8 rounded-xl relative z-20 transition-all duration-300 hover:border-secondary-container">
<div class="w-12 h-12 bg-secondary-container text-primary flex items-center justify-center rounded-lg font-bold mb-6">02</div>
<h4 class="font-headline-md text-headline-md mb-3">Architecture</h4>
<p class="font-body-md text-on-primary-container">Engineering technical blueprints that prioritize resilience, scalability, and structural integrity.</p>
</div>
</div>
<!-- Step 3 -->
<div class="relative group">
<div class="bg-primary-container border border-on-primary-container/20 p-8 rounded-xl relative z-20 transition-all duration-300 hover:border-secondary-container">
<div class="w-12 h-12 bg-secondary-container text-primary flex items-center justify-center rounded-lg font-bold mb-6">03</div>
<h4 class="font-headline-md text-headline-md mb-3">Development</h4>
<p class="font-body-md text-on-primary-container">Agile construction phase with a focus on code quality, security architecture, and performance.</p>
</div>
</div>
<!-- Step 4 -->
<div class="relative group">
<div class="bg-primary-container border border-on-primary-container/20 p-8 rounded-xl relative z-20 transition-all duration-300 hover:border-secondary-container">
<div class="w-12 h-12 bg-secondary-container text-primary flex items-center justify-center rounded-lg font-bold mb-6">04</div>
<h4 class="font-headline-md text-headline-md mb-3">Evolution</h4>
<p class="font-body-md text-on-primary-container">Continuous monitoring and optimization to ensure the system evolves with your organization.</p>
</div>
</div>
</div>
</div>
</section>
<!-- CTA Section -->
<section class="py-32 bg-surface overflow-hidden relative">
<div class="max-w-4xl mx-auto px-margin-mobile text-center relative z-10">
<div class="inline-block p-4 rounded-2xl bg-white shadow-xl mb-12 border border-outline-variant/20">
<img alt="Consulting Team" class="w-full h-64 object-cover rounded-xl" data-alt="A professional team of engineers and consultants gathered around a large digital display in a brightly lit modern office. The scene features high-key lighting and a palette of cool blues and greys. The atmosphere is collaborative and intensely focused on technical problem-solving with a sophisticated, light-mode aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjXox70JJD63DuIvI0Q2sR2oBwkTPzBA6oDcAeU3zS2hO56EUZVSp1p7jd_BL8zuKOVkaQ4NJ9V0Z2ulVylfB004YiZvQa82_u8pzQRzVDyGBpBHlU-p9rU75xEjLBaKSNzG6-8crhui3tsQ4oB61vVTetXwf3AoR2s4UXqw92tQXLNn020voX6_nLZcUlG-YfVxEh_WC04HzL5V6tCtiXZbih2Ob7vOmIPMkQqnbvtfKvpQg7ThcwYcQJ4KrquWhEnvF59brkzEc"/>
</div>
<h2 class="font-display-lg text-display-lg text-primary mb-8">Let's Solve Your Hardest Problem</h2>
<p class="font-body-lg text-on-surface-variant mb-12">Industrial reliability isn't a goal; it's our foundation. Partner with us to build technology that lasts.</p>
<div class="flex flex-col sm:flex-row justify-center gap-6">
<button class="bg-secondary-container text-on-secondary-fixed font-bold px-10 py-5 rounded-lg text-lg shadow-lg hover:scale-105 transition-transform">
                    Schedule an Audit
                </button>
<button class="border-2 border-primary text-primary font-bold px-10 py-5 rounded-lg text-lg hover:bg-primary hover:text-on-primary transition-all">
                    View Case Studies
                </button>
</div>
</div>
<!-- Animated background accent -->
<div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary-container/10 rounded-full blur-3xl"></div>
<div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-container/5 rounded-full blur-3xl"></div>
</section>
<script>
    // Particle background animation
    const container = document.getElementById('particle-container');
    const particleCount = 40;

    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'data-stream-particle';

        const startX = Math.random() * 100;
        const startY = Math.random() * 100;
        const duration = 10 + Math.random() * 20;
        const delay = Math.random() * 5;

        particle.style.left = `${startX}%`;
        particle.style.top = `${startY}%`;
        particle.style.opacity = Math.random() * 0.3;

        container.appendChild(particle);

        particle.animate([
            { transform: 'translate(0, 0)', opacity: 0 },
            { opacity: 0.3, offset: 0.2 },
            { transform: `translate(${(Math.random() - 0.5) * 200}px, ${(Math.random() - 0.5) * 200}px)`, opacity: 0 }
        ], {
            duration: duration * 1000,
            iterations: Infinity,
            delay: delay * 1000,
            easing: 'linear'
        });
    }

    for (let i = 0; i < particleCount; i++) {
        createParticle();
    }

    // Radar Service Nodes Logic
    const services = [
        { name: 'Software', icon: 'developer_mode', radius: 100, angle: 0 },
        { name: 'Consulting', icon: 'query_stats', radius: 150, angle: 60 },
        { name: 'Architecture', icon: 'account_tree', radius: 200, angle: 120 },
        { name: 'Analysis', icon: 'analytics', radius: 250, angle: 180 },
        { name: 'Transformation', icon: 'digital_out_of_home', radius: 200, angle: 240 },
        { name: 'Support', icon: 'support_agent', radius: 150, angle: 300 }
    ];

    const radarContainer = document.getElementById('radar-system');
    const nodes = [];

    services.forEach((service, index) => {
        const node = document.createElement('div');
        node.className = 'service-node';

        // Convert polar to cartesian
        const rad = (service.angle * Math.PI) / 180;
        const x = service.radius * Math.cos(rad);
        const y = service.radius * Math.sin(rad);

        node.style.left = `calc(50% + ${x}px - 24px)`;
        node.style.top = `calc(50% + ${y}px - 24px)`;

        node.innerHTML = `
            <span class="material-symbols-outlined text-secondary text-2xl">${service.icon}</span>
            <div class="service-tooltip font-label-md">${service.name}</div>
        `;

        radarContainer.appendChild(node);
        nodes.push({ element: node, angle: service.angle });
    });

    // Radar Tracking logic
    let currentRadarAngle = 0;
    function updateRadarTracking() {
        currentRadarAngle = (currentRadarAngle + (360 / (6 * 60))) % 360; // 6s duration at 60fps

        nodes.forEach(node => {
            // Check if radar sweep (currentRadarAngle) is passing the node angle
            // Normalize angles to 0-360
            const diff = Math.abs(currentRadarAngle - node.angle);
            const isPassing = diff < 15 || diff > 345;

            if (isPassing) {
                node.element.classList.add('active');
            } else {
                node.element.classList.remove('active');
            }
        });

        requestAnimationFrame(updateRadarTracking);
    }
    updateRadarTracking();

    // Intersection Observer for Scroll Reveals
    const observerOptions = {
        threshold: 0.1
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-10');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.glass-card, .bg-primary-container').forEach(el => {
        el.classList.add('opacity-0', 'translate-y-10', 'transition-all', 'duration-700');
        revealObserver.observe(el);
    });
</script>
</div>
