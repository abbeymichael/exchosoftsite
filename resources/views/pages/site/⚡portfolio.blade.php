<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\PortfolioItem;

new class extends Component
{
    #[Computed]
    public function items()
    {
        return PortfolioItem::where("status", "published")->orderBy("sort_order")->get();
    }
    //
};
?>

<div>
<x-page-banner
    tag="Our Work"
    tagIcon="grid_view"
    title='Projects We Are <span style="color:#00b8db;">Proud Of</span>'
    subtitle="A curated selection of the software, platforms, and systems we have shipped for clients across multiple industries and continents."
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Portfolio']]"
    glowX="75%"
    glowX2="25%"
>
  <svg slot="ornament" class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none" viewBox="0 0 180 180" fill="none">
    <polygon points="90,4 174,46 174,134 90,176 6,134 6,46" stroke="#00b8db" stroke-width="1"/><polygon points="90,30 150,63 150,117 90,150 30,117 30,63" stroke="#00b8db" stroke-width="1"/>
    <circle cx="90" cy="90" r="5" fill="#00b8db"/>
  </svg>
</x-page-banner>

    <section class="site-section bg-white px-6 py-20 md:px-24">
        <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($this->items as $item)
                <div class="rounded-2xl border-[1.5px] border-[rgba(0,184,219,0.2)] bg-white/70 p-7 hover:shadow-lg transition">
                    <h3 class="mb-3 font-bold text-xl">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $item->description }}</p>
                    <a href="{{ $item->project_url }}" target="_blank" class="text-[var(--cyan)] text-sm font-bold">VIEW PROJECT &rarr;</a>
                </div>
            @endforeach
        </div>
    </section>
</div>