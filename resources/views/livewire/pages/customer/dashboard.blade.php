<?php

use App\Models\Order;
use App\Models\DemoBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('My Account — ExchoSoft')] class extends Component
{
    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();

        $stats = [
            'orders'  => Order::where('customer_user_id', $user->id)->count(),
            'demos'   => DemoBooking::where('customer_user_id', $user->id)->count(),
        ];

        $recentOrders  = Order::where('customer_user_id', $user->id)->with('items')->latest()->limit(5)->get();
        $upcomingDemos = DemoBooking::where('customer_user_id', $user->id)->upcoming()->limit(3)->get();

        return view('livewire.pages.customer.dashboard', compact('stats', 'recentOrders', 'upcomingDemos'));
    }
}; ?>

<div class="py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Welcome --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-500 mt-1">Manage your orders, licenses, and demo bookings.</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-8">
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 text-center">
                <p class="text-2xl font-bold text-slate-900">{{ $stats['orders'] }}</p>
                <p class="text-sm text-slate-500">Orders</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 text-center">
                <p class="text-2xl font-bold text-slate-900">{{ $stats['demos'] }}</p>
                <p class="text-sm text-slate-500">Demos</p>
            </div>
            <a href="{{ route('site.products') }}" wire:navigate class="rounded-2xl bg-cyan-600 text-white p-5 text-center hover:bg-cyan-700 transition-colors shadow-sm shadow-cyan-500/25">
                <p class="text-2xl">🛒</p>
                <p class="text-sm font-semibold mt-1">Shop</p>
            </a>
            <a href="{{ route('site.book-demo') }}" wire:navigate class="rounded-2xl bg-violet-600 text-white p-5 text-center hover:bg-violet-700 transition-colors shadow-sm">
                <p class="text-2xl">📅</p>
                <p class="text-sm font-semibold mt-1">Book Demo</p>
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Recent Orders --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
                    <a href="{{ route('customer.orders') }}" wire:navigate class="text-xs text-cyan-600 hover:underline">View all</a>
                </div>
                @if($recentOrders->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-400">No orders yet. <a href="{{ route('site.products') }}" wire:navigate class="text-cyan-600 hover:underline">Browse products</a></div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-xs font-mono font-semibold text-slate-700">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-500">{{ $order->items->count() }} item(s) · {{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">GHS {{ number_format($order->total, 2) }}</p>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Upcoming Demos --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-900">Upcoming Demos</h2>
                    <a href="{{ route('site.book-demo') }}" wire:navigate class="text-xs text-cyan-600 hover:underline">Book new</a>
                </div>
                @if($upcomingDemos->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-400">No demos scheduled. <a href="{{ route('site.book-demo') }}" wire:navigate class="text-cyan-600 hover:underline">Book one</a></div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($upcomingDemos as $demo)
                    <div class="px-5 py-3">
                        <p class="text-sm font-semibold text-slate-900">{{ $demo->product_name ?? 'General Demo' }}</p>
                        <p class="text-xs text-slate-500">{{ $demo->preferred_date->format('d M Y') }} @if($demo->preferred_time)at {{ $demo->preferred_time }}@endif</p>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium mt-1 capitalize
                            {{ $demo->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $demo->status }}
                        </span>
                        @if($demo->meeting_link && $demo->status === 'confirmed')
                            <a href="{{ $demo->meeting_link }}" target="_blank" class="ml-2 text-xs text-cyan-600 hover:underline">Join</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            <a href="{{ route('customer.orders') }}" wire:navigate class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4 hover:shadow-md transition-all">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">My Orders</p>
                    <p class="text-xs text-slate-500">Order history</p>
                </div>
            </a>
            <a href="{{ route('customer.licenses') }}" wire:navigate class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4 hover:shadow-md transition-all">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">My Licenses</p>
                    <p class="text-xs text-slate-500">Software keys</p>
                </div>
            </a>
            <a href="{{ route('customer.profile') }}" wire:navigate class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4 hover:shadow-md transition-all">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Profile</p>
                    <p class="text-xs text-slate-500">Account settings</p>
                </div>
            </a>
        </div>

    </div>
</div>
