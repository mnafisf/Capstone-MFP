@props([
    'name',
])

@php
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',

        'database' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" /><path stroke-linecap="round" stroke-linejoin="round" d="M20 4v5c0 1.657-3.582 3-8 3s-8-1.343-8-3V4" /><path stroke-linecap="round" stroke-linejoin="round" d="M20 9v5c0 1.657-3.582 3-8 3s-8-1.343-8-3V9" />',

        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15" /><rect x="9" y="2.5" width="6" height="4" rx="1" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 11h6M9 15h4" />',

        'clipboard-check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15" /><rect x="9" y="2.5" width="6" height="4" rx="1" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 14.5l2 2 4-4" />',

        'grid' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.5" /><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" /><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" /><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />',

        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20" /><circle cx="9.5" cy="7.5" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 20v-1.5a4 4 0 0 0-3-3.87" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 4.13a3.5 3.5 0 0 1 0 6.74" />',

        'user' => '<circle cx="12" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />',

        'user-check' => '<circle cx="9" cy="7.5" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20a6.5 6.5 0 0 1 13 0" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.5l2 2 3.5-3.5" />',

        'id-card' => '<rect x="2.5" y="5" width="19" height="14" rx="2" /><circle cx="8.5" cy="11" r="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 16.3c.6-1.4 1.7-2.1 3-2.1s2.4.7 3 2.1" /><path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5h4M14.5 13h4" />',

        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11z" /><circle cx="12" cy="10" r="2.5" />',

        'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7.5 3v5.5c0 4.6-3.1 8-7.5 9.5-4.4-1.5-7.5-4.9-7.5-9.5V6L12 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2.2 2.2L15.5 10" />',

        'check-circle' => '<circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M8.5 12.2l2.5 2.5 4.5-5" />',

        'swap' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8h13m0 0l-3-3m3 3l-3 3" /><path stroke-linecap="round" stroke-linejoin="round" d="M20 16H7m0 0l3-3m-3 3l3 3" />',

        'sliders' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7h9M19 7h1M4 17h3M13 17h7" /><circle cx="16" cy="7" r="2" /><circle cx="10" cy="17" r="2" />',

        'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v4M16 3v4M3.5 10h17" />',

        'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8v4.5a4 4 0 0 1-8 0V4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 5.5H5.5V7A3.5 3.5 0 0 0 9 10.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 5.5h2.5V7A3.5 3.5 0 0 1 15 10.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 12.5V16" /><path stroke-linecap="round" stroke-linejoin="round" d="M8.5 20h7" /><path stroke-linecap="round" stroke-linejoin="round" d="M10 20v-4M14 20v-4" />',

        'list' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 6.5h11M9 12h11M9 17.5h11M4.5 6.5h.01M4.5 12h.01M4.5 17.5h.01" />',

        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 4.5h2.5A1.5 1.5 0 0 1 19 6v12a1.5 1.5 0 0 1-1.5 1.5H15" /><path stroke-linecap="round" stroke-linejoin="round" d="M10 8l-4 4 4 4" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 12h9" />',
    ];

    $icon = $icons[$name] ?? '';
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    {{ $attributes->merge(['class' => 'shrink-0']) }}
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="1.5"
    aria-hidden="true"
>
    {!! $icon !!}
</svg>
