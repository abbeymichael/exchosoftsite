<?php

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component {
    public BlogPost $post;

    public function mount(string $slug): void
    {
        $this->post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        // Increment views
        $this->post->increment('views');
    }

    public function render(): \Illuminate\View\View
    {
        return view('pages.site.blog-post')->title($this->post->title . ' — ExchoSoft Blog');
    }
}; ?>

<div>
    <style>
        /* ── BLOG POST DETAIL ── */
        .bp-banner {
            position: relative;
            height: 70vh;
            min-height: 420px;
            max-height: 640px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            background: var(--navy);
        }

        .bp-banner img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
        }

        .bp-banner-radar {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 184, 219, 0.08) 50%, transparent 100%);
            animation: bp-sweep 4s infinite linear;
            pointer-events: none;
        }

        @keyframes bp-sweep {
            from {
                transform: translateX(-100%)
            }

            to {
                transform: translateX(100%)
            }
        }

        .bp-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--navy) 0%, rgba(13, 33, 55, 0.4) 50%, transparent 100%);
        }

        .bp-banner-content {
            position: relative;
            z-index: 10;
            padding: 0 6rem 3rem;
            width: 100%;
            max-width: 1100px;
        }

        .bp-banner-pill {
            display: inline-block;
            padding: 0.3rem 0.9rem;
            border-radius: 4px;
            background: var(--cyan);
            color: white;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }

        .bp-banner h1 {
            font-family: var(--font-display);
            font-size: clamp(1.6rem, 3.5vw, 2.8rem);
            font-weight: 800;
            color: white;
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
        }

        .bp-banner-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.25rem;
        }

        .bp-banner-meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.65);
        }

        .bp-banner-meta-item svg {
            opacity: 0.7;
        }

        /* MAIN CONTENT AREA */
        .bp-body {
            max-width: 1440px;
            margin: 0 auto;
            padding: 4rem 6rem;
            display: grid;
            grid-template-columns: 1fr 3fr 1.3fr;
            gap: 3rem;
            position: relative;
        }

        /* TOC sidebar */
        .bp-toc {
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        .bp-toc-inner {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            border: 1.5px solid rgba(0, 184, 219, 0.18);
            border-radius: 14px;
            padding: 1.75rem;
        }

        .bp-toc h3 {
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 1.25rem;
        }

        .bp-toc ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .bp-toc ul li {
            margin-bottom: 0.75rem;
        }

        .bp-toc ul li a {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-decoration: none;
            display: block;
            padding-left: 0.75rem;
            transition: color 0.2s, border-color 0.2s;
            border-left: 2px solid transparent;
        }

        .bp-toc ul li a:hover,
        .bp-toc ul li a.active {
            color: var(--cyan);
            border-left-color: var(--cyan);
        }

        .bp-toc-share {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .bp-toc-share-lbl {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .bp-toc-share-btns {
            display: flex;
            gap: 0.5rem;
        }

        .bp-share-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .bp-share-btn:hover {
            background: var(--cyan);
            border-color: var(--cyan);
        }

        .bp-share-btn:hover svg {
            stroke: white;
        }

        /* ARTICLE */
        .bp-article {
            background: white;
            border-radius: 14px;
            padding: 3rem 3.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        }

        .bp-article-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .bp-article-cat {
            background: rgba(0, 184, 219, 0.08);
            color: var(--cyan-deep);
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .bp-article-date {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .bp-article-sep {
            color: var(--border);
        }

        .bp-author-bar {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1.5rem 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            margin-bottom: 2.5rem;
        }

        .bp-author-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            border: 3px solid var(--ice);
        }

        .bp-author-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .bp-author-role {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
            letter-spacing: 0.04em;
        }

        .bp-cover {
            width: 100%;
            border-radius: 12px;
            aspect-ratio: 16/9;
            object-fit: cover;
            margin-bottom: 2.5rem;
            transition: filter 0.3s;
        }

        .bp-cover:hover {
            filter: brightness(1.05) contrast(1.03);
        }

        .bp-prose {
            color: var(--text-secondary);
            line-height: 1.9;
            font-size: 1rem;
        }

        .bp-prose h1,
        .bp-prose h2,
        .bp-prose h3 {
            font-family: var(--font-display);
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.02em;
            margin: 2.5rem 0 0.85rem;
        }

        .bp-prose h2 {
            font-size: 1.5rem;
        }

        .bp-prose h3 {
            font-size: 1.15rem;
        }

        .bp-prose p {
            margin-bottom: 1.4rem;
        }

        .bp-prose ul,
        .bp-prose ol {
            padding-left: 1.5rem;
            margin-bottom: 1.4rem;
        }

        .bp-prose li {
            margin-bottom: 0.5rem;
            line-height: 1.8;
        }

        .bp-prose blockquote {
            border-left: 4px solid var(--cyan);
            padding: 1rem 1.5rem;
            margin: 2rem 0;
            background: var(--ice);
            border-radius: 0 10px 10px 0;
            font-style: italic;
        }

        .bp-prose blockquote p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 1.05rem;
        }

        .bp-prose code {
            background: var(--ice);
            padding: 0.15rem 0.5rem;
            border-radius: 5px;
            font-size: 0.88em;
            color: var(--cyan-deep);
        }

        .bp-prose pre {
            background: var(--navy);
            color: var(--sky);
            padding: 1.5rem;
            border-radius: 12px;
            overflow-x: auto;
            margin-bottom: 1.4rem;
        }

        .bp-prose pre code {
            background: none;
            color: inherit;
            padding: 0;
        }

        .bp-prose a {
            color: var(--cyan);
            text-decoration: underline;
        }

        .bp-prose strong {
            color: var(--navy);
            font-weight: 700;
        }

        .bp-empty {
            background: var(--ice);
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 4rem 2rem;
            text-align: center;
            color: var(--text-muted);
        }

        .bp-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            transition: color 0.2s;
        }

        .bp-back-link:hover {
            color: var(--cyan);
        }

        /* RIGHT SIDEBAR */
        .bp-sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .bp-sidebar-related h3 {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bp-related-card {
            display: block;
            text-decoration: none;
            margin-bottom: 1.25rem;
        }

        .bp-related-card:hover .bp-related-img img {
            transform: scale(1.05);
        }

        .bp-related-img {
            overflow: hidden;
            border-radius: 10px;
            aspect-ratio: 16/9;
        }

        .bp-related-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .bp-related-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--navy);
            margin-top: 0.6rem;
            line-height: 1.45;
            transition: color 0.2s;
        }

        .bp-related-card:hover .bp-related-title {
            color: var(--cyan-dark);
        }

        .bp-cta-widget {
            background: var(--navy);
            border-radius: 14px;
            padding: 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .bp-cta-widget::before {
            content: '';
            position: absolute;
            top: -2rem;
            right: -2rem;
            width: 8rem;
            height: 8rem;
            background: rgba(0, 184, 219, 0.15);
            border-radius: 50%;
            filter: blur(30px);
            pointer-events: none;
        }

        .bp-cta-widget h4 {
            font-family: var(--font-display);
            font-size: 1.05rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .bp-cta-widget p {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 1.25rem;
            position: relative;
            line-height: 1.65;
        }

        .bp-cta-widget a {
            display: block;
            width: 100%;
            padding: 0.85rem;
            border-radius: 10px;
            text-align: center;
            background: var(--cyan);
            color: white;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.82rem;
            text-decoration: none;
            transition: background 0.2s;
            position: relative;
        }

        .bp-cta-widget a:hover {
            background: var(--cyan-dark);
        }

        /* FOOTER CTA */
        .bp-cta-strip {
            background: var(--ice);
            padding: 5rem 6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .bp-cta-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(0, 184, 219, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
            pointer-events: none;
        }

        .bp-cta-strip h2 {
            font-family: var(--font-display);
            font-size: clamp(1.6rem, 2.5vw, 2.2rem);
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 0.75rem;
            position: relative;
        }

        .bp-cta-strip p {
            font-size: 1rem;
            color: var(--text-secondary);
            max-width: 520px;
            margin: 0 auto 2.5rem;
            position: relative;
        }

        .bp-cta-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            position: relative;
        }

        .bp-cta-btn-primary {
            padding: 0.9rem 2.5rem;
            border-radius: 10px;
            background: var(--navy);
            color: white;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .bp-cta-btn-primary:hover {
            background: var(--cyan);
            transform: translateY(-2px);
        }

        .bp-cta-btn-outline {
            padding: 0.9rem 2.5rem;
            border-radius: 10px;
            border: 2px solid var(--navy);
            color: var(--navy);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .bp-cta-btn-outline:hover {
            background: var(--navy);
            color: white;
        }

        @media (max-width: 1200px) {
            .bp-body {
                grid-template-columns: 1fr 2fr;
                padding: 3rem 3rem;
            }

            .bp-toc {
                display: none;
            }
        }

        @media (max-width: 1024px) {
            .bp-banner-content {
                padding: 0 2rem 2rem;
            }

            .bp-body {
                grid-template-columns: 1fr;
                padding: 2.5rem 2rem;
            }

            .bp-toc,
            .bp-sidebar {
                display: none;
            }

            .bp-article {
                padding: 2rem 1.5rem;
            }

            .bp-cta-strip {
                padding: 3.5rem 2rem;
            }
        }

        @media (max-width: 640px) {
            .bp-banner {
                height: 55vh;
            }

            .bp-banner h1 {
                font-size: clamp(1.3rem, 6vw, 1.8rem);
            }

            .bp-article {
                padding: 1.5rem 1rem;
            }

            .bp-cta-strip {
                padding: 2.5rem 1.25rem;
            }
        }
    </style>

    {{-- ── BANNER ── --}}
    <section class="bp-banner">
        @if ($post->cover_image)
            <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}">
        @endif
        <div class="bp-banner-radar"></div>
        <div class="bp-banner-overlay"></div>
        <div class="bp-banner-content">
            <span class="bp-banner-pill">{{ ucfirst($post->category ?? 'Blog') }}</span>
            <h1>{{ $post->title }}</h1>
            <div class="bp-banner-meta">
                <div class="bp-banner-meta-item">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    {{ $post->published_at?->format('F d, Y') }}
                </div>
                <div class="bp-banner-meta-item">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    {{ $post->read_time_minutes }} min read
                </div>
            </div>
        </div>
    </section>

    {{-- ── MAIN LAYOUT ── --}}
    <div class="bp-body"
        style="background:radial-gradient(circle at 1px 1px,rgba(0,184,219,0.06) 1px,transparent 1px);background-size:28px 28px;">

        {{-- Left TOC --}}
        <aside class="bp-toc">
            <div class="bp-toc-inner">
                <h3>Outline</h3>
                <ul id="bp-toc-list">
                    <li><a href="#" class="active">Introduction</a></li>
                    <li><a href="#">Key Concepts</a></li>
                    <li><a href="#">Implementation</a></li>
                    <li><a href="#">Conclusion</a></li>
                </ul>
                <div class="bp-toc-share">
                    <div class="bp-toc-share-lbl">Share Insights</div>
                    <div class="bp-toc-share-btns">
                        <button class="bp-share-btn"><svg width="14" height="14" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3" />
                                <circle cx="6" cy="12" r="3" />
                                <circle cx="18" cy="19" r="3" />
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                            </svg></button>
                        <button class="bp-share-btn"><svg width="14" height="14" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                            </svg></button>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Article --}}
        <article class="bp-article">
            <div class="bp-article-meta">
                @if ($post->category)
                    <span class="bp-article-cat">{{ $post->category }}</span>
                @endif
                <span class="bp-article-date">{{ $post->published_at?->format('d M Y') }}</span>
                <span class="bp-article-sep">·</span>
                <span class="bp-article-date">{{ $post->read_time_minutes }} min read</span>
                <span class="bp-article-sep">·</span>
                <span class="bp-article-date">{{ number_format($post->views) }} views</span>
            </div>

            <div class="bp-author-bar">
                <div class="bp-author-avatar">{{ strtoupper(substr($post->author?->name ?? 'EC', 0, 2)) }}</div>
                <div>
                    <div class="bp-author-name">{{ $post->author?->name ?? 'Exchosoft Engineering Team' }}</div>
                    <div class="bp-author-role">Exchosoft Consult · Principal Systems Architect</div>
                </div>
            </div>

            @if ($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="bp-cover">
            @endif

            @if ($post->content)
                <div class="bp-prose min-w-0 prose max-w-none
           prose-pre:overflow-x-auto
           prose-pre:max-w-auto
           prose-img:max-w-full">{!! $post->content !!}</div>
            @else
                <div class="bp-empty">
                    <p style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">Content coming soon</p>
                    <p style="font-size:0.875rem;">This post is being written. Check back soon!</p>
                </div>
            @endif

            <a href="{{ route('site.blog') }}" wire:navigate class="bp-back-link">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                Back to Blog
            </a>
        </article>

        {{-- Right sidebar --}}
        <aside class="bp-sidebar">
            <div class="bp-sidebar-related">
                <h3>
                    <span class="material-symbols-outlined"
                        style="font-size:1.1rem;color:var(--cyan);">rebase_edit</span>
                    Related Insights
                </h3>
                {{-- Placeholder related posts - ideally would be passed from component --}}
                <div
                    style="font-size:0.82rem;color:var(--text-muted);padding:1rem;background:var(--ice);border-radius:10px;text-align:center;">
                    More articles coming soon
                </div>
            </div>
            <div class="bp-cta-widget">
                <h4>Complex Architecture?</h4>
                <p>Schedule a briefing with our principal architects to review your technical challenges.</p>
                <a href="{{ route('site.consulting') }}" wire:navigate>Talk to an Architect</a>
            </div>
        </aside>
    </div>

    {{-- ── FOOTER CTA ── --}}
    <section class="bp-cta-strip">
        <h2>Build Resilience From Here.</h2>
        <p>Industrial reliability isn't a feature; it's a foundation. Let's design systems that never quit, even when
            the network does.</p>
        <div class="bp-cta-btns">
            <a href="{{ route('site.consulting') }}" wire:navigate class="bp-cta-btn-primary">Schedule
                Consultation</a>
            <a href="{{ route('site.case-studies') }}" wire:navigate class="bp-cta-btn-outline">View Case Studies</a>
        </div>
    </section>

</div>
