<?php

use App\Models\Product;
use App\Models\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Pricing Plans — ExchoLicense')] class extends Component
{
    // Pricing plan config (in production these come from a plans table or config file)
    public array $plans = [
        [
            'name'        => 'Starter',
            'description' => 'Perfect for individuals and small teams',
            'monthly'     => 9.99,
            'annual'      => 99.00,
            'features'    => ['1 product', '5 licenses', '1 device / license', 'Email support'],
            'color'       => 'slate',
            'popular'     => false,
        ],
        [
            'name'        => 'Professional',
            'description' => 'For growing software businesses',
            'monthly'     => 29.99,
            'annual'      => 299.00,
            'features'    => ['5 products', '50 licenses', '3 devices / license', 'Offline activation', 'Priority support'],
            'color'       => 'cyan',
            'popular'     => true,
        ],
        [
            'name'        => 'Enterprise',
            'description' => 'Full power for large organisations',
            'monthly'     => 79.99,
            'annual'      => 799.00,
            'features'    => ['Unlimited products', 'Unlimited licenses', 'Floating licenses', 'API access', 'Dedicated support', 'Custom integrations'],
            'color'       => 'violet',
            'popular'     => false,
        ],
    ];

    #[Computed]
    public function stats(): array
    {
        return [
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue'      => Subscription::where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount'),
            'annual_revenue'       => Subscription::where('status', 'active')->where('billing_cycle', 'annual')->sum('amount'),
            'total_products'       => Product::where('is_active', true)->count(),
        ];
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div>
    <x-slot:heading>Pricing Plans</x-slot:heading>

    <div class="space-y-8">

        {{-- Revenue stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-green-50 p-2.5 inline-block mb-3">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($this->stats['active_subscriptions']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Active Subscriptions</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-cyan-50 p-2.5 inline-block mb-3">
                    <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-slate-900">${{ number_format($this->stats['monthly_revenue'], 0) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Monthly Revenue</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-violet-50 p-2.5 inline-block mb-3">
                    <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-slate-900">${{ number_format($this->stats['annual_revenue'], 0) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Annual Revenue</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="rounded-xl bg-amber-50 p-2.5 inline-block mb-3">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($this->stats['total_products']) }}</p>
                <p class="mt-0.5 text-sm text-slate-500">Active Products</p>
            </div>
        </div>

        {{-- Plans --}}
        <div>
            <h2 class="text-base font-semibold text-slate-900 mb-4">Subscription Plans</h2>
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                @foreach($plans as $plan)
                <div class="relative rounded-2xl bg-white p-8 shadow-sm
                    {{ $plan['popular'] ? 'ring-2 ring-cyan-500' : 'ring-1 ring-slate-100' }}">

                    @if($plan['popular'])
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full bg-cyan-600 px-4 py-1 text-xs font-semibold text-white shadow">
                            Most Popular
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-slate-900">{{ $plan['name'] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $plan['description'] }}</p>

                    <div class="mt-6">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-bold text-slate-900">${{ number_format($plan['monthly'], 2) }}</span>
                            <span class="text-sm text-slate-500">/month</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-400">${{ number_format($plan['annual'], 2) }} billed annually</p>
                    </div>

                    <ul class="mt-6 space-y-3">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-center gap-2.5 text-sm text-slate-600">
                            <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <div class="mt-8">
                        <button class="w-full rounded-xl py-2.5 text-sm font-semibold transition-colors
                            {{ $plan['popular']
                                ? 'bg-cyan-600 text-white hover:bg-cyan-700'
                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $plan['popular'] ? 'Get started — Popular' : 'Get started' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
