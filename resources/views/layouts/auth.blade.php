<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ExchoLicense — Admin Login' }}</title>
    @php $faviconIco = public_path('assets/images/icon.ico'); @endphp
    @if(file_exists($faviconIco) && filesize($faviconIco) > 0)
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased">

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 bg-slate-50">

    {{-- Brand --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        @php
            $iconPath = public_path('assets/images/icon.png');
            $logoPath = public_path('assets/images/logo.png');
            $hasIcon  = file_exists($iconPath) && filesize($iconPath) > 0;
            $hasLogo  = file_exists($logoPath) && filesize($logoPath) > 0;
        @endphp
        @if($hasIcon)
            <img src="{{ asset('assets/images/icon.png') }}" alt="ExchoLicense" class="mx-auto h-14 w-14 rounded-2xl object-cover shadow-lg">
        @else
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-600 shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
        @endif
        @if($hasLogo)
            <div class="mt-4"><img src="{{ asset('assets/images/logo.png') }}" alt="ExchoLicense" class="mx-auto h-7 w-auto"></div>
        @else
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">ExchoLicense</h1>
        @endif
        <p class="mt-1 text-sm text-slate-500">Admin Panel</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm">
        {{ $slot }}
    </div>

</div>

@livewireScripts
</body>
</html>
