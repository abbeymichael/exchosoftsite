{{--
  page-banner.blade.php  — v5 inner-page banner component
  Matches the design from "all-banners for inner pages.html"

  Props:
    tag         – pill badge text (string)
    tagIcon     – Material Symbol name for badge icon (string)
    title       – headline; wrap text in <span class="text-cyan"> for cyan highlights
    subtitle    – paragraph below headline (string)
    breadcrumbs – array of ['label','route'?,'url'?] — last item = current (no link)
    ornament    – raw SVG string for the right decorative element (optional)
    glowX       – CSS % for radial glow horizontal position, default '70%'
    glowX2      – CSS % for secondary glow, default '15%'
    chips       – array of ['icon','text','accent'?] — accent=true gives cyan tint
    stats       – array of ['value','label'] shown below subtitle
--}}

@props([
    'tag'         => null,
    'tagIcon'     => null,
    'title'       => '',
    'subtitle'    => null,
    'breadcrumbs' => [],
    'ornament'    => null,
    'glowX'       => '70%',
    'glowX2'      => '15%',
    'chips'       => [],
    'stats'       => [],
])

<div class="relative overflow-hidden banner-glow py-20"
     style="background:#08121d;border-bottom:1px solid rgba(0,184,219,.1);--gx:{{ $glowX }};--gx2:{{ $glowX2 }};">

  {{-- Grid lines --}}
  <div class="absolute inset-0 opacity-[.03] pointer-events-none"
       style="background-image:linear-gradient(#00b8db 1px,transparent 1px),linear-gradient(90deg,#00b8db 1px,transparent 1px);background-size:60px 60px;"></div>

  {{-- Top accent line --}}
  <div class="absolute top-0 inset-x-0 h-0.5"
       style="background:linear-gradient(90deg,transparent,rgba(0,184,219,.6) 30%,rgba(76,217,253,.8) 50%,rgba(0,184,219,.6) 70%,transparent);"></div>

  {{-- Right ornament (optional) --}}
  @if($ornament)
  <div class="absolute right-[7%] top-1/2 -translate-y-1/2 w-44 h-44 opacity-[.06] pointer-events-none">
    {!! $ornament !!}
  </div>
  @endif

  {{-- Content --}}
  <div class="relative z-10 max-w-6xl mx-auto px-6 md:px-10">

    {{-- Breadcrumbs --}}
    @if(count($breadcrumbs) > 0)
    <nav class="d1 animate-[fadeUp_.55s_ease_both] flex items-center gap-1.5 text-[11px] font-medium tracking-widest uppercase mb-5"
         style="color:rgba(255,255,255,.4);animation:fadeUp .55s ease .05s both;">
      @foreach($breadcrumbs as $idx => $crumb)
        @if($idx < count($breadcrumbs) - 1)
          @if(isset($crumb['route']))
            <a href="{{ route($crumb['route']) }}" wire:navigate
               class="hover:text-cyan transition-colors" style="color:rgba(255,255,255,.4);">{{ $crumb['label'] }}</a>
          @else
            <a href="{{ $crumb['url'] ?? '#' }}" wire:navigate
               class="hover:text-cyan transition-colors" style="color:rgba(255,255,255,.4);">{{ $crumb['label'] }}</a>
          @endif
          <span style="color:rgba(0,184,219,.3);">›</span>
        @else
          <span class="font-semibold" style="color:#00b8db;">{{ $crumb['label'] }}</span>
        @endif
      @endforeach
    </nav>
    @endif

    {{-- Tag pill --}}
    @if($tag)
    <div class="d2 animate-[fadeUp_.55s_ease_both] inline-flex items-center gap-1.5 mb-4 rounded-full px-3.5 py-1 text-[11px] font-semibold tracking-widest uppercase"
         style="background:rgba(0,184,219,.08);border:1px solid rgba(0,184,219,.2);color:#00b8db;animation:fadeUp .55s ease .14s both;">
      @if($tagIcon)
        <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 1">{{ $tagIcon }}</span>
      @endif
      {{ $tag }}
    </div>
    @endif

    {{-- Title --}}
    <h1 class="d3 font-syne text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-[1.1] mb-3"
        style="font-family:'Syne',sans-serif;animation:fadeUp .55s ease .23s both;">
      {!! $title !!}
    </h1>

    {{-- Subtitle --}}
    @if($subtitle)
    <p class="d4 text-base leading-relaxed max-w-xl"
       style="color:rgba(255,255,255,.5);animation:fadeUp .55s ease .33s both;">
      {{ $subtitle }}
    </p>
    @endif

    {{-- Extra slot --}}
    {{ $slot }}

    {{-- Chips --}}
    @if(count($chips) > 0)
    <div class="d4 flex flex-wrap items-center gap-3 mt-4"
         style="animation:fadeUp .55s ease .33s both;">
      @foreach($chips as $chip)
      <span class="inline-flex items-center gap-1.5 text-[11px] font-medium px-3 py-1 rounded-md"
            style="{{ ($chip['accent'] ?? false)
                ? 'background:rgba(0,184,219,.07);border:1px solid rgba(0,184,219,.25);color:#00b8db;'
                : 'background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.55);' }}">
        @if(!empty($chip['icon']))
          <span class="material-symbols-outlined text-[13px]">{{ $chip['icon'] }}</span>
        @endif
        {{ $chip['text'] }}
      </span>
      @endforeach
    </div>
    @endif

    {{-- Stats --}}
    @if(count($stats) > 0)
    <div class="d5 flex flex-wrap gap-8 mt-6" style="animation:fadeUp .55s ease .42s both;">
      @foreach($stats as $stat)
      <div>
        <div class="font-syne font-extrabold text-2xl" style="color:#00b8db;font-family:'Syne',sans-serif;">{{ $stat['value'] }}</div>
        <div class="text-[12px] mt-0.5" style="color:rgba(255,255,255,.4);">{{ $stat['label'] }}</div>
      </div>
      @endforeach
    </div>
    @endif

  </div>
</div>

@once
<style>
  @keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
  }
  .banner-glow::after {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background:
      radial-gradient(ellipse 55% 80% at var(--gx,70%) 50%, rgba(0,184,219,.11) 0%, transparent 70%),
      radial-gradient(ellipse 28% 45% at var(--gx2,15%) 85%, rgba(122,207,232,.05) 0%, transparent 60%);
  }
</style>
@endonce
