@props(['items' => []])

<div {{ $attributes->merge(['class' => 'faq-accordion space-y-3']) }}>
    @foreach ($items as $i => $item)
        <div class="faq-item content-card overflow-hidden" data-faq-item>
            <button
                type="button"
                class="faq-trigger flex w-full items-center justify-between gap-4 p-5 text-start transition hover:bg-surface"
                aria-expanded="false"
                aria-controls="faq-panel-{{ $i }}"
                id="faq-trigger-{{ $i }}"
            >
                <span class="font-display text-base font-black text-brand">{{ $item['question'] }}</span>
                <span class="faq-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand transition-transform duration-300" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                    </svg>
                </span>
            </button>
            <div
                id="faq-panel-{{ $i }}"
                role="region"
                aria-labelledby="faq-trigger-{{ $i }}"
                class="faq-panel hidden border-t border-slate-100 px-5 pb-5 pt-0"
            >
                <p class="pt-4 font-normal leading-8 text-muted">{{ $item['answer'] }}</p>
            </div>
        </div>
    @endforeach
</div>
