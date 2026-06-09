<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">فصولي وموادي</x-slot>

        @if ($rows->isEmpty())
            <p class="text-sm text-gray-500">لم يتم تعيينك على أي فصول بعد. تواصل مع الإدارة.</p>
        @else
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($rows as $row)
                    <div class="rounded-xl border border-gray-100 p-4 dark:border-white/10">
                        <div class="font-bold text-gray-800 dark:text-gray-100">{{ $row->subject ?? 'مادة' }}</div>
                        <div class="mt-1 text-sm text-gray-500">{{ $row->grade ?? '' }} — فصل {{ $row->section ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
