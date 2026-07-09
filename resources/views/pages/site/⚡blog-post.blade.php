<?php

use App\Models\BlogPost;
use League\CommonMark\CommonMarkConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.site')] class extends Component {
    public BlogPost $post;

    // Exposed to layout as $title
    public string $title = '';

    public function mount(string $slug): void
    {
        $this->post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $this->post->increment('views');

        $this->title = $this->post->title . ' — ExchoSoft Blog';

        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $this->post->content = $converter->convert($this->post->content ?? '')->getContent();
    }

    public function render()
    {
        return $this->view()
            ->title($this->title);
    }
}; ?>

<div class="min-h-screen bg-slate-50/60 antialiased selection:bg-[#00b8db]/20 selection:text-[#008ba6]">
    <style>
        /* ── Typographic Foundations for Rendered Markdown ── */
        .bp-prose { word-break: break-word; overflow-wrap: break-word; color: #334155; font-size: 1.05rem; }
        .bp-prose h1, .bp-prose h2, .bp-prose h3, .bp-prose h4 {
            color: #0f172a; font-weight: 800; line-height: 1.3; margin-top: 2.5rem; margin-bottom: 1rem; font-family: system-ui, sans-serif;
        }
        .bp-prose h1 { font-size: 2rem; tracking: -0.025em; }
        .bp-prose h2 { font-size: 1.6rem; border-b: 1px solid #f1f5f9; padding-bottom: 0.5rem; tracking: -0.02em; }
        .bp-prose h3 { font-size: 1.3rem; tracking: -0.01em; }
        .bp-prose p { margin-bottom: 1.5rem; line-height: 1.85; color: #475569; }
        .bp-prose a { color: #00b8db; text-decoration: none; font-weight: 600; border-bottom: 1px solid transparent; transition: all 0.2s; }
        .bp-prose a:hover { color: #008ba6; border-color: #008ba6; }
        .bp-prose strong { color: #0f172a; font-weight: 700; }

        /* Lists formatting */
        .bp-prose ul, .bp-prose ol { padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .bp-prose ul { list-style-type: disc; }
        .bp-prose ol { list-style-type: decimal; }
        .bp-prose li { margin-bottom: 0.6rem; line-height: 1.8; }

        /* Code Syntax and Blocks Styling */
        .bp-prose code { background: rgba(0, 184, 219, 0.08); color: #0077a8; border-radius: 6px; padding: 0.2rem 0.45rem; font-size: 0.875em; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 500; }
        .bp-prose pre { background: #0f172a; color: #e2e8f0; border-radius: 16px; padding: 1.5rem; overflow-x: auto; margin-bottom: 2rem; font-size: 0.9rem; line-height: 1.7; border: 1px solid #1e293b; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .bp-prose pre code { background: none; color: inherit; padding: 0; font-size: inherit; font-family: inherit; font-weight: inherit; }

        /* Tables Restyling */
        .bp-prose table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 0.95rem; display: block; overflow-x: auto; }
        .bp-prose th, .bp-prose td { border: 1px solid #e2e8f0; padding: 0.85rem 1.1rem; text-align: left; }
        .bp-prose th { background: rgba(0, 184, 219, 0.03); font-weight: 700; color: #0f172a; }
        .bp-prose tr:nth-child(even) td { background: #f8fafc; }

        /* Blockquotes Structural Alignment */
        .bp-prose blockquote { border-left: 4px solid #00b8db; margin: 2.5rem 0; padding: 1rem 1.75rem; background: linear-gradient(to right, rgba(0, 184, 219, 0.04), transparent); border-radius: 0 16px 16px 0; color: #475569; font-style: italic; font-size: 1.125rem; }
        .bp-prose img { max-width: 100%; height: auto; border-radius: 20px; margin: 2.5rem 0; box-shadow: 0 20px 40px -15px rgba(15,23,42,0.08); border: 1px solid #e2e8f0; }

        /* Keyframes for Banner Components */
        @keyframes subtleGlow {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50% { opacity: 0.25; transform: scale(1.05); }
        }
        .animate-banner-glow { animation: subtleGlow 8s ease-in-out infinite; }
    </style>

    {{-- ── IMMERSIVE UPGRADED HERO BANNER LAYER ── --}}
    <header class="relative overflow-hidden bg-slate-950 py-16 lg:py-24 border-b border-slate-800">
        <!-- Visual Grid Overlay Elements -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute -left-20 top-0 h-[450px] w-[450px] rounded-full bg-[#00b8db]/10 blur-[120px] pointer-events-none animate-banner-glow"></div>
        <div class="absolute -right-20 bottom-0 h-[350px] w-[350px] rounded-full bg-cyan-500/5 blur-[100px] pointer-events-none animate-banner-glow" style="animation-delay: 2s;"></div>

        <div class="relative mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-12 xl:px-20 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-8 items-center">

            {{-- Title, Breadcrumbs, and Meta Aggregation Stack --}}
            <div class="space-y-6 max-w-4xl">
                <!-- Inline Functional Breadcrumbs -->

                <div class="space-y-4">


                    <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl md:text-5xl lg:leading-[1.15]">
                        {!! str_replace($post->title, '<span class="text-[#00b8db]">' . e($post->title) . '</span>', $post->title) !!}
                    </h1>
                </div>

                {{-- Banner Metadata Layout Chips --}}
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    @if($post->author_name ?? false)
                        <div class="inline-flex items-center gap-2 text-xs font-medium px-3.5 py-2 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300 backdrop-blur-md">
                            <span class="material-symbols-outlined text-sm text-[#00b8db]">person</span>
                            <span>{{ $post->author_name }}</span>
                        </div>
                    @endif
                    @if($post->published_at)
                        <div class="inline-flex items-center gap-2 text-xs font-medium px-3.5 py-2 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300 backdrop-blur-md">
                            <span class="material-symbols-outlined text-sm text-[#00b8db]">calendar_month</span>
                            <span>{{ $post->published_at->format("M d, Y") }}</span>
                        </div>
                    @endif
                    @if($post->read_time_minutes)
                        <div class="inline-flex items-center gap-2 text-xs font-medium px-3.5 py-2 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300 backdrop-blur-md">
                            <span class="material-symbols-outlined text-sm text-[#00b8db]">schedule</span>
                            <span>{{ $post->read_time_minutes }} min read</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Decorative Architectural Abstract Isometric Ornament SVG --}}
            <div class="hidden lg:block relative justify-self-end">
                <div class="absolute inset-0 bg-gradient-to-tr from-[#00b8db]/20 to-transparent blur-3xl opacity-50 rounded-full"></div>
                <svg class="relative w-52 h-52 opacity-25 pointer-events-none filter drop-shadow-[0_0_15px_rgba(0,184,219,0.2)]" viewBox="0 0 180 180" fill="none">
                    <rect x="20" y="40" width="140" height="100" rx="12" stroke="#00b8db" stroke-width="1.5"/>
                    <rect x="35" y="58" width="110" height="14" rx="4" stroke="#00b8db" stroke-width="1.5" fill="rgba(0,184,219,0.05)"/>
                    <rect x="35" y="82" width="80" height="8" rx="2" fill="#00b8db" opacity="0.7"/>
                    <rect x="35" y="98" width="100" height="8" rx="2" fill="#00b8db" opacity="0.4"/>
                    <circle cx="43" cy="124" r="6" fill="none" stroke="#00b8db" stroke-width="1.5"/>
                    <rect x="58" y="121" width="55" height="6" rx="2" fill="#00b8db" opacity="0.5"/>
                </svg>
            </div>
        </div>
    </header>

    {{-- ── CONTENT AND RUNTIME WORKSPACE ENVIRONMENT ── --}}
    <div class="relative mx-auto max-w-[1450px] gap-8 grid grid-cols-1 px-4 py-8 sm:px-6 sm:py-12 xl:grid-cols-[280px_1fr] xl:gap-10 xl:px-12 2xl:grid-cols-[300px_1fr_320px] 2xl:gap-12 2xl:px-20"
         style="background-image: radial-gradient(circle at 1px 1px, rgba(0,184,219,0.03) 1px, transparent 1px); background-size: 32px 32px;">

        {{-- ── LEFT STICKY SIDEBAR: AUTOMATED TABLE OF CONTENTS ── --}}
        <aside class="sticky top-28 hidden h-fit xl:block order-1">
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 backdrop-blur-xl shadow-sm space-y-5">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-1.5 h-3 bg-[#00b8db] rounded-full"></span> Content Roadmap
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Anchored system structure</p>
                </div>

                {{-- Loaded on initialization via Runtime Intersection Observer script --}}
                <ul class="space-y-1 list-none p-0 border-l border-slate-100" id="bp-toc-list">
                    <li class="text-xs text-slate-400 italic pl-3 py-1 flex items-center gap-2">
                        <span class="w-1 h-1 bg-slate-300 rounded-full animate-pulse"></span> Parsing content data...
                    </li>
                </ul>

                <div class="border-t border-slate-100 pt-4">
                    <div class="mb-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Share Insights</div>
                    <div class="flex gap-2">
                        <button onclick="window.navigator.clipboard.writeText(window.location.href); alert('Insight vector matrix token copied.');"
                                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-[#00b8db] hover:border-[#00b8db]/40 transition-all shadow-sm">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                            </svg>
                        </button>
                        <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-[#00b8db] hover:border-[#00b8db]/40 transition-all shadow-sm">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── CENTRAL LOGICAL ARTICLE STREAM CANVAS ── --}}
        <main class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_4px_30px_rgba(15,23,42,0.015)] sm:p-8 md:p-10 lg:p-12 xl:p-14 order-2">
                           {{-- Full High-Resolution Post Cover Element --}}
            @if ($post->cover_image)
                <div class="mb-10 overflow-hidden rounded-2xl border border-slate-200/70 shadow-sm group">
                    <img src="{{ asset('storage/' . $post->cover_image) }}"
                         alt="{{ $post->title }}"
                         class="w-full object-cover transition-transform duration-700 group-hover:scale-[1.015]"
                         style="aspect-ratio: 16/9; object-position: center;">
                </div>
            @endif

            {{-- Meta Performance Metric Bar --}}
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-6 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-slate-700">Telemetry Status:</span>
                    <span class="inline-flex items-center gap-1.5 text-[#008ba6] font-medium bg-cyan-50 px-2 py-0.5 rounded-md border border-cyan-100">
                        <span class="w-1.5 h-1.5 bg-[#00b8db] rounded-full"></span> Production Node Verified
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm">visibility</span> {{ number_format($post->views) }} logs</span>
                </div>
            </div>


            {{-- Profile Meta Context Section --}}
            <div class="mb-10 flex items-center gap-4 rounded-2xl bg-slate-50/80 border border-slate-100 p-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-slate-900 font-mono text-sm font-black text-white shadow-sm tracking-tighter">
                    {{ strtoupper(substr($post->author?->name ?? $post->author_name ?? 'EC', 0, 2)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">
                        {{ $post->author?->name ?? $post->author_name ?? 'Exchosoft Engineering Team' }}
                    </div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        Exchosoft Systems Group · Principal Solution Architect
                    </div>
                </div>
            </div>



            {{-- Active Output Stream Node --}}
            @if ($post->content)
                <div class="bp-prose max-w-none" id="blog-content-root">
                    {!! $post->content !!}
                </div>
            @else
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-16 text-center text-slate-400">
                    <span class="material-symbols-outlined text-3xl text-slate-300 mb-2">edit_note</span>
                    <p class="text-sm font-bold text-slate-700">Composition Stream Pending</p>
                    <p class="text-xs text-slate-400 mt-1">Telemetry segments for this configuration matrix are currently undergoing compiler deployment optimization.</p>
                </div>
            @endif

            {{-- Component Exit Return Nav --}}
            <div class="mt-12 border-t border-slate-100 pt-6">
                <a href="{{ route('site.blog') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 shadow-sm hover:text-[#00b8db] hover:border-[#00b8db]/40 transition-all">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Return to Knowledge Base
                </a>
            </div>
        </main>

        {{-- ── RIGHT SIDEBAR ACTIONS ── --}}
        <aside class="flex flex-col gap-6 2xl:sticky 2xl:top-28 2xl:h-fit order-3">

            {{-- Related Insights Module --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-[#00b8db]">rebase_edit</span>
                    Adjacent Systems
                </h3>
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 text-center text-xs text-slate-400 italic">
                    Alternative telemetry nodes are configuring runtime pipelines.
                </div>
            </div>

            {{-- Conversion Context Component Box --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 p-6 border border-slate-800 shadow-lg">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-[#00b8db]/15 blur-2xl pointer-events-none"></div>
                <h4 class="relative mb-2 font-bold text-sm text-white tracking-tight">
                    Complex Infrastructure Metrics?
                </h4>
                <p class="relative mb-5 text-xs leading-relaxed text-slate-400">
                    Schedule a structural audit configuration diagnostic session with our technical design core deployment units.
                </p>
                <a href="{{ route('site.consulting') }}" wire:navigate
                   class="relative block w-full rounded-xl bg-[#00b8db] py-2.5 text-center text-xs font-bold text-white shadow-md hover:bg-[#009cb9] transition-all">
                    Initiate Systems Diagnostic
                </a>
            </div>
        </aside>
    </div>

    {{-- ── DEEP FOOTER BRAND ACTION STATEMENT ── --}}
    <section class="relative overflow-hidden border-t border-slate-200 bg-white px-6 py-14 text-center sm:py-20"
             style="background-image: radial-gradient(circle, rgba(0,184,219,0.04) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-2xl mx-auto space-y-4">
            <h2 class="text-2xl font-black text-slate-900 sm:text-3xl tracking-tight">
                Design For Continuous Systems Resiliency.
            </h2>
            <p class="text-sm leading-relaxed text-slate-500 max-w-lg mx-auto">
                Systemic integrity isn't patched at compile-time—it's designed into the architecture. Build fault-tolerant operational frameworks.
            </p>
            <div class="flex flex-wrap justify-center gap-3 pt-4">
                <a href="{{ route('site.consulting') }}" wire:navigate
                   class="rounded-xl bg-slate-900 px-6 py-3.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition-all">
                    Schedule Consultation
                </a>
                <a href="{{ route('site.case-studies') }}" wire:navigate
                   class="rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                    Review Operational History
                </a>
            </div>
        </div>
    </section>

    {{-- ── RUNTIME INTERSECTION OBSERVER FOR TABLE OF CONTENTS ── --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            const contentRoot = document.getElementById('blog-content-root');
            const tocList = document.getElementById('bp-toc-list');
            if (!contentRoot || !tocList) return;

            // Extract native structural subheaders out of converted markdown wrapper
            const headings = contentRoot.querySelectorAll('h2, h3');
            if (headings.length === 0) {
                tocList.innerHTML = '<li class="text-xs text-slate-400 italic pl-3">Flat hierarchy detected.</li>';
                return;
            }

            tocList.innerHTML = ''; // Wipe diagnostic buffer states

            headings.forEach((heading, index) => {
                const headingId = heading.id || `doc-node-link-${index}`;
                heading.id = headingId;

                const listItem = document.createElement('li');
                const isH3 = heading.tagName.toLowerCase() === 'h3';

                const link = document.createElement('a');
                link.href = `#${headingId}`;
                link.innerText = heading.innerText;

                // Indent nested subheadings smoothly
                link.className = isH3
                    ? 'block border-l-2 border-slate-100 pl-6 text-xs text-slate-400 hover:text-[#00b8db] no-underline transition-all py-1.5 truncate max-w-[220px]'
                    : 'block border-l-2 border-slate-100 pl-3 text-xs text-slate-500 hover:text-[#00b8db] no-underline transition-all py-1.5 font-medium truncate max-w-[220px]';

                link.setAttribute('data-toc-anchor', headingId);

                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                listItem.appendChild(link);
                tocList.appendChild(listItem);
            });

            // Tracking viewports positioning via geometric triggers
            const observerOptions = { root: null, rootMargin: '-15% 0px -70% 0px', threshold: 0 };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        document.querySelectorAll('[data-toc-anchor]').forEach(el => {
                            el.classList.remove('text-[#00b8db]', 'border-[#00b8db]', 'font-bold');
                            el.classList.add('text-slate-400', 'border-slate-100');
                        });
                        const activeLink = document.querySelector(`[data-toc-anchor="${entry.target.id}"]`);
                        if (activeLink) {
                            activeLink.classList.remove('text-slate-400', 'border-slate-100');
                            activeLink.classList.add('text-[#00b8db]', 'border-[#00b8db]', 'font-bold');
                        }
                    }
                });
            }, observerOptions);

            headings.forEach(h => observer.observe(h));
        });
    </script>
</div>
