<?php

namespace App\Livewire\Concerns;

use App\Models\Page;

/**
 * LoadsPageSeo
 *
 * Mixin for Livewire site page components.
 *
 * Usage:
 *   use App\Livewire\Concerns\LoadsPageSeo;
 *
 *   new class extends Component {
 *       use LoadsPageSeo;
 *       // In mount() or render():
 *       $this->loadPageSeo('about');
 *   }
 *
 * The trait exposes a public $pageSeo property (a Page instance or null)
 * and a withSeoData() method that returns a view data array ready to
 * merge into a compact() / view() call.
 *
 * The site layout (layouts.site) reads the following slots/variables:
 *   $title, $metaDescription, $metaKeywords, $canonicalUrl,
 *   $ogTitle, $ogDescription, $ogImage, $ogType,
 *   $twitterCard, $twitterTitle, $twitterDescription, $twitterImage,
 *   $schemaMarkup
 */
trait LoadsPageSeo
{
    public ?Page $pageSeo = null;

    /**
     * Load the Page record for the given key and store in $this->pageSeo.
     * Silently fails (leaves $pageSeo null) if the record doesn't exist yet.
     */
    protected function loadPageSeo(string $key): void
    {
        $this->pageSeo = Page::where('key', $key)->first();
    }

    /**
     * Returns an array of SEO variables to pass to the view.
     * Merge this into your view data so the site layout can pick them up.
     *
     * @param  string|null  $fallbackTitle        Default page <title>
     * @param  string|null  $fallbackDescription  Default meta description
     */
    protected function seoViewData(
        string $fallbackTitle       = null,
        string $fallbackDescription = null
    ): array {
        $p = $this->pageSeo;

        return [
            // <title>
            'title'          => ($p?->meta_title ?? $p?->title ?? $fallbackTitle)
                                    ?? 'Exchosoft Consult',

            // <meta name="description">
            'metaDescription' => ($p?->meta_description ?? $fallbackDescription)
                                    ?? '',

            // <meta name="keywords">
            'metaKeywords'    => $p?->meta_keywords ?? '',

            // <link rel="canonical">
            'canonicalUrl'    => $p?->canonical_url
                                    ? (str_starts_with($p->canonical_url, 'http')
                                        ? $p->canonical_url
                                        : url($p->canonical_url))
                                    : url()->current(),

            // Open Graph
            'ogTitle'         => $p?->resolved_og_title    ?? $fallbackTitle ?? '',
            'ogDescription'   => $p?->resolved_og_description ?? $fallbackDescription ?? '',
            'ogImage'         => $p?->og_image ?? '',
            'ogType'          => $p?->og_type  ?? 'website',

            // Twitter Card
            'twitterCard'        => $p?->twitter_card        ?? 'summary_large_image',
            'twitterTitle'       => $p?->resolved_twitter_title       ?? '',
            'twitterDescription' => $p?->resolved_twitter_description ?? '',
            'twitterImage'       => $p?->twitter_image ?? $p?->og_image ?? '',

            // JSON-LD
            'schemaMarkup'    => $p?->schema_markup ?? null,

            // Banner convenience helpers (for pages that use Page banner data)
            'pageBannerHeading'    => $p?->banner_heading    ?? '',
            'pageBannerSubheading' => $p?->banner_subheading ?? '',
            'pageBannerImage'      => $p?->banner_image      ?? '',
            'pageBannerCtaText'    => $p?->banner_cta_text   ?? '',
            'pageBannerCtaUrl'     => $p?->banner_cta_url    ?? '',
        ];
    }
}
