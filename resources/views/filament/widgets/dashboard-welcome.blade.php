<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-bold text-gray-950 dark:text-white">
                    مرحبًا بك في لوحة تحكم {{ $schoolName }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $date }}
                    <span class="mx-2">•</span>
                    السنة الدراسية الحالية:
                    <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $academicYear }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-filament::button
                    tag="a"
                    :href="$applicationsUrl"
                    icon="heroicon-o-clipboard-document-list"
                    color="warning"
                    :badge="$pendingCount > 0 ? $pendingCount : null"
                    badge-color="danger"
                >
                    طلبات القبول الجديدة
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="$studentCreateUrl"
                    icon="heroicon-o-user-plus"
                    color="primary"
                >
                    إضافة طالب
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="$sectionsUrl"
                    icon="heroicon-o-user-group"
                    color="gray"
                >
                    عرض الفصول
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
