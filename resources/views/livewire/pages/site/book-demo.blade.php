<?php

use App\Models\DemoBooking;
use App\Models\ShopProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Book a Demo — Exchosoft Consult')] class extends Component
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
<style>
  .page-banner { min-height: 380px; background: var(--navy); position: relative; overflow: hidden; display: flex; align-items: center; }
  .page-banner-dots { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(0,184,219,0.14) 1px, transparent 1px); background-size: 32px 32px; pointer-events: none; }
  .page-banner-glow { position: absolute; inset: 0; background: radial-gradient(circle at 75% 50%, rgba(0,184,219,0.1) 0%, transparent 60%); pointer-events: none; }
  .page-banner-content { position: relative; z-index: 2; padding: 4rem 6rem; max-width: 700px; }
  .page-banner-crumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; }
  .page-banner-crumb a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .page-banner-crumb a:hover { color: var(--cyan); }
  .page-banner-crumb .sep { color: rgba(255,255,255,0.2); }
  .page-banner-crumb .ccurrent { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .page-banner-tag { display: inline-flex; background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2); color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; margin-bottom: 1.25rem; text-transform: uppercase; }
  .page-banner h1 { font-family: var(--font-display); font-size: clamp(2rem, 3.8vw, 3.2rem); font-weight: 800; color: var(--white); line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1rem; }
  .page-banner h1 em { color: var(--cyan); font-style: normal; }
  .page-banner-sub { font-size: 1rem; color: rgba(255,255,255,0.55); max-width: 540px; line-height: 1.75; font-weight: 300; }
  .demo-body { padding: 4rem 6rem; }
  .demo-form-wrap { max-width: 720px; margin: 0 auto; }
  .demo-form-box { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 2.5rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
  .demo-form-box h2 { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; color: var(--navy); margin-bottom: 2rem; }
  .df-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
  .df-field { margin-bottom: 1rem; }
  .df-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 0.4rem; font-family: var(--font-display); }
  .df-input, .df-select, .df-textarea {
    width: 100%; padding: 0.7rem 0.9rem; border: 1px solid var(--border); border-radius: 8px;
    font-size: 0.875rem; font-family: var(--font-body); color: var(--text-primary);
    background: var(--white); transition: border-color 0.2s;
  }
  .df-input:focus, .df-select:focus, .df-textarea:focus { outline: none; border-color: var(--cyan); }
  .df-textarea { resize: none; }
  .df-error { font-size: 0.75rem; color: #dc2626; margin-top: 0.35rem; }
  .df-radio-row { display: flex; gap: 1.5rem; align-items: center; }
  .df-radio-label { display: flex; align-items: center; gap: 0.4rem; font-size: 0.875rem; color: var(--text-secondary); cursor: pointer; }
  .btn-submit-demo { width: 100%; background: var(--cyan); color: var(--white); padding: 0.95rem; border-radius: 8px; border: none; cursor: pointer; font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; transition: background 0.2s; margin-top: 0.5rem; }
  .btn-submit-demo:hover { background: var(--cyan-dark); }
  .demo-success { background: var(--sky-light); border: 1px solid rgba(0,184,219,0.3); border-radius: 16px; padding: 3rem 2rem; text-align: center; }
  .demo-success h2 { font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: var(--navy); margin-bottom: 0.5rem; }
  .demo-success p { font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem; }
  @media (max-width:1024px) { .page-banner-content { padding:3rem 2rem; } .demo-body { padding:2.5rem 2rem; } .df-row { grid-template-columns:1fr; } }
</style>

<!-- BANNER -->
<x-page-banner
    tag="Get Started"
    title="See Our Products **in Action**"
    subtitle="Book a personalized demo and let our team walk you through how Exchosoft products can transform your business operations — in your reality, not ours."
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Book a Demo']]"
/>

    <section class="demo-body">
        <div class="demo-form-wrap">

            @if($submitted)
            <div class="demo-success">
              <div style="font-size:3rem;margin-bottom:1rem;">✓</div>
              <h2>Demo Booked!</h2>
              <p>Thank you, {{ $name }}! We've received your demo request and will confirm the details within 24 hours.</p>
              <a href="{{ route('home') }}" wire:navigate style="background:var(--cyan);color:white;padding:0.75rem 2rem;border-radius:8px;font-family:var(--font-display);font-size:0.9rem;font-weight:700;text-decoration:none;display:inline-block;">Back to Home</a>
            </div>
            @else
            <div class="demo-form-box">
              <h2>Demo Request Form</h2>
              <form wire:submit="submit">
                <div class="df-row">
                  <div>
                    <label class="df-label">Full Name *</label>
                    <input wire:model="name" type="text" class="df-input">
                    @error('name') <p class="df-error">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="df-label">Email Address *</label>
                    <input wire:model="email" type="email" class="df-input">
                    @error('email') <p class="df-error">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="df-label">Phone</label>
                    <input wire:model="phone" type="tel" placeholder="+233..." class="df-input">
                  </div>
                  <div>
                    <label class="df-label">Company</label>
                    <input wire:model="company" type="text" class="df-input">
                  </div>
                  <div>
                    <label class="df-label">Job Title</label>
                    <input wire:model="job_title" type="text" placeholder="e.g. IT Manager" class="df-input">
                  </div>
                  <div>
                    <label class="df-label">Number of Attendees</label>
                    <input wire:model="attendees" type="number" min="1" max="20" class="df-input">
                  </div>
                </div>
                <div class="df-field">
                  <label class="df-label">Product of Interest</label>
                  <select wire:model="shop_product_id" class="df-select">
                    <option value="">Any Product / General Demo</option>
                    @foreach($products as $p)
                      <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="df-field">
                  <label class="df-label">Demo Type</label>
                  <div class="df-radio-row">
                    <label class="df-radio-label"><input wire:model="demo_type" type="radio" value="online"> Online (Video Call)</label>
                    <label class="df-radio-label"><input wire:model="demo_type" type="radio" value="onsite"> On-site (Accra)</label>
                  </div>
                </div>
                <div class="df-row">
                  <div>
                    <label class="df-label">Preferred Date *</label>
                    <input wire:model="preferred_date" type="date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="df-input">
                    @error('preferred_date') <p class="df-error">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="df-label">Preferred Time *</label>
                    <select wire:model="preferred_time" class="df-select">
                      <option value="">Select time...</option>
                      @foreach(['9:00 AM','10:00 AM','11:00 AM','1:00 PM','2:00 PM','3:00 PM','4:00 PM'] as $t)
                        <option value="{{ $t }}">{{ $t }} (GMT+0)</option>
                      @endforeach
                    </select>
                    @error('preferred_time') <p class="df-error">{{ $message }}</p> @enderror
                  </div>
                </div>
                <div class="df-field">
                  <label class="df-label">Specific Requirements</label>
                  <textarea wire:model="requirements" rows="3" class="df-textarea" placeholder="What features or use cases do you want to see demonstrated?"></textarea>
                </div>
                <div class="df-field">
                  <label class="df-label">Additional Message</label>
                  <textarea wire:model="message" rows="2" class="df-textarea" placeholder="Anything else you'd like us to know..."></textarea>
                </div>
                <button type="submit" class="btn-submit-demo">Submit Demo Request →</button>
              </form>
            </div>
            @endif
        </div>
    </section>
</div>
