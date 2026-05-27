@props([
    'tag'         => null,
    'title'       => '',
    'subtitle'    => null,
    'breadcrumbs' => [],   // [['label'=>'Home','route'=>'home'], ['label'=>'Current']]
    'theme'       => 'cyan',  // cyan | green | violet
    'height'      => 'md',    // sm | md | lg
    'glowPosition' => '75% 50%',
    'stats'       => [],   // optional [{label, value}]
    'badgeText'   => null,
])

@php
    $minH = match($height) {
        'sm'  => '300px',
        'lg'  => '520px',
        default => '400px',
    };
    $accentColor = match($theme) {
        'green'  => 'rgba(76,175,130,0.14)',
        'violet' => 'rgba(139,92,246,0.14)',
        default  => 'rgba(0,184,219,0.14)',
    };
    $glowColor = match($theme) {
        'green'  => 'rgba(76,175,130,0.12)',
        'violet' => 'rgba(139,92,246,0.12)',
        default  => 'rgba(0,184,219,0.1)',
    };
    $tagBg     = match($theme) {
        'green'  => 'rgba(76,175,130,0.12)',
        'violet' => 'rgba(139,92,246,0.12)',
        default  => 'rgba(0,184,219,0.1)',
    };
    $tagBorder = match($theme) {
        'green'  => 'rgba(76,175,130,0.25)',
        'violet' => 'rgba(139,92,246,0.25)',
        default  => 'rgba(0,184,219,0.22)',
    };
    $tagColor  = match($theme) {
        'green'  => '#4caf82',
        'violet' => '#a78bfa',
        default  => 'var(--sky)',
    };
    $titleAccentColor = match($theme) {
        'green'  => '#4caf82',
        'violet' => '#a78bfa',
        default  => 'var(--cyan)',
    };
    // Parse **text** → <em> in title
    $parsedTitle = preg_replace('/\*\*(.+?)\*\*/', '<em style="color:'.$titleAccentColor.';font-style:normal;">$1</em>', e($title));
@endphp

<style>
.pb-banner {
  background: var(--navy);
  position: relative; overflow: hidden;
  display: flex; align-items: center;
  min-height: {{ $minH }};
}
.pb-dots {
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, {{ $accentColor }} 1px, transparent 1px);
  background-size: 32px 32px; pointer-events: none;
}
.pb-accent-line {
  position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
  background: linear-gradient(180deg, transparent 0%, {{ $tagColor }} 40%, {{ $tagColor }} 60%, transparent 100%);
  opacity: 0.35;
}
.pb-glow {
  position: absolute; inset: 0;
  background-image:
    radial-gradient(circle at {{ $glowPosition }}, {{ $glowColor }} 0%, transparent 58%),
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.02) 0%, transparent 50%);
  pointer-events: none;
}
.pb-content {
  position: relative; z-index: 2;
  padding: 4rem 6rem 4rem;
  max-width: 780px;
}
.pb-crumb {
  display: flex; align-items: center; gap: 0.5rem;
  margin-bottom: 2rem;
}
.pb-crumb a {
  font-size: 0.78rem; color: rgba(255,255,255,0.38);
  text-decoration: none; transition: color 0.2s;
}
.pb-crumb a:hover { color: {{ $tagColor }}; }
.pb-crumb .pb-sep { color: rgba(255,255,255,0.18); font-size: 0.75rem; }
.pb-crumb .pb-cur {
  font-size: 0.78rem; color: {{ $tagColor }}; font-weight: 600;
}
.pb-crumb .pb-dot {
  width: 4px; height: 4px; border-radius: 50%;
  background: {{ $tagColor }}; display: inline-block;
  margin: 0 1px; opacity: 0.7;
}
.pb-tag {
  display: inline-flex; align-items: center; gap: 0.45rem;
  background: {{ $tagBg }}; border: 1px solid {{ $tagBorder }};
  color: {{ $tagColor }};
  padding: 0.28rem 0.9rem; border-radius: 100px;
  font-size: 0.72rem; font-weight: 700; letter-spacing: 0.07em;
  margin-bottom: 1.25rem; text-transform: uppercase; width: fit-content;
}
.pb-tag-dot {
  width: 5px; height: 5px; border-radius: 50%; background: {{ $tagColor }};
  animation: pb-pulse 2s ease-in-out infinite;
}
@keyframes pb-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.8); }
}
.pb-h1 {
  font-family: var(--font-display);
  font-size: clamp(2rem, 3.8vw, 3.2rem);
  font-weight: 800; color: var(--white);
  line-height: 1.1; letter-spacing: -0.03em;
  margin-bottom: 1.1rem;
}
.pb-subtitle {
  font-size: 1rem; color: rgba(255,255,255,0.52);
  max-width: 560px; line-height: 1.8; font-weight: 300;
}
.pb-stats {
  display: flex; gap: 2.5rem; margin-top: 2rem;
  flex-wrap: wrap;
}
.pb-stat-val {
  font-family: var(--font-display); font-size: 1.5rem;
  font-weight: 800; color: {{ $tagColor }}; line-height: 1;
}
.pb-stat-lbl {
  font-size: 0.75rem; color: rgba(255,255,255,0.4);
  margin-top: 0.2rem;
}
.pb-right {
  position: absolute; right: 5rem; top: 50%; transform: translateY(-50%);
  z-index: 3;
}
@media (max-width: 1024px) {
  .pb-content { padding: 3rem 2rem; }
  .pb-right { display: none; }
}
</style>

<div class="pb-banner">
  <div class="pb-dots"></div>
  <div class="pb-accent-line"></div>
  <div class="pb-glow"></div>
  <div class="pb-content">
    {{-- Breadcrumbs --}}
    @if(count($breadcrumbs) > 0)
    <div class="pb-crumb">
      @foreach($breadcrumbs as $idx => $crumb)
        @if($idx < count($breadcrumbs) - 1)
          @if(isset($crumb['route']))
            <a href="{{ route($crumb['route']) }}" wire:navigate>{{ $crumb['label'] }}</a>
          @else
            <a href="{{ $crumb['url'] ?? '#' }}" wire:navigate>{{ $crumb['label'] }}</a>
          @endif
          <span class="pb-sep">/</span>
        @else
          <span class="pb-dot"></span>
          <span class="pb-cur">{{ $crumb['label'] }}</span>
        @endif
      @endforeach
    </div>
    @endif

    {{-- Tag --}}
    @if($tag)
    <div class="pb-tag">
      <span class="pb-tag-dot"></span>
      {{ $tag }}
    </div>
    @endif

    {{-- Title --}}
    <h1 class="pb-h1">{!! $parsedTitle !!}</h1>

    {{-- Subtitle --}}
    @if($subtitle)
    <p class="pb-subtitle">{{ $subtitle }}</p>
    @endif

    {{-- Slot (extra content below) --}}
    {{ $slot }}

    {{-- Stats --}}
    @if(count($stats) > 0)
    <div class="pb-stats">
      @foreach($stats as $stat)
      <div>
        <div class="pb-stat-val">{{ $stat['value'] }}</div>
        <div class="pb-stat-lbl">{{ $stat['label'] }}</div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Right slot if provided --}}
  @isset($right)
  <div class="pb-right">{{ $right }}</div>
  @endisset
</div>
