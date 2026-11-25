@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'home-section-head items-start']) }}>
    <div>
        @if ($eyebrow)
            <p class="home-section-eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 class="home-section-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="home-section-subtitle">{{ $subtitle }}</p>
        @endif

        @if (isset($actions))
            <div class="home-section-actions mt-3 justify-start">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
