@props(['items' => []])

<div {{ $attributes->merge(['class' => 'gallery-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-3']) }}>
    @foreach ($items as $i => $item)
        <figure
            data-reveal
            data-reveal-delay="{{ ($i % 3) * 80 }}"
            data-lightbox
            data-lightbox-src="{{ $item['image'] }}"
            data-lightbox-caption="{{ $item['title'] }}"
            role="button"
            tabindex="0"
            aria-label="تكبير صورة {{ $item['title'] }}"
            class="gallery-item group relative cursor-pointer overflow-hidden rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
        >
            <img
                src="{{ $item['image'] }}"
                alt="{{ $item['title'] }}"
                class="h-64 w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
            >
            <span class="absolute end-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-white opacity-0 backdrop-blur transition duration-300 group-hover:opacity-100" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 8v6M8 11h6M19 11a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
            </span>
            <figcaption class="gallery-overlay absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-brand-dark/90 via-brand/20 to-transparent p-5 opacity-0 transition duration-300 group-hover:opacity-100">
                @if (! empty($item['category']))
                    <span class="mb-2 inline-flex w-fit rounded-full bg-accent/90 px-3 py-1 text-xs font-bold text-brand">{{ $item['category'] }}</span>
                @endif
                <span class="font-display text-lg font-black text-white">{{ $item['title'] }}</span>
            </figcaption>
        </figure>
    @endforeach
</div>
