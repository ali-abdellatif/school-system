@props([
    'eyebrow' => null,
    'title',
    'centered' => false,
    'dark' => false,
])

<div {{ $attributes->merge(['class' => $centered ? 'text-center' : '']) }}>
    @if ($eyebrow)
        <span @class([
            'section-eyebrow',
            'text-accent' => $dark,
        ])>{{ $eyebrow }}</span>
    @endif
    <h2 @class([
        'section-title',
        'mt-3' => filled($eyebrow),
        'text-white' => $dark,
    ])>{{ $title }}</h2>
    @if ($slot->isNotEmpty())
        <div class="mt-5 text-base leading-8 text-muted">{{ $slot }}</div>
    @endif
</div>
