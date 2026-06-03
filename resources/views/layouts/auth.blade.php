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



    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg">
        {{ $slot }}
    </div>

</div>

@livewireScripts
</body>
</html>
