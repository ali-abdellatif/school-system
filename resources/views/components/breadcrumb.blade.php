@props(['items' => []])

<nav aria-label="مسار التنقل" class="page-breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-sm">
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <svg class="h-4 w-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                @endif
                @if (isset($item['url']) && ! ($item['active'] ?? false))
                    <a href="{{ $item['url'] }}" class="text-blue-100 transition hover:text-accent">{{ $item['label'] }}</a>
                @else
                    <span @class(['font-bold', 'text-accent' => $item['active'] ?? false, 'text-blue-100' => ! ($item['active'] ?? false)])>
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
