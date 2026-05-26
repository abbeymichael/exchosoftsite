<?php

use App\Models\WhitePaper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('White Papers — Exchosoft Consult')] class extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $papers = WhitePaper::published()->latest('published_at')->paginate(9);
        return view('livewire.pages.site.white-papers', compact('papers'));
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
    <div class="page-banner-crumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="ccurrent">White Papers</span></div>
    <div class="page-banner-tag">Deep Dives</div>
    <h1>Technical Research & <em>Industry Analysis</em></h1>
    <p class="page-banner-sub">In-depth technical guides, ROI analyses, and research papers from the Exchosoft team — including the ChurchOps ROI white paper and more.</p>
  </div>
</div>

    <section class="listing-body">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($papers->isEmpty())
            <div class="text-center py-20 text-slate-400">
                <p class="text-lg font-semibold">No white papers published yet.</p>
                <p class="text-sm mt-1">Add white papers from the admin panel.</p>
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($papers as $paper)
                <div class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="bg-gradient-to-br from-violet-50 to-cyan-50 h-32 flex items-center justify-center px-6">
                        <svg class="h-14 w-14 text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 capitalize">{{ $paper->category }}</span>
                            @if($paper->is_gated)<span class="inline-flex items-center rounded-full bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-700">Gated</span>@endif
                        </div>
                        <h3 class="font-bold text-slate-900 line-clamp-2">{{ $paper->title }}</h3>
                        @if($paper->summary)<p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $paper->summary }}</p>@endif
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-xs text-slate-400">{{ $paper->downloads }} downloads</p>
                            @if($paper->is_gated)
                            <a href="{{ route('customer.register') }}" wire:navigate class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-violet-700 transition-colors">
                                Register to Download
                            </a>
                            @else
                            @if($paper->file_path)
                            <a href="{{ asset('storage/'.$paper->file_path) }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-cyan-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-700 transition-colors">
                                Download PDF
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($papers->hasPages())<div class="mt-10">{{ $papers->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
