<?php

use App\Models\DemoBooking;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Book a Demo — ExchoSoft')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $job_title = '';
    public string $shop_product_id = '';
    public string $demo_type = 'online';
    public string $preferred_date = '';
    public string $preferred_time = '';
    public int $attendees = 1;
    public string $requirements = '';
    public string $message = '';

    public bool $submitted = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->name  = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'name'           => 'required|string|max:200',
            'email'          => 'required|email',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|string',
        ]);

        DemoBooking::create([
            'customer_user_id' => auth()->id(),
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'company'          => $this->company,
            'job_title'        => $this->job_title,
            'shop_product_id'  => $this->shop_product_id ?: null,
            'product_name'     => $this->shop_product_id
                ? ShopProduct::find($this->shop_product_id)?->name
                : null,
            'demo_type'        => $this->demo_type,
            'preferred_date'   => $this->preferred_date,
            'preferred_time'   => $this->preferred_time,
            'attendees'        => $this->attendees,
            'requirements'     => $this->requirements,
            'message'          => $this->message,
        ]);

        $this->submitted = true;
    }

    public function render(): \Illuminate\View\View
    {
        $products = ShopProduct::published()->orderBy('name')->get(['id', 'name']);
        return view('livewire.pages.site.book-demo', compact('products'));
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-slate-900 text-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Get Started</p>
            <h1 class="text-4xl font-bold mb-4">Book a Product Demo</h1>
            <p class="text-slate-400 max-w-xl mx-auto">See our products in action. Our team will walk you through everything and answer your questions live.</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

            @if($submitted)
            <div class="rounded-2xl bg-green-50 border border-green-200 p-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-xl font-bold text-green-800 mb-2">Demo Booked!</h2>
                <p class="text-green-700 mb-6">Thank you, {{ $name }}! We've received your demo request and will confirm the details within 24 hours.</p>
                <a href="{{ route('home') }}" wire:navigate class="inline-flex rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition-colors">Back to Home</a>
            </div>

            @else
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-8">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Demo Request Form</h2>

                <form wire:submit="submit" class="space-y-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name *</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address *</label>
                            <input wire:model="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-100">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone</label>
                            <input wire:model="phone" type="tel" placeholder="+233..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company</label>
                            <input wire:model="company" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Job Title</label>
                            <input wire:model="job_title" type="text" placeholder="e.g. IT Manager" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Number of Attendees</label>
                            <input wire:model="attendees" type="number" min="1" max="20" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Product of Interest</label>
                        <select wire:model="shop_product_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            <option value="">Any Product / General Demo</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Demo Type</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="demo_type" type="radio" value="online" class="text-cyan-600">
                                <span class="text-sm text-slate-700">Online (Video Call)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="demo_type" type="radio" value="onsite" class="text-cyan-600">
                                <span class="text-sm text-slate-700">On-site (Accra)</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Preferred Date *</label>
                            <input wire:model="preferred_date" type="date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            @error('preferred_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Preferred Time *</label>
                            <select wire:model="preferred_time" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">Select time...</option>
                                @foreach(['9:00 AM','10:00 AM','11:00 AM','1:00 PM','2:00 PM','3:00 PM','4:00 PM'] as $t)
                                    <option value="{{ $t }}">{{ $t }} (GMT+0)</option>
                                @endforeach
                            </select>
                            @error('preferred_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Specific Requirements</label>
                        <textarea wire:model="requirements" rows="3" placeholder="What specific features or use cases do you want to see demonstrated?" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Additional Message</label>
                        <textarea wire:model="message" rows="2" placeholder="Anything else you'd like us to know..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-cyan-600 py-3 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-md shadow-cyan-500/25">
                        Submit Demo Request
                    </button>
                </form>
            </div>
            @endif
        </div>
    </section>
</div>
