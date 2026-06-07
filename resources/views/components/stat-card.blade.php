@props(['value' => null, 'label', 'count' => null, 'suffix' => ''])

@php
    $displayCount = $count ?? (is_numeric($value) ? $value : null);
    $displaySuffix = $suffix ?: (is_string($value) && ! is_numeric($value) ? preg_replace('/[\d,.\s]/', '', $value) : '');
    $staticValue = $value && ! $displayCount ? $value : null;
@endphp

<div {{ $attributes->merge(['class' => 'rounded-3xl bg-white p-6 text-brand']) }}>
    @if ($displayCount !== null)
        <div class="text-4xl font-black" data-count="{{ $displayCount }}" data-count-suffix="{{ $displaySuffix }}">0{{ $displaySuffix }}</div>
    @else
        <div class="text-4xl font-black">{{ $staticValue }}</div>
    @endif
    <div class="mt-2 text-sm font-medium text-muted">{{ $label }}</div>
</div>
