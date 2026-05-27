<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.site')] class extends Component
{
    public CaseStudy $study;

    public function mount(string $slug): void
    {
        $this->study = CaseStudy::published()->where('slug', $slug)->firstOrFail();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.site.case-study-detail')->title($this->study->title . ' — ExchoSoft');
    }
}; ?>

<div>
    <x-page-banner
        height="sm"
        :title="$study->title"
        :subtitle="$study->client_name ? ($study->client_name . ($study->client_industry ? ' · ' . $study->client_industry : '')) : null"
        tag="Case Study"
        :breadcrumbs="[
            ['label'=>'Home','route'=>'home'],
            ['label'=>'Case Studies','route'=>'site.case-studies'],
            ['label'=>$study->title],
        ]"
    />
    <section class="py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ $study->title }}</h1>
            <p class="text-slate-500 mb-8">{{ $study->client_name }} @if($study->client_industry)· {{ $study->client_industry }}@endif</p>

            @if($study->metrics)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-8">
                @foreach($study->metrics as $m)
                <div class="rounded-xl bg-green-50 border border-green-100 p-4 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $m['value'] ?? '' }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $m['label'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
            @endif

            @if($study->challenge)
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900 mb-2">The Challenge</h2>
                <p class="text-slate-600 leading-relaxed">{{ $study->challenge }}</p>
            </div>
            @endif
            @if($study->solution)
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900 mb-2">Our Solution</h2>
                <p class="text-slate-600 leading-relaxed">{{ $study->solution }}</p>
            </div>
            @endif
            @if($study->results)
            <div class="mb-8">
                <h2 class="text-lg font-bold text-slate-900 mb-2">Results</h2>
                <p class="text-slate-600 leading-relaxed">{{ $study->results }}</p>
            </div>
            @endif

            @if($study->content)
            <div class="prose prose-slate max-w-none">{!! $study->content !!}</div>
            @endif

            <div class="flex gap-3 mt-8">
                <a href="{{ route('site.case-studies') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">← All Case Studies</a>
                <a href="{{ route('site.consulting') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">Work With Us</a>
            </div>
        </div>
    </section>
</div>
