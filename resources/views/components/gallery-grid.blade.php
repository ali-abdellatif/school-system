@props(['items' => []])

<div {{ $attributes->merge(['class' => 'gallery-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-3']) }}>
    @foreach ($items as $i => $item)
        <figure
            data-reveal
            data-reveal-delay="{{ ($i % 3) * 80 }}"
            class="gallery-item group relative overflow-hidden rounded-2xl"
        >
            <img
                src="{{ $item['image'] }}"
                alt="{{ $item['title'] }}"
                class="h-64 w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
            >
            <figcaption class="gallery-overlay absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-brand-dark/90 via-brand/20 to-transparent p-5 opacity-0 transition duration-300 group-hover:opacity-100">
                @if (! empty($item['category']))
                    <span class="mb-2 inline-flex w-fit rounded-full bg-accent/90 px-3 py-1 text-xs font-bold text-brand">{{ $item['category'] }}</span>
                @endif
                <span class="font-display text-lg font-black text-white">{{ $item['title'] }}</span>
            </figcaption>
        </figure>
    @endforeach
</div>
