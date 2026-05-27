<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Case Studies — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $studies = CaseStudy::published()->latest('published_at')->paginate(9);
        return view('livewire.pages.site.case-studies', compact('studies'));
    }
}; ?>

<div>
<style>
  .listing-body { padding: 4rem 6rem; }
  .cs-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; }
  .cs-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 16px;
    overflow: hidden; text-decoration: none; display: block;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
  }
  .cs-card:hover { border-color: var(--cyan); box-shadow: 0 12px 32px rgba(0,184,219,0.12); transform: translateY(-3px); }
  .cs-card-img {
    height: 140px; background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;
  }
  .cs-card-img img { max-height: 64px; max-width: 80%; object-fit: contain; }
  .cs-card-img-placeholder { font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; color: rgba(0,184,219,0.35); }
  .cs-featured-badge {
    position: absolute; top: 0.65rem; right: 0.65rem;
    background: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.3);
    color: #f59e0b; padding: 0.2rem 0.55rem; border-radius: 4px;
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
  }
  .cs-card-body { padding: 1.25rem 1.4rem 1rem; }
  .cs-card-industry { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--cyan); margin-bottom: 0.35rem; }
  .cs-card-client { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.5rem; }
  .cs-card-title { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--navy); line-height: 1.4; margin-bottom: 0.5rem; }
  .cs-card-results { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.6; }
  .cs-card-metrics { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
  .cs-metric { background: rgba(0,184,219,0.08); color: var(--cyan-deep); padding: 0.22rem 0.65rem; border-radius: 100px; font-size: 0.72rem; font-weight: 600; }
  .cs-card-footer { padding: 0.75rem 1.4rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
  .cs-view-link { font-size: 0.78rem; font-weight: 600; color: var(--cyan); font-family: var(--font-display); }
  .cs-empty { text-align: center; padding: 5rem 2rem; color: var(--text-muted); }
  .cs-empty p:first-child { font-size: 1rem; font-weight: 600; }
  .cs-empty p:last-child { font-size: 0.875rem; margin-top: 0.5rem; }
  @media(max-width:1024px){ .listing-body{padding:2.5rem 2rem;} .cs-grid{grid-template-columns:repeat(2,1fr);} }
  @media(max-width:640px){ .cs-grid{grid-template-columns:1fr;} }
</style>

<x-page-banner
    tag="Proof of Impact"
    title="Real Results from **Real Businesses**"
    subtitle="In-depth stories of how Exchosoft systems have transformed operations, increased revenue, and solved genuine problems across our markets."
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Case Studies']]"
    :stats="[['value'=>'10+','label'=>'Industries served'],['value'=>'3','label'=>'Continents'],['value'=>'100%','label'=>'Custom builds']]"
/>

<section class="listing-body">
  @if($studies->isEmpty())
  <div class="cs-empty">
    <p>No case studies published yet.</p>
    <p>Case studies added from the admin will appear here.</p>
  </div>
  @else
  <div class="cs-grid">
    @foreach($studies as $study)
    <a href="{{ route('site.case-studies.show', $study->slug) }}" wire:navigate class="cs-card">
      <div class="cs-card-img">
        @if($study->client_logo)
          <img src="{{ asset('storage/'.$study->client_logo) }}" alt="{{ $study->client_name }}">
        @else
          <div class="cs-card-img-placeholder">{{ strtoupper(substr($study->client_name ?? 'CS', 0, 2)) }}</div>
        @endif
        @if($study->is_featured)<span class="cs-featured-badge">Featured</span>@endif
      </div>
      <div class="cs-card-body">
        @if($study->client_industry)<div class="cs-card-industry">{{ $study->client_industry }}</div>@endif
        <div class="cs-card-client">{{ $study->client_name }}</div>
        <div class="cs-card-title">{{ Str::limit($study->title, 80) }}</div>
        @if($study->results)<div class="cs-card-results">{{ Str::limit($study->results, 90) }}</div>@endif
        @if($study->metrics)
        <div class="cs-card-metrics">
          @foreach(array_slice(is_array($study->metrics) ? $study->metrics : [], 0, 2) as $metric)
          <span class="cs-metric">{{ $metric['value'] ?? '' }} {{ $metric['label'] ?? '' }}</span>
          @endforeach
        </div>
        @endif
      </div>
      <div class="cs-card-footer">
        <span class="cs-view-link">Read Case Study →</span>
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--cyan)" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
    </a>
    @endforeach
  </div>
  @if($studies->hasPages())<div style="margin-top:2rem;">{{ $studies->links() }}</div>@endif
  @endif
</section>

<div class="site-cta-strip">
  <div>
    <h2>Want results like these?</h2>
    <p>Every case study started with a conversation. Let's talk about your project.</p>
  </div>
  <a href="{{ route('site.consulting') }}" wire:navigate class="btn-white-solid">Start a Conversation</a>
</div>
</div>
