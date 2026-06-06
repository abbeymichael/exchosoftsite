<?php

use App\Models\ProductPlan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component {
    #[Url]
    public ?string $plan = null;

    // Plan & Pricing Context States
    public ?ProductPlan $productPlan = null;

    // Form Inputs
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public bool $create_account = false;
    public string $password = '';

    public function mount(): void
    {
        // Enforce validation on plan parameter presence
        if (!$this->plan || !($this->productPlan = ProductPlan::with('product')->find($this->plan))) {
            abort(404, 'Invalid or expired package initialization state.');
        }

        // Autofill details dynamically if the user is already authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
        }
    }

    public function executeCheckout()
    {
        // Form Validation Context Rules
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:200',
            'phone' => 'required|string|max:30',
        ];

        // Apply strict credential evaluation if account creation is requested
        if (!Auth::check() && $this->create_account) {
            $rules['password'] = 'required|string|min:8';
            $rules['email'] .= '|unique:users,email';
        }

        $this->validate($rules);

        try {
            return DB::transaction(function () {
                $userId = null;

                // User Lifecycle Management (Account Generation Route)
                if (!Auth::check() && $this->create_account) {
                    $user = User::create([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'password' => Hash::make($this->password),
                    ]);

                    Auth::login($user);
                    $userId = $user->id;
                } elseif (Auth::check()) {
                    $userId = Auth::id();
                }

                // Draft & Save Order Ledger Instance
                $price = $this->productPlan->sale_price ?? $this->productPlan->price;

                // Generate a custom robust order sequence number matching your table layout configuration
                $orderNumber = 'EXC-' . strtoupper(Str::random(4)) . '-' . rand(1000, 9999);

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'customer_user_id' => $userId,
                    'guest_name' => $userId ? null : $this->name,
                    'guest_email' => $userId ? null : $this->email,
                    'guest_phone' => $userId ? null : $this->phone,
                    'subtotal' => $price,
                    'discount' => 0.0,
                    'tax' => 0.0,
                    'total' => $price,
                    'currency' => $this->productPlan->currency ?? 'USD',
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => 'gateway',
                ]);

                // Attach Chosen Product Plan Line Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $this->productPlan->product_id,
                    'plan_id' => $this->productPlan->id,
                    'product_name' => $this->productPlan->product->name ?? 'Architecture System Node',
                    'plan_name' => $this->productPlan->name,
                    'product_version' => $this->productPlan->product->current_version ?? '1.0.0',
                    'unit_price' => $price,
                    'quantity' => 1,
                    'total' => $price,
                ]);

                // Routing Target: Forward to payment gateway processor processing endpoint
                return redirect()->route('site.payment.gateway', ['order' => $order->order_number]);
            });
        } catch (\Exception $e) {
            session()->flash('checkout_error', 'Transaction could not be initialized: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    {{-- ── CHECKOUT HERO BANNER ── --}}
    <x-page-banner tag="Secure Checkout" tagIcon="lock"
        title='Finalize Your<br><span style="color:#00b8db;">System Deployment</span>'
        subtitle="Review your deployment parameters and establish your architecture licensing node key protocols."
        glowX="75%" glowX2="20%">
        {{-- PADLOCK ORNAMENT SVG SLOT --}}
       <svg
            slot="ornament"
            class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none"
            viewBox="0 0 180 180"
            fill="none"
        >
            <rect x="1" y="1" width="178" height="178" rx="16" stroke="#00b8db" stroke-width="1" />
            <rect x="22" y="22" width="136" height="136" rx="10" stroke="#00b8db" stroke-width="1" />
            <rect x="44" y="44" width="92" height="92" rx="6" stroke="#00b8db" stroke-width="1" />
            <rect x="66" y="66" width="48" height="48" rx="4" stroke="#00b8db" stroke-width="1" />
            <line x1="90" y1="1" x2="90" y2="179" stroke="#00b8db" stroke-width=".5" />
            <line x1="1" y1="90" x2="179" y2="90" stroke="#00b8db" stroke-width=".5" />
            <circle cx="90" cy="90" r="5" fill="#00b8db" />
        </svg>
    </x-page-banner>

    {{-- ── CHECKOUT CONTENT CORE ── --}}
    <section class="site-section py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div
                class="lg:col-span-7 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 md:p-8 rounded-2xl shadow-sm">
                <h2 class="font-display text-xl font-extrabold text-slate-900 dark:text-white mb-2">License Provisioning
                    Identity</h2>
                <p class="text-xs text-slate-400 mb-6">Complete your identity records below to generate your unique
                    structural cryptographic core licenses.</p>

                @if (session()->has('checkout_error'))
                    <div
                        class="mb-5 p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs rounded-xl font-mono">
                        {{ session('checkout_error') }}
                    </div>
                @endif

                <form wire:submit="executeCheckout" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Full Name
                                *</label>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-[#00b8db] focus:ring-0 outline-none transition-colors dark:text-white"
                                placeholder="e.g. Kwame Mensah" @if (Auth::check()) readonly @endif>
                            @error('name')
                                <p class="text-[11px] text-red-500 font-mono">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Phone
                                Number *</label>
                            <input type="text" wire:model="phone"
                                class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-[#00b8db] focus:ring-0 outline-none transition-colors dark:text-white"
                                placeholder="+233 XX XXX XXXX">
                            @error('phone')
                                <p class="text-[11px] text-red-500 font-mono">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email Address
                            *</label>
                        <input type="email" wire:model="email"
                            class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-[#00b8db] focus:ring-0 outline-none transition-colors dark:text-white"
                            placeholder="you@company.com" @if (Auth::check()) readonly @endif>
                        @error('email')
                            <p class="text-[11px] text-red-500 font-mono">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (!Auth::check())
                        <hr class="border-slate-100 dark:border-slate-800/80 my-6" />

                        <div
                            class="p-4 bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/80 rounded-xl">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="create_account"
                                    class="w-4 h-4 rounded border-slate-300 dark:border-slate-800 text-[#00b8db] focus:ring-[#00b8db]">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Create an
                                        Exchosoft Account?</span>
                                    <span class="text-[11px] text-slate-400 block">Save your keys securely and access
                                        deployment version upgrades anytime.</span>
                                </div>
                            </label>

                            @if ($create_account)
                                <div class="mt-4 space-y-1.5" x-transition>
                                    <label
                                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Choose
                                        Secure Password</label>
                                    <input type="password" wire:model="password"
                                        class="w-full px-4 py-3 text-sm bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-[#00b8db] outline-none dark:text-white"
                                        placeholder="Minimum 8 characters">
                                    @error('password')
                                        <p class="text-[11px] text-red-500 font-mono">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full mt-6 px-6 py-4 bg-[#00b8db] hover:bg-[#009cb8] text-white font-semibold text-sm rounded-xl transition-colors shadow-md shadow-[#00b8db]/10 flex items-center justify-center gap-2 border-0 cursor-pointer">
                        <span wire:loading.remove wire:target="executeCheckout">Initialize Secure Gateway Payment</span>
                        <span wire:loading wire:target="executeCheckout">Processing Order Protocol...</span>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-5 space-y-4">
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-2xl shadow-sm">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Manifest Summary</h3>

                    @if ($productPlan)
                        <div class="pb-4 border-b border-slate-100 dark:border-slate-800 flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#00b8db]/10 flex items-center justify-center text-[#00b8db] shrink-0">
                                <span class="material-symbols-outlined text-xl">terminal</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white leading-tight">
                                    {{ $productPlan->product->name ?? 'System Node Core' }}
                                </h4>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    {{ $productPlan->name }} Plan ({{ $productPlan->billing_label }})
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 space-y-2.5 text-xs font-mono">
                            <div class="flex justify-between text-slate-400">
                                <span>Base License Unit Matrix</span>
                                <span class="text-slate-700 dark:text-slate-300">
                                    {{ $productPlan->currency }}
                                    {{ number_format($productPlan->sale_price ?? $productPlan->price, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Cryptographic Provisioning Fee</span>
                                <span class="text-slate-700 dark:text-slate-300">FREE</span>
                            </div>
                            <hr class="border-slate-100 dark:border-slate-800/60 my-2" />
                            <div class="flex justify-between items-baseline pt-1">
                                <span class="font-sans font-bold text-slate-900 dark:text-white text-sm">Total Due
                                    Payment</span>
                                <span class="font-sans font-black text-xl text-[#00b8db]">
                                    {{ $productPlan->currency }}
                                    {{ number_format($productPlan->sale_price ?? $productPlan->price, 2) }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">verified_user</span>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 leading-relaxed font-mono">
                        Upon confirmation from the payment ledger gateway, your system license key will generate
                        instantly and be delivered directly to your profile stack.
                    </p>
                </div>
            </div>

        </div>
    </section>
</div>
