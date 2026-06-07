@props(['icon', 'title'])

<div {{ $attributes->merge(['class' => 'feature-tile']) }}>
    <span class="feature-icon">{{ $icon }}</span>
    <h3>{{ $title }}</h3>
    <p>{{ $slot }}</p>
</div>
