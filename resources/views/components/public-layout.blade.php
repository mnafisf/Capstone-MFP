@props([
    'title' => null,
    'activeSection' => 'beranda',
    'showFooter' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'SAPHYR') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 antialiased" style="font-family: 'Barlow', sans-serif;">
        @include('partials.public.nav', ['activeSection' => $activeSection])

        {{ $slot }}

        @if ($showFooter)
            @include('partials.public.footer')
        @endif
    </body>
</html>
