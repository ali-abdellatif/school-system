@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'content-card']) }}>
    {{ $slot }}
</div>
