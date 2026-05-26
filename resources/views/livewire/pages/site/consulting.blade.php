<?php

use App\Models\ConsultingInquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Consulting & Services — Exchosoft Consult')] class extends Component
{
    public string $name         = '';
    public string $email        = '';
    public string $phone        = '';
    public string $company      = '';
    public string $inquiry_type = 'consulting';
    public string $subject      = '';
    public string $description  = '';
    public string $budget_range = '';
    public string $timeline     = '';
    public string $how_heard    = '';
    public bool   $submitted    = false;

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
<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');
  .page-banner { min-height: 400px; background: var(--navy); position: relative; overflow: hidden; display: flex; align-items: center; }
  .page-banner-dots { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(0,184,219,0.14) 1px, transparent 1px); background-size: 32px 32px; pointer-events: none; }
  .page-banner-glow { position: absolute; inset: 0; background: radial-gradient(circle at 75% 50%, rgba(0,184,219,0.1) 0%, transparent 60%); pointer-events: none; }
  .page-banner-content { position: relative; z-index: 2; padding: 4rem 6rem; max-width: 700px; }
  .page-banner-crumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; }
  .page-banner-crumb a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .page-banner-crumb a:hover { color: var(--cyan); }
  .page-banner-crumb .sep { color: rgba(255,255,255,0.2); font-size: 0.75rem; }
  .page-banner-crumb .cdot { width: 5px; height: 5px; border-radius: 50%; background: var(--cyan); display: inline-block; margin-right: 0.2rem; vertical-align: middle; }
  .page-banner-crumb .ccurrent { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .page-banner-tag { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2); color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; margin-bottom: 1.25rem; text-transform: uppercase; }
  .page-banner h1 { font-family: var(--font-display); font-size: clamp(2rem, 3.8vw, 3.2rem); font-weight: 800; color: var(--white); line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1rem; }
  .page-banner h1 em { color: var(--cyan); font-style: normal; }
  .page-banner-sub { font-size: 1rem; color: rgba(255,255,255,0.55); max-width: 540px; line-height: 1.75; font-weight: 300; }

  .consult-body { padding: 5rem 6rem; background: var(--white); }
  .services-intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: start; margin-bottom: 5rem; }
  .services-intro-left p { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.8; margin-top: 1rem; }
  .service-list { display: flex; flex-direction: column; gap: 1.25rem; }
  .service-row { display: flex; gap: 1.25rem; align-items: flex-start; }
  .service-row-num { font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: rgba(0,184,219,0.2); line-height: 1; min-width: 2rem; }
  .service-row h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.35rem; }
  .service-row p { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.7; }

  .services-grid-section { background: var(--ice); padding: 5rem 6rem; }
  .sg-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; margin-top: 3rem; }
  .sg-card { background: var(--white); border: 1px solid var(--border); border-radius: 12px; padding: 1.75rem; transition: border-color 0.2s, box-shadow 0.2s; }
  .sg-card:hover { border-color: var(--cyan); box-shadow: 0 8px 24px rgba(0,184,219,0.1); }
  .sg-icon { font-size: 2rem; margin-bottom: 1rem; display: block; }
  .sg-card h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; }
  .sg-card p { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.7; }

  .inquiry-section { background: var(--navy); padding: 5rem 6rem; }
  .inquiry-inner { max-width: 720px; margin: 0 auto; }
  .inquiry-header { text-align: center; margin-bottom: 3rem; }
  .inquiry-header h2 { font-family: var(--font-display); font-size: clamp(1.5rem,2.5vw,2rem); font-weight: 800; color: var(--white); letter-spacing: -0.02em; margin-bottom: 0.75rem; }
  .inquiry-header p { font-size: 0.9rem; color: rgba(255,255,255,0.55); font-weight: 300; }
  .inquiry-form { background: rgba(255,255,255,0.04); border: 1px solid rgba(0,184,219,0.15); border-radius: 16px; padding: 2.5rem; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
  .form-field { margin-bottom: 1rem; }
  .form-field:last-of-type { margin-bottom: 0; }
  .fi-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.5); margin-bottom: 0.4rem; font-family: var(--font-display); }
  .fi-input, .fi-select, .fi-textarea {
    width: 100%; padding: 0.7rem 0.9rem; border: 1px solid rgba(0,184,219,0.2); border-radius: 8px;
    font-size: 0.875rem; font-family: var(--font-body); background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.85); transition: border-color 0.2s;
  }
  .fi-input:focus, .fi-select:focus, .fi-textarea:focus { outline: none; border-color: var(--cyan); }
  .fi-select option { background: var(--navy); color: var(--white); }
  .fi-textarea { resize: none; }
  .fi-error { font-size: 0.75rem; color: #fca5a5; margin-top: 0.35rem; }
  .btn-submit-full { width: 100%; background: var(--cyan); color: var(--white); padding: 0.95rem; border-radius: 8px; border: none; cursor: pointer; font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; transition: background 0.2s; margin-top: 1.25rem; }
  .btn-submit-full:hover { background: var(--cyan-dark); }
  .submitted-box { background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.3); border-radius: 12px; padding: 3rem 2rem; text-align: center; }
  .submitted-box h3 { font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: #86efac; margin-bottom: 0.5rem; }
  .submitted-box p { font-size: 0.9rem; color: rgba(255,255,255,0.6); }

  @media (max-width:1024px) {
    .page-banner-content { padding: 3rem 2rem; }
    .consult-body { padding: 3.5rem 2rem; }
    .services-intro-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    .services-grid-section { padding: 3.5rem 2rem; }
    .sg-grid { grid-template-columns: 1fr 1fr; }
    .inquiry-section { padding: 3.5rem 2rem; }
    .form-row { grid-template-columns: 1fr; }
  }
  @media (max-width:640px) { .sg-grid { grid-template-columns: 1fr; } }
</style>

<!-- BANNER -->
<div class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-content">
    <div class="page-banner-crumb">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="sep">/</span>
      <span class="cdot"></span>
      <span class="ccurrent">Consulting</span>
    </div>
    <div class="page-banner-tag">Services</div>
    <h1>Technology Consulting Built on <em>Real Experience</em></h1>
    <p class="page-banner-sub">Strategic guidance and custom development for businesses that need technology that actually works — not theory, not templates, not compromises.</p>
  </div>
</div>

<!-- INTRO / SERVICES -->
<section class="consult-body">
  <div class="services-intro-grid">
    <div class="services-intro-left">
      <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:var(--cyan);text-transform:uppercase;margin-bottom:0.75rem;">What We Offer</p>
      <h2 style="font-family:var(--font-display);font-size:clamp(1.7rem,2.8vw,2.4rem);font-weight:800;color:var(--navy);letter-spacing:-0.03em;line-height:1.15;margin-bottom:1rem;">Advisory & Development Services</h2>
      <p>We offer technology consulting grounded in real-world experience across Africa, the Caribbean, and the diaspora. Whether you need strategic guidance, a bespoke system, or a technical partner who understands your market — we're built for that.</p>
      <p>We don't pitch generic solutions. We sit with you, understand your operations, and recommend what will actually work for your conditions, your team, and your budget.</p>
    </div>
    <div class="service-list">
      @foreach([
        ['01','Custom Software Development','Built from scratch for your specific operations. No forcing your workflows into pre-made molds — we architect around how you actually work.'],
        ['02','Technology Strategy & Consulting','Objective guidance on technology investments, system choices, and digital transformation — from people who\'ve done it across diverse markets.'],
        ['03','System Architecture & Design','Offline-first, cloud-connected, LAN-collaborative — we design architectures that match operational realities, not just best-case scenarios.'],
        ['04','Business Process Analysis','We map your workflows, identify inefficiencies, and show exactly where the right technology will create measurable improvement.'],
        ['05','Digital Transformation','Complete operational modernization that respects your business context, team capabilities, and infrastructure constraints.'],
        ['06','Ongoing Support & Partnership','Systems need to evolve. We stay involved as your technology partner — not just a vendor who disappears after launch.'],
      ] as [$num, $title, $desc])
      <div class="service-row">
        <div class="service-row-num">{{ $num }}</div>
        <div>
          <h3>{{ $title }}</h3>
          <p>{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- EXPERTISE GRID -->
<section class="services-grid-section">
  <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:var(--cyan);text-transform:uppercase;margin-bottom:0.75rem;">Our Expertise</p>
  <h2 style="font-family:var(--font-display);font-size:clamp(1.7rem,2.8vw,2.4rem);font-weight:800;color:var(--navy);letter-spacing:-0.03em;line-height:1.15;">Areas of Deep Experience</h2>
  <div class="sg-grid">
    @foreach([
      ['🏥','Healthcare Systems','Hospital management, pharmacy ops, laboratory systems — offline-first, zero data loss, compliant.'],
      ['⛪','Faith Organizations','Church management from membership to multi-branch financials, SMS comms, and attendance.'],
      ['🧺','Service Businesses','Laundry, dry cleaning, and service industry platforms with POS, Kanban boards, and analytics.'],
      ['🏦','Financial Services','Secure, auditable systems for insurance, assurance, and financial institutions.'],
      ['🌍','Heritage & Culture','Platforms for diaspora engagement, cultural preservation, and cross-continental communities.'],
      ['📱','Mobile-First Products','Applications designed from the ground up for mobile users on variable connectivity.'],
    ] as [$icon, $title, $desc])
    <div class="sg-card">
      <span class="sg-icon">{{ $icon }}</span>
      <h3>{{ $title }}</h3>
      <p>{{ $desc }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- INQUIRY FORM -->
<section class="inquiry-section" id="inquiry">
  <div class="inquiry-inner">
    <div class="inquiry-header">
      <h2>Send an Inquiry</h2>
      <p>Tell us about your project or requirements and we'll get back to you within 24–48 hours.</p>
    </div>

    @if($submitted)
    <div class="submitted-box">
      <div style="font-size:3rem;margin-bottom:1rem;">✓</div>
      <h3>Inquiry Received!</h3>
      <p>Thank you. We'll review your message and respond within 24–48 hours.</p>
    </div>
    @else
    <div class="inquiry-form">
      <form wire:submit="submit">
        <div class="form-row">
          <div>
            <label class="fi-label">Full Name *</label>
            <input wire:model="name" type="text" class="fi-input" placeholder="Your full name">
            @error('name') <p class="fi-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="fi-label">Email *</label>
            <input wire:model="email" type="email" class="fi-input" placeholder="your@email.com">
            @error('email') <p class="fi-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="fi-label">Phone</label>
            <input wire:model="phone" type="tel" class="fi-input" placeholder="Optional">
          </div>
          <div>
            <label class="fi-label">Company</label>
            <input wire:model="company" type="text" class="fi-input" placeholder="Organization name">
          </div>
          <div>
            <label class="fi-label">Inquiry Type</label>
            <select wire:model="inquiry_type" class="fi-select">
              <option value="consulting">Consulting</option>
              <option value="gig">Gig / Contract</option>
              <option value="contract">Long-term Contract</option>
              <option value="partnership">Partnership</option>
            </select>
          </div>
          <div>
            <label class="fi-label">Budget Range</label>
            <select wire:model="budget_range" class="fi-select">
              <option value="">Select range...</option>
              <option value="Under GHS 2,000">Under GHS 2,000</option>
              <option value="GHS 2,000 - 5,000">GHS 2,000 – 5,000</option>
              <option value="GHS 5,000 - 10,000">GHS 5,000 – 10,000</option>
              <option value="GHS 10,000 - 25,000">GHS 10,000 – 25,000</option>
              <option value="GHS 25,000+">GHS 25,000+</option>
              <option value="Open to discuss">Open to discuss</option>
            </select>
          </div>
        </div>
        <div class="form-field">
          <label class="fi-label">Subject *</label>
          <input wire:model="subject" type="text" class="fi-input" placeholder="Brief summary of what you need">
          @error('subject') <p class="fi-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-field">
          <label class="fi-label">Describe Your Project *</label>
          <textarea wire:model="description" rows="5" class="fi-textarea" placeholder="Tell us about your project, goals, challenges, and what success looks like..."></textarea>
          @error('description') <p class="fi-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-row">
          <div>
            <label class="fi-label">Timeline</label>
            <select wire:model="timeline" class="fi-select">
              <option value="">Select timeline...</option>
              <option value="ASAP">ASAP</option>
              <option value="1-4 weeks">1–4 weeks</option>
              <option value="1-3 months">1–3 months</option>
              <option value="3-6 months">3–6 months</option>
              <option value="6+ months">6+ months</option>
              <option value="Ongoing">Ongoing / Retainer</option>
            </select>
          </div>
          <div>
            <label class="fi-label">How did you hear about us?</label>
            <select wire:model="how_heard" class="fi-select">
              <option value="">Select...</option>
              <option value="google">Google Search</option>
              <option value="referral">Referral</option>
              <option value="social">Social Media</option>
              <option value="event">Event / Conference</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn-submit-full">Send Inquiry →</button>
      </form>
    </div>
    @endif
  </div>
</section>

</div>
