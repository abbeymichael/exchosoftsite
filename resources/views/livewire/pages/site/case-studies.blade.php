<?php

use App\Models\CaseStudy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('Case Studies — ExchoSoft')] class extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $studies = CaseStudy::published()->latest('published_at')->paginate(9);
        return view('livewire.pages.site.case-studies', compact('studies'));
    }
}; ?>

<div>
    <section class="bg-slate-900 text-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Proof of Impact</p>
            <h1 class="text-4xl font-bold mb-4">Case Studies</h1>
            <p class="text-slate-400 max-w-xl mx-auto">Real stories of how ExchoSoft's solutions have transformed businesses and delivered measurable results.</p>
        </div>
    </section>

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
