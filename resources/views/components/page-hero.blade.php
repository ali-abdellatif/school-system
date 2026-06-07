@props([
    'title',
    'description' => null,
    'breadcrumbs' => [],
])

<section class="page-hero relative overflow-hidden bg-brand text-white">
    <div class="absolute inset-0 hero-pattern opacity-30"></div>
    <div class="absolute -start-24 top-0 h-64 w-64 rounded-full bg-accent/10 blur-3xl"></div>
    <div class="absolute -end-16 bottom-0 h-48 w-48 rounded-full bg-brand-light/30 blur-2xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 md:py-16">
        @if (count($breadcrumbs))
            <x-breadcrumb :items="$breadcrumbs" class="mb-6" />
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <h1 class="font-display text-3xl font-black leading-tight md:text-5xl">{{ $title }}</h1>
                @if ($description)
                    <p class="mt-4 max-w-2xl text-lg font-normal leading-8 text-blue-50">{{ $description }}</p>
                @endif
            </div>
            @if ($slot->isNotEmpty())
                <div class="max-w-sm rounded-2xl border border-white/15 bg-white/10 p-5 text-sm leading-7 text-blue-50 backdrop-blur">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</section>
