<?php

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    #[Url]
    public ?string $order = null;

    public ?Order $orderInstance = null;

    public function mount(): void
    {
        // 1. Verify that order token exists in the system matrix
        if (!$this->order || !$this->orderInstance = Order::with(['orderItems.product', 'orderItems.plan'])->where('order_number', $this->order)->first()) {
            abort(404, 'Order node parameter reference tracking signature not found.');
        }

        // 2. Security validation gate: Protect structural data visibility from third-party cross-reads
        if ($this->orderInstance->customer_user_id && $this->orderInstance->customer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access token validation identity mismatch.');
        }
    }
}; ?>

<div>
    {{-- ── SUCCESS HERO BANNER ── --}}
    <x-page-banner
        tag="Deployment Confirmed"
        tagIcon="check_circle"
        title='Thank You For Your<br><span style="color:#00b8db;">System Order</span>'
        subtitle="Your request protocol has settled successfully inside our ledger. Processing key generation."
        :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Checkout'],['label'=>'Order Complete']]"
        glowX="30%"
        glowX2="85%"
    >
        {{-- SUCCESS BALLOON CHECK ORNAMENT SVG --}}
        <svg slot="ornament" class="absolute right-[12%] top-1/2 -translate-y-1/2 w-36 h-36 opacity-[.06] pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="#00b8db" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
    </x-page-banner>

    {{-- ── SUCCESS RECEIPT MATRIX CONTAINER ── --}}
    <section class="site-section py-16 bg-slate-50 dark:bg-slate-950 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12">

                {{-- Left Core Meta Details --}}
                <div class="md:col-span-7 p-6 md:p-10 space-y-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider
                            @if($orderInstance->payment_status === 'paid') bg-emerald-500/10 text-emerald-500 @else bg-amber-500/10 text-amber-500 @endif mb-3">
                            <span class="w-1.5 h-1.5 rounded-full @if($orderInstance->payment_status === 'paid') bg-emerald-500 @else bg-amber-500 @endif animate-pulse"></span>
                            Payment Status: {{ bigintval($orderInstance->payment_status) === 'paid' ? 'Settled' : 'Awaiting Ledger Settlement' }}
                        </div>

                        <h2 class="font-display text-2xl font-black text-slate-900 dark:text-white">
                            @if($orderInstance->payment_status === 'paid')
                                Provisioning Infrastructure Node Keys
                            @else
                                Awaiting Payment Notification
                            @endif
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Order Index Ledger Reference Reference: <span class="font-mono text-slate-600 dark:text-slate-300 font-bold">{{ $orderInstance->order_number }}</span></p>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-800" />

                    {{-- Dynamic Account Creation Welcome Message --}}
                    @if($orderInstance->customer_user_id)
                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-2xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-blue-500 text-lg">account_circle</span>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Account Architecture Ready</h4>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Your personal dashboard account has been created and provisioned using the email <span class="font-mono font-bold text-slate-600 dark:text-slate-300">{{ $orderInstance->customer_email }}</span>. You can sign in right now to manage software updates.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-slate-50 dark:bg-slate-950/50 border border-slate-200/60 dark:border-slate-800 rounded-2xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 text-lg">person_outline</span>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Guest Checkout Session</h4>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Your deployment tracking parameters are registered on our guest channels. A copy of your license keys will land inside your email inbox shortly.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">Next Step Instructions</h3>

                        <div class="flex gap-3 items-start">
                            <span class="w-5 h-5 rounded-md bg-[#00b8db]/10 text-[#00b8db] font-mono text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Check your verification inbox for your download links and license parameters snapshot.</p>
                        </div>
                        <div class="flex gap-3 items-start">
                            <span class="w-5 h-5 rounded-md bg-[#00b8db]/10 text-[#00b8db] font-mono text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Initialize the client platform or application and append the primary token license key strings.</p>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-wrap gap-3">
                        @if($orderInstance->customer_user_id)
                            <a href="/dashboard" class="px-5 py-3 bg-[#00b8db] hover:bg-[#009cb8] text-white text-xs font-bold rounded-xl transition-colors no-underline">
                                Launch Customer Dashboard
                            </a>
                        @endif
                        <a href="{{ route('home') }}" class="px-5 py-3 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold rounded-xl transition-colors no-underline">
                            Return to Website Catalog
                        </a>
                    </div>
                </div>

                {{-- Right Summary Pane --}}
                <div class="md:col-span-5 bg-slate-50/50 dark:bg-slate-950/20 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800 p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono mb-4">Package Scope</h3>

                        <div class="space-y-4">
                            @foreach($orderInstance->orderItems as $item)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[#00b8db] text-lg mt-0.5">terminal</span>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-white leading-snug">{{ $item->product_name }}</h4>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->plan_name }} Package (v{{ $item->product_version }})</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800/80 space-y-2.5 font-mono text-[11px]">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $orderInstance->currency }} {{ number_format($orderInstance->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Discounts Applied</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $orderInstance->currency }} {{ number_format($orderInstance->discount, 2) }}</span>
                        </div>
                        <hr class="border-slate-100 dark:border-slate-800/40 my-2" />
                        <div class="flex justify-between items-baseline">
                            <span class="font-sans font-bold text-slate-800 dark:text-white text-xs">Total Remitted</span>
                            <span class="font-sans font-black text-lg text-[#00b8db]">
                                {{ $orderInstance->currency }} {{ number_format($orderInstance->total, 2) }}
                            </span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>
</div>
