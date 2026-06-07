@props(['steps' => [], 'current' => 1])

<div class="step-wizard">
    <div class="mb-6 flex items-center justify-between text-xs font-bold text-muted">
        <span>الخطوة {{ $current }} من {{ count($steps) }}</span>
        <span>{{ round(($current / count($steps)) * 100) }}%</span>
    </div>

    <div class="mb-6 h-1.5 overflow-hidden rounded-full bg-slate-100">
        <div class="h-full rounded-full bg-brand transition-all duration-500" style="width: {{ round(($current / count($steps)) * 100) }}%"></div>
    </div>

    <div class="flex items-center">
        @foreach ($steps as $number => $label)
            <div class="flex min-w-0 items-center {{ ! $loop->last ? 'flex-1' : '' }}">
                <div class="flex min-w-0 flex-col items-center">
                    <div @class([
                        'step-circle',
                        'step-circle--active' => $current === $number,
                        'step-circle--done' => $current > $number,
                        'step-circle--pending' => $current < $number,
                    ])>
                        @if ($current > $number)
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $number }}
                        @endif
                    </div>
                    <span @class([
                        'mt-2.5 truncate text-xs font-bold',
                        'text-brand' => $current >= $number,
                        'text-slate-400' => $current < $number,
                    ])>{{ $label }}</span>
                </div>
                @if (! $loop->last)
                    <div @class([
                        'step-connector mx-3',
                        'step-connector--done' => $current > $number,
                    ])></div>
                @endif
            </div>
        @endforeach
    </div>
</div>
