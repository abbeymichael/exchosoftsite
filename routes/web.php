<?php

use Illuminate\Support\Facades\Route;

// ─── HOME — redirects to the ExchoSoft site ─────────────────────────────────
Route::get('/', fn () => redirect()->route('home'));

// ═══════════════════════════════════════════════════════════════════════════════
// PUBLIC WEBSITE — ExchoSoft.com
// ═══════════════════════════════════════════════════════════════════════════════

Route::prefix('')->name('')->group(function () {

    // Home
    Route::livewire('/home', 'pages::site.home')->name('home');

    // ── Site pages ──────────────────────────────────────────────────────────
    Route::prefix('')->name('site.')->group(function () {

        // About
        Route::livewire('/about', 'pages::site.about')->name('about');

        // Services
        Route::livewire('/services', 'pages::site.services')->name('services');

        // Contact
        Route::livewire('/contact', 'pages::site.contact')->name('contact');

        // Products
        Route::livewire('/products', 'pages::site.products')->name('products');
        Route::livewire('/products/{slug}', 'pages::site.product-detail')->name('products.show');

        // Portfolio
        Route::livewire('/portfolio', 'pages::site.portfolio')->name('portfolio');
        Route::livewire('/portfolio/{slug}', 'pages::site.portfolio-detail')->name('portfolio.show');

        // Case Studies
        Route::livewire('/case-studies', 'pages::site.case-studies')->name('case-studies');
        Route::livewire('/case-studies/{slug}', 'pages::site.case-study-detail')->name('case-studies.show');

        // White Papers
        Route::livewire('/white-papers', 'pages::site.white-papers')->name('white-papers');

        // Tech Blog
        Route::livewire('/blog', 'pages::site.blog')->name('blog');
        Route::livewire('/blog/{slug}', 'pages::site.blog-post')->name('blog.show');

        // Consulting
        Route::livewire('/consulting', 'pages::site.consulting')->name('consulting');

        // Book Demo (public)
        Route::livewire('/book-demo', 'pages::site.book-demo')->name('book-demo');


                // ── Legal & Policy pages ───────────────────────────────────────────
        Route::livewire('/privacy-policy',           'pages::site.legal.privacy-policy')->name('privacy-policy');
        Route::livewire('/terms-of-service',         'pages::site.legal.terms-of-service')->name('terms-of-service');
        Route::livewire('/security',                 'pages::site.legal.security')->name('security');
        Route::livewire('/cookie-policy',            'pages::site.legal.cookie-policy')->name('cookie-policy');
        Route::livewire('/data-processing-agreement', 'pages::site.legal.data-processing-agreement')->name('data-processing-agreement');
    });
});

// ═══════════════════════════════════════════════════════════════════════════════
// CUSTOMER AUTH
// ═══════════════════════════════════════════════════════════════════════════════

Route::prefix('account')->name('customer.')->group(function () {

    // Guest-only auth
    Route::middleware('guest')->group(function () {
        Route::livewire('/login', 'pages::customer.auth.login')->name('login');
        Route::livewire('/register', 'pages::customer.auth.register')->name('register');
    });

    // Logout (POST)
    Route::middleware('auth')->post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');

    // Protected customer portal
    Route::middleware('auth')->group(function () {
        Route::livewire('/dashboard', 'pages::customer.dashboard')->name('dashboard');
        Route::livewire('/orders', 'pages::customer.orders')->name('orders');
        Route::livewire('/licenses', 'pages::customer.licenses')->name('licenses');
        Route::livewire('/profile', 'pages::customer.profile')->name('profile');
    });
});

// ═══════════════════════════════════════════════════════════════════════════════
// ADMIN AUTH
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/login', 'pages::admin.auth.login')->name('login');
});

Route::middleware('auth')->post('/admin/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout');

// ═══════════════════════════════════════════════════════════════════════════════
// ADMIN PANEL — ExchoSoft Admin
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Overview
    Route::livewire('/dashboard', 'pages::admin.dashboard')->name('dashboard');

        // ── Page Editors ───────────────────────────────────────────────────────
    Route::livewire('/pages',                        'pages::admin.pages.index')->name('pages.index');
    Route::livewire('/pages/{key}/edit',             'pages::admin.pages.edit')->name('pages.edit');
    Route::livewire('/pages/{key}/versions',         'pages::admin.pages.versions')->name('pages.versions');

    // ── Website Management ─────────────────────────────────────────────────
    Route::livewire('/shop-products', 'pages::admin.shop-products')->name('shop-products');
    Route::livewire('/orders', 'pages::admin.orders')->name('orders');
    Route::livewire('/demo-bookings', 'pages::admin.demo-bookings')->name('demo-bookings');
    Route::livewire('/consulting', 'pages::admin.consulting')->name('consulting');

    // ── Content Management ─────────────────────────────────────────────────
    Route::livewire('/blog-posts', 'pages::admin.blog-posts')->name('blog-posts');
    Route::livewire('/white-papers', 'pages::admin.white-papers')->name('white-papers');
    Route::livewire('/case-studies', 'pages::admin.case-studies')->name('case-studies');
    Route::livewire('/portfolio', 'pages::admin.portfolio')->name('portfolio');

    // ── License Management ─────────────────────────────────────────────────
    Route::livewire('/products', 'pages::admin.products')->name('products');
    Route::livewire('/licenses', 'pages::admin.licenses')->name('licenses');
    Route::livewire('/customers', 'pages::admin.customers')->name('customers');
    Route::livewire('/activations', 'pages::admin.activations')->name('activations');
    Route::livewire('/batches', 'pages::admin.batches')->name('batches');

    // ── Developer ──────────────────────────────────────────────────────────
    Route::livewire('/api-tokens', 'pages::admin.api-tokens')->name('api-tokens');
    Route::livewire('/audit-logs', 'pages::admin.audit-logs')->name('audit-logs');
    Route::livewire('/api-docs', 'pages::admin.api-docs')->name('api-docs');

    // ── Settings ───────────────────────────────────────────────────────────
    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/admins', 'pages::admin.admins')->name('admins');
    Route::livewire('/subscriptions', 'pages::admin.subscriptions')->name('subscriptions');
    Route::livewire('/pricing', 'pages::admin.pricing')->name('pricing');
});
