<?php

use App\Models\PortfolioItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Portfolio — Exchosoft Consult')] class extends Component
{
    use WithPagination;
    public string $filterCategory = '';

    public function render(): \Illuminate\View\View
    {
        $items = PortfolioItem::published()
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('sort_order')->latest()->paginate(12);

        return view('livewire.pages.site.portfolio', compact('items'));
    }
}; ?>

<div>
<style>
  .page-banner{min-height:380px;background:var(--navy);position:relative;overflow:hidden;display:flex;align-items:center;}
  .page-banner-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(0,184,219,0.14) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;}
  .page-banner-glow{position:absolute;inset:0;background:radial-gradient(circle at 75% 50%,rgba(0,184,219,0.1) 0%,transparent 60%);pointer-events:none;}
  .page-banner-content{position:relative;z-index:2;padding:4rem 6rem;max-width:700px;}
  .page-banner-crumb{display:flex;align-items:center;gap:0.5rem;margin-bottom:2rem;}
  .page-banner-crumb a{font-size:0.78rem;color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;}
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
    <div class="page-banner-crumb"><a href="{{ route('home') }}" wire:navigate>Home</a><span class="sep">/</span><span class="ccurrent">Portfolio</span></div>
    <div class="page-banner-tag">Our Work</div>
    <h1>Projects That <em>Actually Worked</em></h1>
    <p class="page-banner-sub">A showcase of systems we've built — from hospital management and church platforms to financial services tools and heritage preservation systems.</p>
  </div>
</div>

    <section class="listing-body">
        <div>
            <div class="flex flex-wrap gap-2 mb-8">
                <button wire:click="$set('filterCategory', '')" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors {{ $filterCategory === '' ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">All</button>
                @foreach(['software','web','mobile','design','consulting'] as $cat)
                <button wire:click="$set('filterCategory', '{{ $cat }}')" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors capitalize {{ $filterCategory === $cat ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $cat }}</button>
                @endforeach
            </div>

            @if($items->isEmpty())
            <div class="text-center py-20 text-slate-400">
                <p class="text-lg font-semibold">No portfolio items yet.</p>
                <p class="text-sm mt-1">Add portfolio projects from the admin panel.</p>
            </div>
            @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                <a href="{{ route('site.portfolio.show', $item->slug) }}" wire:navigate class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="bg-slate-100 h-44 flex items-center justify-center overflow-hidden relative">
                        @if($item->cover_image)
                            <img src="{{ asset('storage/'.$item->cover_image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                        @if($item->is_featured)
                            <span class="absolute top-2 left-2 rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-white">Featured</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-medium text-slate-500 capitalize">{{ $item->category }}</span>
                        <h3 class="font-bold text-slate-900 mt-1 group-hover:text-cyan-700 transition-colors">{{ $item->title }}</h3>
                        @if($item->client_name)<p class="text-xs text-slate-400 mt-1">Client: {{ $item->client_name }}</p>@endif
                        @if($item->duration)<p class="text-xs text-slate-400">Duration: {{ $item->duration }}</p>@endif
                        @if($item->description)<p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $item->description }}</p>@endif
                        @if($item->tech_stack)
                        <div class="flex flex-wrap gap-1 mt-3">
                            @foreach(array_slice($item->tech_stack, 0, 4) as $tech)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $tech }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @if($items->hasPages())<div class="mt-10">{{ $items->links() }}</div>@endif
            @endif
        </div>
    </section>
</div>
