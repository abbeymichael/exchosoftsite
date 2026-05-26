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
  .page-banner{min-height:380px;background:var(--navy);position:relative;overflow:hidden;display:flex;align-items:center;}
  .page-banner-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(0,184,219,0.14) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;}
  .page-banner-glow{position:absolute;inset:0;background:radial-gradient(circle at 75% 50%,rgba(0,184,219,0.1) 0%,transparent 60%);pointer-events:none;}
  .page-banner-content{position:relative;z-index:2;padding:4rem 6rem;max-width:700px;}
  .page-banner-crumb{display:flex;align-items:center;gap:0.5rem;margin-bottom:2rem;}
  .page-banner-crumb a{font-size:0.78rem;color:rgba(255,255,255,0.4);text-decoration:none;}
  .page-banner-crumb a:hover{color:var(--cyan);}
  .page-banner-crumb .sep{color:rgba(255,255,255,0.2);}
  .page-banner-crumb .ccurrent{font-size:0.78rem;color:var(--cyan);font-weight:500;}
  .page-banner-tag{display:inline-flex;background:rgba(0,184,219,0.1);border:1px solid rgba(0,184,219,0.2);color:var(--sky);padding:0.28rem 0.85rem;border-radius:100px;font-size:0.72rem;font-weight:600;letter-spacing:0.06em;margin-bottom:1.25rem;text-transform:uppercase;}
  .page-banner h1{font-family:var(--font-display);font-size:clamp(2rem,3.8vw,3.2rem);font-weight:800;color:var(--white);line-height:1.1;letter-spacing:-0.03em;margin-bottom:1rem;}
  .page-banner h1 em{color:var(--cyan);font-style:normal;}
  .page-banner-sub{font-size:1rem;color:rgba(255,255,255,0.55);max-width:540px;line-height:1.75;font-weight:300;}
  .listing-body{padding:4rem 6rem;}
  @media(max-width:1024px){.page-banner-content{padding:3rem 2rem;}.listing-body{padding:2.5rem 2rem;}}
</style>
<div class="page-banner">
  <div class="page-banner-dots"></div>
  <div class="page-banner-glow"></div>
  <div class="page-banner-content">
    <div class="page-banner-crumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="ccurrent">Case Studies</span></div>
    <div class="page-banner-tag">Proof of Impact</div>
    <h1>Real Results from <em>Real Businesses</em></h1>
    <p class="page-banner-sub">In-depth stories of how Exchosoft systems have transformed operations, increased revenue, and solved genuine problems across our markets.</p>
  </div>
</div>
    <section class="listing-body">

    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($studies->isEmpty())
            <div class="text-center py-20 text-slate-400">
                <p class="text-lg font-semibold">No case studies published yet.</p>
                <p class="text-sm mt-1">Add case studies from the admin panel.</p>
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($studies as $study)
                <a href="{{ route('site.case-studies.show', $study->slug) }}" wire:navigate
                   class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="bg-gradient-to-br from-cyan-50 to-violet-50 h-36 flex items-center justify-center px-6">
                        @if($study->client_logo)
                            <img src="{{ asset('storage/'.$study->client_logo) }}" alt="{{ $study->client_name }}" class="max-h-16 max-w-full object-contain">
                        @else
                            <p class="text-2xl font-bold text-slate-300">{{ substr($study->client_name, 0, 2) }}</p>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($study->is_featured)<span class="text-xs text-amber-500 font-semibold">⭐ Featured</span>@endif
                        <p class="text-xs text-slate-500 mt-1">{{ $study->client_name }} @if($study->client_industry)· {{ $study->client_industry }}@endif</p>
                        <h3 class="font-bold text-slate-900 mt-2 group-hover:text-cyan-700 transition-colors line-clamp-2">{{ $study->title }}</h3>
                        @if($study->results)
                        <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $study->results }}</p>
                        @endif
                        @if($study->metrics)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach(array_slice($study->metrics, 0, 2) as $metric)
                            <span class="rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">{{ $metric['value'] ?? '' }} {{ $metric['label'] ?? '' }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @if($studies->hasPages())<div class="mt-10">{{ $studies->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
