<?php

use App\Models\WhitePaper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.site')] #[Title('White Papers — ExchoSoft')] class extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $papers = WhitePaper::published()->latest('published_at')->paginate(9);
        return view('livewire.pages.site.white-papers', compact('papers'));
    }
}; ?>

<div>
    <section class="bg-slate-900 text-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-cyan-400 mb-3">Deep Dives</p>
            <h1 class="text-4xl font-bold mb-4">White Papers</h1>
            <p class="text-slate-400 max-w-xl mx-auto">In-depth research, technical guides, and industry analysis from the ExchoSoft team.</p>
        </div>
    </section>

    <section class="py-14">
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
