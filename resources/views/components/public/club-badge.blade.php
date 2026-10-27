@props([
    'club' => null,
    'name' => null,
    'code' => null,
    'color' => null,
    'logo' => null,
    'size' => 'md',
])

@php
    $sizeClasses = [
        'sm' => 'home-club-badge !h-7 !w-7 !text-[10px]',
        'md' => 'home-club-badge',
        'lg' => 'home-club-badge-lg',
    ];

    $name ??= $club?->name;
    $code ??= $club?->code;
    $color ??= $club?->shirt_color;
    $logo ??= $club?->logo_path;

    $background = $color && preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#1e3a8a';

    if ($code) {
        $initials = strtoupper(substr($code, 0, 3));
    } else {
        $words = preg_split('/\s+/', trim((string) $name)) ?: [];
        $initials = '';

        foreach (array_slice($words, 0, 3) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }

        $initials = strtoupper($initials ?: '?');
    }
@endphp

<span class="{{ $sizeClasses[$size] ?? $sizeClasses['md'] }} overflow-hidden" style="background-color: {{ $background }};" title="{{ $name }}">
    @if ($logo)
        <img src="{{ asset('storage/'.$logo) }}" alt="{{ $name }}" class="h-full w-full object-cover">
    @else
        {{ $initials }}
    @endif
</span>
