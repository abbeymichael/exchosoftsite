<?php

use App\Models\ConsultingInquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Contact Us — Exchosoft Consult')] class extends Component
{
    public string $name         = '';
    public string $email        = '';
    public string $phone        = '';
    public string $company      = '';
    public string $inquiry_type = '';
    public string $budget       = '';
    public string $subject      = '';
    public string $description  = '';
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
            'description' => 'required|string|min:20',
        ]);

        ConsultingInquiry::create([
            'customer_user_id' => auth()->id(),
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'company'          => $this->company,
            'inquiry_type'     => $this->inquiry_type ?: 'contact',
            'subject'          => $this->subject,
            'description'      => $this->description,
            'budget_range'     => $this->budget,
            'timeline'         => $this->timeline,
            'how_heard'        => $this->how_heard,
        ]);

        $this->submitted = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.contact');
    }
}; ?>

<div>
<style>
  /* ── CONTACT PAGE ── */
  .contact-hero {
    min-height: 420px; background: var(--navy);
    position: relative; overflow: hidden; display: flex; align-items: center; padding: 3rem 0;
  }
  .contact-hero-particles { position: absolute; inset: 0; pointer-events: none; }
  .contact-radar-ring {
    position: absolute; top: 50%; left: 50%; border: 1px solid rgba(0,184,219,0.12);
    border-radius: 50%; transform: translate(-50%,-50%);
  }
  .contact-radar-sweep {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    width: 800px; height: 800px; border-radius: 50%;
    background: conic-gradient(from 0deg, rgba(0,184,219,0.25) 0deg, transparent 90deg);
    animation: contactSweep 5s linear infinite;
  }
  @keyframes contactSweep { from{transform:translate(-50%,-50%) rotate(0deg)} to{transform:translate(-50%,-50%) rotate(360deg)} }
  .contact-ping-icon {
    position: absolute; display: flex; align-items: center; justify-content: center;
    width: 44px; height: 44px; border-radius: 50%;
    animation: pingPulse 2.5s cubic-bezier(0,0,0.2,1) infinite;
  }
  @keyframes pingPulse { 0%{transform:scale(1)} 70%,100%{transform:scale(2.2);opacity:0} }
  .contact-hero-content { position: relative; z-index: 2; padding: 2rem 6rem; }
  .contact-hero-crumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
  .contact-hero-crumb a { font-size: 0.78rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
  .contact-hero-crumb a:hover { color: var(--cyan); }
  .contact-hero-crumb .sep { color: rgba(255,255,255,0.18); }
  .contact-hero-crumb .current { font-size: 0.78rem; color: var(--cyan); font-weight: 500; }
  .contact-hero-tag {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: rgba(0,184,219,0.1); border: 1px solid rgba(0,184,219,0.2);
    color: var(--sky); padding: 0.28rem 0.85rem; border-radius: 100px;
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
    margin-bottom: 1.5rem;
  }
  .contact-hero h1 {
    font-family: var(--font-display); font-size: clamp(2.2rem,4vw,3.4rem);
    font-weight: 800; color: var(--white); line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1rem;
  }
  .contact-hero h1 em { color: var(--cyan); font-style: normal; }
  .contact-hero-sub { font-size: 1rem; color: rgba(255,255,255,0.55); max-width: 520px; line-height: 1.75; font-weight: 300; }

  /* ── CONTACT BODY ── */
  .contact-body { padding: 5rem 6rem; background: var(--ice); }
  .contact-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 4rem; align-items: start; }

  /* Left panel */
  .contact-info-panel {}
  .contact-info-panel h2 { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; color: var(--navy); margin-bottom: 0.75rem; letter-spacing: -0.02em; }
  .contact-info-panel > p { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 2rem; }
  .contact-hq-card {
    background: rgba(255,255,255,0.7); backdrop-filter: blur(12px);
    border: 1.5px solid rgba(0,184,219,0.2); border-radius: 14px;
    padding: 1.5rem; border-left: 4px solid var(--cyan);
    margin-bottom: 1.5rem;
  }
  .contact-hq-card h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .contact-hq-card p { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.7; }
  .contact-hq-card .phone { margin-top: 0.75rem; font-family: var(--font-display); font-size: 0.88rem; font-weight: 600; color: var(--cyan); }
  .contact-locations-label { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem; }
  .contact-loc-chips { display: flex; flex-wrap: wrap; gap: 0.6rem; margin-bottom: 1.75rem; }
  .contact-loc-chip {
    display: flex; align-items: center; gap: 0.5rem;
    background: var(--white); border: 1px solid var(--border);
    padding: 0.35rem 0.85rem; border-radius: 100px;
    font-size: 0.78rem; color: var(--text-secondary);
  }
  .contact-loc-chip span { width: 6px; height: 6px; border-radius: 50%; background: var(--cyan); }
  .contact-data-viz {
    border-radius: 14px; overflow: hidden; height: 140px; position: relative;
    background: var(--navy); display: flex; align-items: center; justify-content: center;
  }
  .contact-data-viz-bar {
    position: absolute; bottom: 0; left: 0; right: 0; height: 80%;
    background: linear-gradient(to top, rgba(0,184,219,0.3), transparent);
    clip-path: polygon(0 80%, 10% 70%, 25% 85%, 40% 60%, 55% 75%, 70% 40%, 85% 60%, 100% 20%, 100% 100%, 0 100%);
  }
  .contact-data-viz-label { font-family: var(--font-display); font-size: 0.7rem; font-weight: 600; color: rgba(0,184,219,0.6); letter-spacing: 0.2em; text-transform: uppercase; position: relative; z-index: 2; }
  .contact-live-badge {
    position: absolute; top: 0.75rem; right: 0.75rem;
    display: flex; align-items: center; gap: 0.35rem;
    font-size: 0.65rem; color: rgba(0,184,219,0.6); font-family: var(--font-display);
  }
  .contact-live-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--cyan); animation: liveBlip 1.5s ease-in-out infinite; }
  @keyframes liveBlip { 0%,100%{opacity:1} 50%{opacity:0.3} }

  /* Form */
  .contact-form-card {
    background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
    border: 1.5px solid rgba(0,184,219,0.18); border-radius: 18px;
    padding: 2.5rem; position: relative; overflow: hidden;
  }
  .contact-form-card::before {
    content: ''; position: absolute; top: 0; left: 2.5rem; width: 1px; height: 100%;
    background: rgba(0,184,219,0.06); pointer-events: none;
  }
  .form-group { margin-bottom: 1.5rem; }
  .form-label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--text-muted); letter-spacing: 0.04em; margin-bottom: 0.5rem; font-family: var(--font-display); text-transform: uppercase; }
  .form-input {
    width: 100%; background: var(--ice); border: 1.5px solid var(--border);
    border-radius: 10px; padding: 0.8rem 1rem; font-size: 0.9rem;
    font-family: var(--font-body); color: var(--text-primary);
    transition: border-color 0.2s, box-shadow 0.2s; outline: none;
  }
  .form-input:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0,184,219,0.12); }
  .form-input::placeholder { color: var(--text-muted); }
  .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .form-submit {
    background: var(--navy); color: var(--white);
    padding: 1rem 2.5rem; border-radius: 10px; border: none; cursor: pointer;
    font-family: var(--font-display); font-size: 0.93rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.75rem;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  }
  .form-submit:hover { background: var(--cyan); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,184,219,0.3); }
  .form-privacy { font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem; font-style: italic; }
  .form-success {
    text-align: center; padding: 3rem 2rem;
  }
  .form-success-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(0,184,219,0.12); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
  .form-success h3 { font-family: var(--font-display); font-size: 1.3rem; font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; }
  .form-success p { font-size: 0.9rem; color: var(--text-secondary); }

  @media (max-width: 1024px) {
    .contact-hero-content { padding: 2rem 2rem 3rem; }
    .contact-body { padding: 3.5rem 2rem; }
    .contact-grid { grid-template-columns: 1fr; gap: 2.5rem; }
  }
  @media (max-width: 640px) {
    .contact-hero-content { padding: 1.5rem 1.25rem 3rem; }
    .form-grid-2 { grid-template-columns: 1fr; }
    .contact-form-card { padding: 1.5rem; }
  }
</style>

{{-- ── HERO ── --}}
<section class="contact-hero">
  {{-- Particles --}}
  <div class="contact-hero-particles" id="contact-particles"></div>
  {{-- Radar rings --}}
  <div class="contact-radar-ring" style="width:400px;height:400px;"></div>
  <div class="contact-radar-ring" style="width:600px;height:600px;"></div>
  <div class="contact-radar-ring" style="width:800px;height:800px;"></div>
  <div class="contact-radar-sweep"></div>
  {{-- Ping icons --}}
  <div class="contact-ping-icon" style="top:30%;left:20%;background:rgba(0,184,219,0.15);">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.7)" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
  </div>
  <div class="contact-ping-icon" style="bottom:25%;right:22%;background:rgba(0,184,219,0.15);animation-delay:1s;">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.7)" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.16h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
  </div>
  <div class="contact-ping-icon" style="top:22%;right:18%;background:rgba(122,207,232,0.12);animation-delay:2s;">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="rgba(122,207,232,0.7)" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
  </div>
  <div class="contact-ping-icon" style="bottom:38%;left:18%;background:rgba(0,184,219,0.12);animation-delay:3s;">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="rgba(0,184,219,0.6)" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
  </div>
  <div class="contact-hero-content">
    <div class="contact-hero-crumb">
      <a href="{{ route('home') }}" wire:navigate>Home</a>
      <span class="sep">/</span>
      <span class="current">Contact</span>
    </div>
    <div class="contact-hero-tag">
      <span style="width:5px;height:5px;border-radius:50%;background:rgba(122,207,232,0.7);"></span>
      Get In Touch
    </div>
    <h1>Let's Start a <em>Conversation</em></h1>
    <p class="contact-hero-sub">Building technology that works in your reality. Reach out to discuss how we can solve your technical challenges with systems engineered for African markets.</p>
  </div>
</section>

{{-- ── CONTACT BODY ── --}}
<section class="contact-body">
  <div class="contact-grid">
    {{-- LEFT: Contact Info --}}
    <div class="contact-info-panel reveal">
      <h2>We're here to listen.</h2>
      <p>Our team of consultants is ready to help. Whether you need custom software, technology consulting, or system architecture — we bridge the gap between your challenges and solutions that actually work.</p>

      <div class="contact-hq-card">
        <h3>
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--cyan)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Accra, Ghana HQ
        </h3>
        <p>Suite 402, Enterprise House<br>High Street, Accra, Ghana</p>
        <div class="phone">+233 24 555 0102</div>
      </div>

      <div class="contact-locations-label">Strategic Locations</div>
      <div class="contact-loc-chips">
        @foreach(['London, UK','Caribbean Hub','Lagos, Nigeria','Atlanta, USA'] as $loc)
        <div class="contact-loc-chip"><span></span> {{ $loc }}</div>
        @endforeach
      </div>

      <div class="contact-data-viz">
        <div class="contact-data-viz-bar"></div>
        <div class="contact-live-badge"><span class="contact-live-dot"></span> LIVE</div>
        <span class="contact-data-viz-label">Operational Integrity</span>
      </div>

      {{-- Contact Methods --}}
      <div style="margin-top:1.75rem;display:flex;flex-direction:column;gap:0.75rem;">
        @foreach([
          ['Email','contact@exchosoft.com','mailto:contact@exchosoft.com'],
          ['Website','exchosoft.com','https://exchosoft.com'],
        ] as [$label,$value,$href])
        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:var(--white);border:1px solid var(--border);border-radius:10px;">
          <span style="font-size:0.72rem;font-weight:700;letter-spacing:0.06em;color:var(--cyan);text-transform:uppercase;min-width:60px;font-family:var(--font-display);">{{ $label }}</span>
          <a href="{{ $href }}" style="font-size:0.88rem;color:var(--text-secondary);text-decoration:none;transition:color 0.2s;" class="hover:text-cyan">{{ $value }}</a>
        </div>
        @endforeach
      </div>
    </div>

    {{-- RIGHT: Contact Form --}}
    <div class="reveal" style="transition-delay:0.2s;">
      <div class="contact-form-card">
        @if($submitted)
          <div class="form-success">
            <div class="form-success-icon">
              <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="var(--cyan)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h3>Message Sent Successfully</h3>
            <p>Thank you for reaching out. Our team will get back to you within 1-2 business days.</p>
            <button wire:click="$set('submitted', false)" style="margin-top:1.5rem;font-size:0.85rem;color:var(--cyan);background:none;border:none;cursor:pointer;font-family:var(--font-display);font-weight:600;">Send Another Message</button>
          </div>
        @else
          <form wire:submit.prevent="submit">
            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label" for="c-name">Full Name *</label>
                <input class="form-input" id="c-name" wire:model="name" type="text" placeholder="John Doe" required>
                @error('name')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label class="form-label" for="c-email">Email Address *</label>
                <input class="form-input" id="c-email" wire:model="email" type="email" placeholder="j.doe@company.com" required>
                @error('email')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label class="form-label" for="c-phone">Phone Number</label>
                <input class="form-input" id="c-phone" wire:model="phone" type="tel" placeholder="+233 24 000 0000">
              </div>
              <div class="form-group">
                <label class="form-label" for="c-company">Company / Organisation</label>
                <input class="form-input" id="c-company" wire:model="company" type="text" placeholder="Your Company Name">
              </div>
              <div class="form-group">
                <label class="form-label" for="c-inquiry">Inquiry Type</label>
                <select class="form-input" id="c-inquiry" wire:model="inquiry_type">
                  <option value="">Select Inquiry Type</option>
                  <option value="consulting">Technical Consulting</option>
                  <option value="software">Custom Software Development</option>
                  <option value="architecture">System Architecture</option>
                  <option value="transformation">Digital Transformation</option>
                  <option value="partnership">Strategic Partnership</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="c-budget">Budget Range</label>
                <select class="form-input" id="c-budget" wire:model="budget">
                  <option value="">Select Range</option>
                  <option value="under-5k">Under $5,000</option>
                  <option value="5k-20k">$5,000 - $20,000</option>
                  <option value="20k-50k">$20,000 - $50,000</option>
                  <option value="50k-150k">$50,000 - $150,000</option>
                  <option value="150k+">$150,000+</option>
                </select>
              </div>
              <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label" for="c-subject">Subject *</label>
                <input class="form-input" id="c-subject" wire:model="subject" type="text" placeholder="Brief description of your project or question" required>
                @error('subject')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
              </div>
              <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label" for="c-desc">Describe Your Project or Challenge *</label>
                <textarea class="form-input" id="c-desc" wire:model="description" rows="5" placeholder="Tell us about your business, the problem you're trying to solve, and what success looks like..." required></textarea>
                @error('description')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label class="form-label" for="c-timeline">Expected Timeline</label>
                <select class="form-input" id="c-timeline" wire:model="timeline">
                  <option value="">Select Timeline</option>
                  <option value="asap">ASAP</option>
                  <option value="1-3months">1-3 Months</option>
                  <option value="3-6months">3-6 Months</option>
                  <option value="6-12months">6-12 Months</option>
                  <option value="ongoing">Ongoing / Long-term</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="c-heard">How Did You Hear About Us?</label>
                <select class="form-input" id="c-heard" wire:model="how_heard">
                  <option value="">Please Select</option>
                  <option value="search">Search Engine</option>
                  <option value="referral">Referral</option>
                  <option value="linkedin">LinkedIn</option>
                  <option value="event">Event / Conference</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <button class="form-submit" type="submit" wire:loading.attr="disabled">
              <span wire:loading.remove>Send Message</span>
              <span wire:loading>Sending...</span>
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="22" x2="11" y1="2" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
            <p class="form-privacy">By submitting this form, you agree to our privacy policy. Your information is handled securely and never shared with third parties.</p>
          </form>
        @endif
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  const pc = document.getElementById('contact-particles');
  if (!pc) return;
  for (let i = 0; i < 45; i++) {
    const p = document.createElement('div');
    Object.assign(p.style, {
      position: 'absolute', background: '#4cd9fd', borderRadius: '50%',
      pointerEvents: 'none', left: Math.random()*100+'%', top: Math.random()*100+'%',
      width: (Math.random()*3+1)+'px', height: (Math.random()*3+1)+'px',
      opacity: Math.random()*0.4+0.1
    });
    pc.appendChild(p);
    p.animate([
      {transform: 'translate(0,0)', opacity: p.style.opacity},
      {transform: `translate(${(Math.random()-0.5)*180}px,${(Math.random()-0.5)*180}px)`, opacity: 0}
    ], { duration: (10+Math.random()*20)*1000, iterations: Infinity, delay: Math.random()*5000, easing: 'linear' });
  }
})();
</script>
</div>
