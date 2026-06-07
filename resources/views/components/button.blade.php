@props([
    'variant' => 'brand',
    'href' => null,
    'type' => 'button',
    'size' => 'md',
])

@php
    $classes = match ($variant) {
        'primary' => 'btn-primary',
        'brand' => 'btn-brand',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
        default => 'btn-brand',
    };

    $sizeClass = match ($size) {
        'lg' => 'px-8 py-4 text-lg',
        'sm' => 'px-5 py-2.5 text-sm',
        default => '',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$classes $sizeClass"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$classes $sizeClass"]) }}>
        {{ $slot }}
    </button>
@endif
