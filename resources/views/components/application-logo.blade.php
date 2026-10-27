@props(['class' => null])

@php
    $hasImageLogo = file_exists(public_path('logo.png'));
    $logoClass = $class ?? ($hasImageLogo ? 'h-10 w-auto max-h-10 object-contain' : 'h-10 w-10');
@endphp

@if ($hasImageLogo)
    <img
        src="{{ asset('logo.png') }}"
        alt="SAPHYR"
        class="{{ $logoClass }}"
        {{ $attributes->except('class') }}
    >
@else
    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="{{ $logoClass }}" {{ $attributes->except('class') }}>
        <circle cx="32" cy="32" r="30" fill="currentColor" fill-opacity="0.12" stroke="currentColor" stroke-width="2"/>
        <path fill="currentColor" d="M32 10l6.2 11.4 12.8 2.1-9.1 9.5 2.1 12.8L32 40.8l-9.9 5.2 2.1-12.8-9.1-9.5 12.8-2.1L32 10z"/>
        <path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" d="M32 10l6.2 11.4L32 22.4 25.8 21.4 32 10zM38.2 21.4l12.8 2.1-9.1 9.5-6.2-1.6 2.5-10zM29.1 31.4l-6.2 1.6-9.1-9.5 12.8-2.1 2.5 10zM22.1 32.9l2.1 12.8L32 40.8l7.8-5.1 2.1-12.8-9.9 5.2-9.9-5.2z"/>
    </svg>
@endif
