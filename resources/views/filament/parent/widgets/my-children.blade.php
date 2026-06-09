<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">أبنائي</x-slot>

        @if ($cards->isEmpty())
            <p class="text-sm text-gray-500">لا يوجد أبناء مرتبطون بحسابك. تواصل مع إدارة المدرسة.</p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cards as $child)
                    <div class="rounded-2xl border border-gray-100 p-5 dark:border-white/10">
                        <div class="flex items-center gap-3">
                            @if ($child['photo'])
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($child['photo']) }}" alt="{{ $child['name'] }}" class="h-14 w-14 rounded-full object-cover">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-lg font-bold text-primary-700">{{ $child['initial'] }}</div>
                            @endif
                            <div>
                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $child['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $child['grade'] ?? '—' }} • فصل {{ $child['section'] ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                                <div class="text-xs text-gray-500">حضور هذا الشهر</div>
                                <div class="text-lg font-bold {{ ($child['attendance'] ?? 0) >= 90 ? 'text-green-600' : (($child['attendance'] ?? 0) >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $child['attendance'] !== null ? $child['attendance'] . '%' : '—' }}
                                </div>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                                <div class="text-xs text-gray-500">متوسط الدرجات</div>
                                <div class="text-lg font-bold text-primary-600">{{ $child['average'] !== null ? $child['average'] . '%' : '—' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
