<?php

use App\Models\ConsultingInquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Consulting & Gigs — ExchoSoft')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $inquiry_type = 'consulting';
    public string $subject = '';
    public string $description = '';
    public string $budget_range = '';
    public string $timeline = '';
    public string $how_heard = '';
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
            'name'        => 'required|string|max:200',
            'email'       => 'required|email',
            'subject'     => 'required|string|max:300',
            'description' => 'required|string|min:30',
        ]);

        ConsultingInquiry::create([
            'customer_user_id' => auth()->id(),
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'company'          => $this->company,
            'inquiry_type'     => $this->inquiry_type,
            'subject'          => $this->subject,
            'description'      => $this->description,
            'budget_range'     => $this->budget_range,
            'timeline'         => $this->timeline,
            'how_heard'        => $this->how_heard,
        ]);

        $this->submitted = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.consulting');
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 to-cyan-900 text-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Services</p>
                <h1 class="text-4xl font-bold mb-4">🚧 Consulting & Gigs — Placeholder</h1>
                <p class="text-slate-300 text-lg leading-relaxed">Replace this with your consulting services, gig offerings, and expertise. Describe what you do, who you serve, and what results you deliver.</p>
            </div>
        </div>
    </section>

    {{-- Services Grid PLACEHOLDER --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">🚧 Services Placeholder</h2>
                <p class="text-slate-500 mt-2">Replace these cards with your actual consulting services and expertise areas.</p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    ['Software Consulting', 'Technology advisory and implementation consulting.', '💻'],
                    ['Custom Development', 'Bespoke software built to your specifications.', '⚙️'],
                    ['System Integration', 'Connecting your existing tools and platforms.', '🔗'],
                    ['Training & Support', 'Team training and ongoing technical support.', '🎓'],
                    ['Code Review & Audit', 'Security and quality audits for existing systems.', '🔍'],
                    ['Product Strategy', 'From idea to launch — product planning and roadmaps.', '🗺️'],
                ] as [$title, $desc, $icon])
                <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
                    <span class="text-3xl mb-3 block">{{ $icon }}</span>
                    <h3 class="font-bold text-slate-900 mb-2">{{ $title }}</h3>
                    <p class="text-sm text-slate-500">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Inquiry Form --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-slate-900">Send an Inquiry</h2>
                <p class="text-slate-500 mt-2">Tell us about your project or gig and we'll get back to you promptly.</p>
            </div>

            @if($submitted)
            <div class="rounded-2xl bg-green-50 border border-green-200 p-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 mb-4">
                    <svg class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-green-800 mb-2">Inquiry Sent!</h3>
                <p class="text-green-700">Thank you! We'll review your inquiry and respond within 24–48 hours.</p>
            </div>
            @else
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-8">
                <form wire:submit="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name *</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email *</label>
                            <input wire:model="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone</label>
                            <input wire:model="phone" type="tel" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company</label>
                            <input wire:model="company" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Inquiry Type</label>
                            <select wire:model="inquiry_type" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="consulting">Consulting</option>
                                <option value="gig">Gig / Contract</option>
                                <option value="contract">Long-term Contract</option>
                                <option value="partnership">Partnership</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Budget Range</label>
                            <select wire:model="budget_range" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">Select range...</option>
                                <option value="Under GHS 2,000">Under GHS 2,000</option>
                                <option value="GHS 2,000 - 5,000">GHS 2,000 - 5,000</option>
                                <option value="GHS 5,000 - 10,000">GHS 5,000 - 10,000</option>
                                <option value="GHS 10,000 - 25,000">GHS 10,000 - 25,000</option>
                                <option value="GHS 25,000+">GHS 25,000+</option>
                                <option value="Open to discuss">Open to discuss</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Subject *</label>
                        <input wire:model="subject" type="text" placeholder="Brief summary of what you need" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                        @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Describe Your Project / Requirements *</label>
                        <textarea wire:model="description" rows="5" placeholder="Tell us about your project, goals, challenges, and what success looks like for you..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Timeline</label>
                            <select wire:model="timeline" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">Select timeline...</option>
                                <option value="ASAP">ASAP</option>
                                <option value="1-4 weeks">1-4 weeks</option>
                                <option value="1-3 months">1-3 months</option>
                                <option value="3-6 months">3-6 months</option>
                                <option value="6+ months">6+ months</option>
                                <option value="Ongoing">Ongoing / Retainer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">How did you hear about us?</label>
                            <select wire:model="how_heard" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400">
                                <option value="">Select...</option>
                                <option value="google">Google Search</option>
                                <option value="referral">Referral</option>
                                <option value="social">Social Media</option>
                                <option value="event">Event / Conference</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-cyan-600 py-3 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors shadow-md shadow-cyan-500/25">
                        Send Inquiry
                    </button>
                </form>
            </div>
            @endif
        </div>
    </section>
</div>
