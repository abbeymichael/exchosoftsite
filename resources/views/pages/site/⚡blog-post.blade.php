<?php

use App\Models\BlogPost;
use League\CommonMark\CommonMarkConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
}; ?>

<div>
    <style>
        /* ── bp-prose: markdown-rendered content ── */
        .bp-prose { word-break: break-word; overflow-wrap: break-word; }

        /* Headings */
        .bp-prose h1, .bp-prose h2, .bp-prose h3,
        .bp-prose h4, .bp-prose h5, .bp-prose h6 {
            font-size: revert;
            font-weight: 700;
            line-height: 1.3;
            margin-top: 1.75em;
            margin-bottom: 0.5em;
            word-break: break-word;
        }

        /* Paragraphs & inline */
        .bp-prose p  { margin-bottom: 1.25em; line-height: 1.75; }
        .bp-prose a  { color: var(--cyan); text-decoration: underline; word-break: break-all; }
        .bp-prose strong { font-weight: 700; }
        .bp-prose em     { font-style: italic; }

        /* Lists */
        .bp-prose ul, .bp-prose ol { padding-left: 1.4em; margin-bottom: 1.25em; }
        .bp-prose ul { list-style: disc; }
        .bp-prose ol { list-style: decimal; }
        .bp-prose li { margin-bottom: 0.4em; line-height: 1.7; }

        /* Inline code */
        .bp-prose code {
            background: rgba(0,184,219,0.08);
            color: var(--cyan-deep, #0077a8);
            border-radius: 4px;
            padding: 0.15em 0.4em;
            font-size: 0.85em;
            word-break: break-all;
        }

        /* Code blocks — scroll horizontally, never break the layout */
        .bp-prose pre {
            background: #0d2137;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 1.25em 1.5em;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1.5em;
            font-size: 0.85em;
            line-height: 1.65;
            max-width: 100%;
            white-space: pre;
        }
        .bp-prose pre code {
            background: none;
            color: inherit;
            padding: 0;
            font-size: inherit;
            word-break: normal;
        }

        /* Blockquote */
        .bp-prose blockquote {
            border-left: 4px solid var(--cyan);
            margin: 1.5em 0;
            padding: 0.75em 1.25em;
            background: rgba(0,184,219,0.05);
            border-radius: 0 8px 8px 0;
            color: var(--text-secondary, #4a5568);
            font-style: italic;
        }

        /* Tables — scroll on small screens, never overflow */
        .bp-prose table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5em;
            font-size: 0.875em;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .bp-prose th, .bp-prose td {
            border: 1px solid var(--border, #e2e8f0);
            padding: 0.6em 0.9em;
            text-align: left;
            white-space: nowrap;
        }
        .bp-prose th { background: rgba(0,184,219,0.07); font-weight: 700; }
        .bp-prose tr:nth-child(even) td { background: rgba(0,0,0,0.02); }

        /* Images */
        .bp-prose img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.25em 0; display: block; }

        /* HR */
        .bp-prose hr { border: none; border-top: 1px solid var(--border, #e2e8f0); margin: 2em 0; }
    </style>


    {{-- ── BANNER ── --}}
    <section class="relative flex items-end overflow-hidden bg-[var(--navy)]"
             style="height:70vh; min-height:420px; max-height:640px;">

        @if ($post->cover_image)
            <img src="{{ asset('storage/' . $post->cover_image) }}"
                 alt="{{ $post->title }}"
                 class="absolute inset-0 h-full w-full object-cover opacity-60">
        @endif

        <div class="bp-radar"></div>

        <div class="absolute inset-0"
             style="background: linear-gradient(to top, var(--navy) 0%, rgba(13,33,55,0.4) 50%, transparent 100%)"></div>

        <div class="relative z-10 w-full max-w-[1100px] px-4 pb-8 sm:px-6 md:px-10 lg:px-8 lg:pb-8 xl:px-24 xl:pb-12">
            <span class="mb-5 inline-block rounded bg-[var(--cyan)] px-3.5 py-1.5 text-[0.72rem] font-bold uppercase tracking-[0.1em] text-white">
                {{ ucfirst($post->category ?? 'Blog') }}
            </span>
            <h1 class="mb-6 font-[var(--font-display)] font-extrabold leading-tight tracking-[-0.02em] text-white"
                style="font-size: clamp(1.6rem, 3.5vw, 2.8rem);">
                {{ $post->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-5">
                <div class="flex items-center gap-1.5 text-[0.8rem] text-white/65">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="opacity-70">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    {{ $post->published_at?->format('F d, Y') }}
                </div>
                <div class="flex items-center gap-1.5 text-[0.8rem] text-white/65">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="opacity-70">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    {{ $post->read_time_minutes }} min read
                </div>
            </div>
        </div>
    </section>

    {{-- ── MAIN LAYOUT ── --}}
    <div class="relative mx-auto max-w-[1440px] gap-8
                grid grid-cols-1 px-4 py-6
                sm:px-5 sm:py-8
                md:px-6 md:py-10
                lg:grid-cols-1 lg:px-8 lg:py-10
                xl:grid-cols-[1fr_2fr] xl:gap-10 xl:px-12 xl:py-14
                2xl:grid-cols-[1fr_3fr_1.3fr] 2xl:gap-12 2xl:px-24 2xl:py-16"
         style="background-image: radial-gradient(circle at 1px 1px, rgba(0,184,219,0.06) 1px, transparent 1px); background-size: 28px 28px;">

        {{-- ── Left TOC ── --}}
        <aside class="sticky top-20 hidden h-fit 2xl:block">
            <div class="rounded-2xl border border-[rgba(0,184,219,0.18)] bg-white/65 p-7 backdrop-blur-xl">
                <h3 class="mb-5 font-[var(--font-display)] text-base font-bold text-[var(--navy)]">Outline</h3>
                <ul class="m-0 list-none p-0" id="bp-toc-list">
                    @foreach(['Introduction','Key Concepts','Implementation','Conclusion'] as $i => $item)
                        <li class="mb-3">
                            <a href="#"
                               class="bp-toc-link block border-l-2 border-transparent pl-3 text-[0.82rem] text-[var(--text-muted)] no-underline transition-colors duration-200 {{ $i === 0 ? 'active' : '' }}">
                                {{ $item }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-8 border-t border-[var(--border)] pt-6">
                    <div class="mb-3 text-[0.7rem] font-bold uppercase tracking-[0.1em] text-[var(--text-muted)]">Share Insights</div>
                    <div class="flex gap-2">
                        <button class="bp-share-btn flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border border-[var(--border)] bg-transparent transition-colors">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                            </svg>
                        </button>
                        <button class="bp-share-btn flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border border-[var(--border)] bg-transparent transition-colors">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── Article ── --}}
        <article class="rounded-2xl border border-[var(--border)] bg-white p-4 shadow-[0_4px_24px_rgba(0,0,0,0.04)]
                        sm:p-6 md:p-8 lg:p-10 xl:p-14">

            {{-- Meta row --}}
            <div class="mb-8 flex flex-wrap items-center gap-3">
                @if ($post->category)
                    <span class="rounded-full bg-[rgba(0,184,219,0.08)] px-3 py-1 text-[0.72rem] font-bold uppercase tracking-[0.06em] text-[var(--cyan-deep)]">
                        {{ $post->category }}
                    </span>
                @endif
                <span class="text-[0.8rem] text-[var(--text-muted)]">{{ $post->published_at?->format('d M Y') }}</span>
                <span class="text-[var(--border)]">·</span>
                <span class="text-[0.8rem] text-[var(--text-muted)]">{{ $post->read_time_minutes }} min read</span>
                <span class="text-[var(--border)]">·</span>
                <span class="text-[0.8rem] text-[var(--text-muted)]">{{ number_format($post->views) }} views</span>
            </div>

            {{-- Author bar --}}
            <div class="mb-10 flex items-center gap-5 border-b border-t border-[var(--border)] py-6">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full border-4 border-[var(--ice)] bg-[var(--navy)] font-[var(--font-display)] text-base font-extrabold text-white">
                    {{ strtoupper(substr($post->author?->name ?? 'EC', 0, 2)) }}
                </div>
                <div>
                    <div class="text-[0.95rem] font-bold text-[var(--text-primary)]">
                        {{ $post->author?->name ?? 'Exchosoft Engineering Team' }}
                    </div>
                    <div class="mt-0.5 text-[0.78rem] tracking-[0.04em] text-[var(--text-muted)]">
                        Exchosoft Consult · Principal Systems Architect
                    </div>
                </div>
            </div>

            {{-- Cover image --}}
            @if ($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}"
                     alt="{{ $post->title }}"
                     class="mb-10 w-full rounded-xl object-cover transition-[filter] duration-300 hover:brightness-105 hover:contrast-[1.03]"
                     style="aspect-ratio: 16/9;">
            @endif

            {{-- Content --}}
            @if ($post->content)
                <div class="bp-prose max-w-full overflow-x-auto">{!! $post->content !!}</div>
            @else
                <div class="rounded-xl border-2 border-dashed border-[var(--border)] bg-[var(--ice)] px-8 py-16 text-center text-[var(--text-muted)]">
                    <p class="mb-2 text-base font-semibold">Content coming soon</p>
                    <p class="text-sm">This post is being written. Check back soon!</p>
                </div>
            @endif

            {{-- Back link --}}
            <a href="{{ route('site.blog') }}" wire:navigate
               class="mt-12 inline-flex w-full items-center gap-2 border-t border-[var(--border)] pt-8 text-[0.85rem] text-[var(--text-muted)] no-underline transition-colors duration-200 hover:text-[var(--cyan)]">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Blog
            </a>
        </article>

        {{-- ── Right Sidebar ── --}}
        <aside class="flex flex-col gap-6 2xl:sticky 2xl:top-20 2xl:h-fit">

            {{-- Related --}}
            <div>
                <h3 class="mb-5 flex items-center gap-2 font-[var(--font-display)] text-[1.1rem] font-bold text-[var(--navy)]">
                    <span class="material-symbols-outlined text-[1.1rem] text-[var(--cyan)]">rebase_edit</span>
                    Related Insights
                </h3>
                <div class="rounded-xl bg-[var(--ice)] p-4 text-center text-[0.82rem] text-[var(--text-muted)]">
                    More articles coming soon
                </div>
            </div>

            {{-- CTA widget --}}
            <div class="relative overflow-hidden rounded-2xl bg-[var(--navy)] p-7">
                <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-[rgba(0,184,219,0.15)]"
                     style="filter: blur(30px)"></div>
                <h4 class="relative mb-3 font-[var(--font-display)] text-[1.05rem] font-bold text-white">
                    Complex Architecture?
                </h4>
                <p class="relative mb-5 text-[0.82rem] leading-[1.65] text-white/55">
                    Schedule a briefing with our principal architects to review your technical challenges.
                </p>
                <a href="{{ route('site.consulting') }}" wire:navigate
                   class="relative block w-full rounded-xl bg-[var(--cyan)] py-3.5 text-center font-[var(--font-display)] text-[0.82rem] font-bold text-white no-underline transition-colors duration-200 hover:bg-[var(--cyan-dark)]">
                    Talk to an Architect
                </a>
            </div>
        </aside>
    </div>

    {{-- ── FOOTER CTA ── --}}
    <section class="relative overflow-hidden border-b border-t border-[var(--border)] px-5 py-10 text-center
                    sm:px-8 sm:py-12 lg:px-8 lg:py-14 xl:px-24 xl:py-20"
             style="background-color: var(--ice); background-image: radial-gradient(circle, rgba(0,184,219,0.08) 1px, transparent 1px); background-size: 28px 28px;">
        <h2 class="relative mb-3 font-[var(--font-display)] text-2xl font-extrabold text-[var(--navy)] sm:text-3xl md:text-4xl">
            Build Resilience From Here.
        </h2>
        <p class="relative mx-auto mb-10 max-w-[520px] text-base text-[var(--text-secondary)]">
            Industrial reliability isn't a feature; it's a foundation. Let's design systems that never quit, even when the network does.
        </p>
        <div class="relative flex flex-wrap justify-center gap-4">
            <a href="{{ route('site.consulting') }}" wire:navigate
               class="rounded-xl bg-[var(--navy)] px-10 py-3.5 font-[var(--font-display)] text-[0.9rem] font-bold text-white no-underline transition-all duration-200 hover:-translate-y-0.5 hover:bg-[var(--cyan)]">
                Schedule Consultation
            </a>
            <a href="{{ route('site.case-studies') }}" wire:navigate
               class="rounded-xl border-2 border-[var(--navy)] px-10 py-3.5 font-[var(--font-display)] text-[0.9rem] font-bold text-[var(--navy)] no-underline transition-all duration-200 hover:bg-[var(--navy)] hover:text-white">
                View Case Studies
            </a>
        </div>
    </section>
</div>
