<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-950 dark:text-white">مرحبًا، {{ $name }} 👋</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $date }} • عدد الأبناء: {{ $childrenCount }}</p>
            </div>
            <x-filament::icon icon="heroicon-o-home" class="hidden h-12 w-12 text-primary-500 sm:block" />
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
