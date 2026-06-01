<?php

namespace App\Livewire\Concerns;

use App\Models\Page;

/**
 * LoadsPageSeo
 *
 * Mixin for Livewire 4 Volt single-file site page components.
 *
 * Usage:
 *   use App\Livewire\Concerns\LoadsPageSeo;
 *
 *   new class extends Component {
 *       use LoadsPageSeo;
 *
 *       public function mount(): void
 *       {
 *           $this->loadPageSeo('about');
 *       }
 *   }
 *
 * In the template use $this->pageSeo, $this->pageBannerHeading, etc.
 * directly — no render() or view() call required.
 *
 * Public properties exposed (all usable as $this->xxx in Volt templates):
 *   $pageSeo              — the raw Page model (or null)
 *   $title                — resolved <title>
 *   $metaDescription      — resolved <meta name="description">
 *   $metaKeywords         — resolved <meta name="keywords">
 *   $canonicalUrl         — resolved canonical URL
 *   $ogTitle, $ogDescription, $ogImage, $ogType
 *   $twitterCard, $twitterTitle, $twitterDescription, $twitterImage
 *   $schemaMarkup         — JSON-LD array or null
 *   $pageBannerHeading, $pageBannerSubheading
 *   $pageBannerImage, $pageBannerCtaText, $pageBannerCtaUrl
 */
trait LoadsPageSeo
{
    // ── Raw page record ───────────────────────────────────────────────────────
    public ?Page $pageSeo = null;

    // ── <title> ───────────────────────────────────────────────────────────────
    public string $title = '';

    // ── Meta ──────────────────────────────────────────────────────────────────
    public string $metaDescription = '';
    public string $metaKeywords    = '';
    public string $canonicalUrl    = '';

    // ── Open Graph ────────────────────────────────────────────────────────────
    public string $ogTitle       = '';
    public string $ogDescription = '';
    public string $ogImage       = '';
    public string $ogType        = 'website';

    // ── Twitter Card ──────────────────────────────────────────────────────────
    public string $twitterCard        = 'summary_large_image';
    public string $twitterTitle       = '';
    public string $twitterDescription = '';
    public string $twitterImage       = '';

    // ── JSON-LD ───────────────────────────────────────────────────────────────
    public mixed $schemaMarkup = null;

    // ── Banner helpers ────────────────────────────────────────────────────────
    public string $pageBannerHeading    = '';
    public string $pageBannerSubheading = '';
    public string $pageBannerImage      = '';
    public string $pageBannerCtaText    = '';
    public string $pageBannerCtaUrl     = '';

    /**
     * Load the Page record for the given key and populate all SEO public
     * properties so the Volt template can reference them directly via $this->x.
     *
     * Call this inside mount():
     *   $this->loadPageSeo('about', 'About Us — Exchosoft', 'Description…');
     *
     * @param  string       $key                  The page key (e.g. 'about')
     * @param  string|null  $fallbackTitle         Fallback <title>
     * @param  string|null  $fallbackDescription   Fallback meta description
     */
    protected function loadPageSeo(
        string  $key,
        ?string $fallbackTitle       = null,
        ?string $fallbackDescription = null
    ): void {
        $this->pageSeo = Page::where('key', $key)->first();

        $p = $this->pageSeo;

        $this->title           = ($p?->meta_title ?? $p?->title ?? $fallbackTitle) ?? 'Exchosoft Consult';
        $this->metaDescription = ($p?->meta_description ?? $fallbackDescription) ?? '';
        $this->metaKeywords    = $p?->meta_keywords ?? '';
        $this->canonicalUrl    = $p?->canonical_url
            ? (str_starts_with($p->canonical_url, 'http') ? $p->canonical_url : url($p->canonical_url))
            : url()->current();

        $this->ogTitle       = $p?->resolved_og_title       ?? $fallbackTitle       ?? '';
        $this->ogDescription = $p?->resolved_og_description ?? $fallbackDescription ?? '';
        $this->ogImage       = $p?->og_image  ?? '';
        $this->ogType        = $p?->og_type   ?? 'website';

        $this->twitterCard        = $p?->twitter_card              ?? 'summary_large_image';
        $this->twitterTitle       = $p?->resolved_twitter_title       ?? '';
        $this->twitterDescription = $p?->resolved_twitter_description ?? '';
        $this->twitterImage       = $p?->twitter_image ?? $p?->og_image ?? '';

        $this->schemaMarkup = $p?->schema_markup ?? null;

        $this->pageBannerHeading    = $p?->banner_heading    ?? '';
        $this->pageBannerSubheading = $p?->banner_subheading ?? '';
        $this->pageBannerImage      = $p?->banner_image      ?? '';
        $this->pageBannerCtaText    = $p?->banner_cta_text   ?? '';
        $this->pageBannerCtaUrl     = $p?->banner_cta_url    ?? '';
    }

    /**
     * @deprecated  Only kept for backward-compatibility with any page that
     *              still calls render()->with($this->seoViewData(…)).
     *              Prefer calling loadPageSeo() in mount() instead.
     */
    protected function seoViewData(
        ?string $fallbackTitle       = null,
        ?string $fallbackDescription = null
    ): array {
        $p = $this->pageSeo;

        return [
            'title'          => ($p?->meta_title ?? $p?->title ?? $fallbackTitle) ?? 'Exchosoft Consult',
            'metaDescription' => ($p?->meta_description ?? $fallbackDescription) ?? '',
            'metaKeywords'    => $p?->meta_keywords ?? '',
            'canonicalUrl'    => $p?->canonical_url
                ? (str_starts_with($p->canonical_url, 'http') ? $p->canonical_url : url($p->canonical_url))
                : url()->current(),
            'ogTitle'         => $p?->resolved_og_title       ?? $fallbackTitle       ?? '',
            'ogDescription'   => $p?->resolved_og_description ?? $fallbackDescription ?? '',
            'ogImage'         => $p?->og_image  ?? '',
            'ogType'          => $p?->og_type   ?? 'website',
            'twitterCard'        => $p?->twitter_card              ?? 'summary_large_image',
            'twitterTitle'       => $p?->resolved_twitter_title       ?? '',
            'twitterDescription' => $p?->resolved_twitter_description ?? '',
            'twitterImage'       => $p?->twitter_image ?? $p?->og_image ?? '',
            'schemaMarkup'    => $p?->schema_markup ?? null,
            'pageBannerHeading'    => $p?->banner_heading    ?? '',
            'pageBannerSubheading' => $p?->banner_subheading ?? '',
            'pageBannerImage'      => $p?->banner_image      ?? '',
            'pageBannerCtaText'    => $p?->banner_cta_text   ?? '',
            'pageBannerCtaUrl'     => $p?->banner_cta_url    ?? '',
        ];
    }
}
