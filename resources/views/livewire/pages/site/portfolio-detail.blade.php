<?php

use App\Models\PortfolioItem;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public PortfolioItem $item;

    public function mount(string $slug): void
    {
        $this->item = PortfolioItem::published()->where('slug', $slug)->firstOrFail();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.portfolio-detail')->title($this->item->title . ' — ExchoSoft');
    }
}; ?>

<div>
    <x-page-banner
        height="sm"
        :title="$item->title"
        :subtitle="$item->client_name ? 'Client: ' . $item->client_name . ($item->client_industry ? ' — ' . $item->client_industry : '') : null"
        :tag="$item->category ? ucfirst($item->category) : 'Portfolio'"
        :breadcrumbs="[
            ['label'=>'Home','route'=>'home'],
            ['label'=>'Portfolio','route'=>'site.portfolio'],
            ['label'=>$item->title],
        ]"
    />
    <section class="py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $item->category }}</span>
                @if($item->is_featured)<span class="text-xs text-amber-500 font-semibold">⭐ Featured</span>@endif
            </div>
            <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ $item->title }}</h1>
            @if($item->client_name)<p class="text-slate-500 mb-6">Client: {{ $item->client_name }} @if($item->client_industry)— {{ $item->client_industry }}@endif</p>@endif

            <div class="bg-slate-100 rounded-2xl h-64 flex items-center justify-center mb-8">
                @if($item->cover_image)
                    <img src="{{ asset('storage/'.$item->cover_image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover rounded-2xl">
                @else
                    <p class="text-slate-400">Cover Image Placeholder</p>
                @endif
            </div>

            @if($item->description)
            <p class="text-slate-600 leading-relaxed mb-6">{{ $item->description }}</p>
            @endif

            @if($item->content)
            <div class="prose prose-slate max-w-none">{!! $item->content !!}</div>
            @endif

            <div class="flex flex-wrap gap-3 mt-8">
                @if($item->project_url)
                <a href="{{ $item->project_url }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">View Live</a>
                @endif
                @if($item->github_url)
                <a href="{{ $item->github_url }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">GitHub</a>
                @endif
                <a href="{{ route('site.portfolio') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">← All Work</a>
            </div>
        </div>
    </section>
</div>
