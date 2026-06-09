<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex justify-end">
            <x-filament::button wire:click="markAllRead" color="gray" icon="heroicon-o-check">
                تعليم الكل كمقروء
            </x-filament::button>
        </div>

        @forelse ($this->notifications as $notification)
            @php $data = $notification->data; @endphp
            <div @class([
                'flex items-start gap-4 rounded-2xl border p-4',
                'border-gray-100 bg-white dark:bg-white/5' => $notification->read_at,
                'border-primary-200 bg-primary-50 dark:bg-primary-500/10' => ! $notification->read_at,
            ])>
                <x-filament::icon
                    :icon="$data['icon'] ?? 'heroicon-o-bell'"
                    @class([
                        'mt-1 h-6 w-6 shrink-0',
                        'text-danger-500' => ($data['color'] ?? '') === 'danger',
                        'text-info-500' => ($data['color'] ?? '') === 'info',
                        'text-success-500' => ($data['color'] ?? '') === 'success',
                        'text-warning-500' => ($data['color'] ?? '') === 'warning',
                    ])
                />
                <div class="flex-1">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $data['title'] ?? 'إشعار' }}</div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $data['body'] ?? '' }}</p>
                    <div class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                @if (! $notification->read_at)
                    <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-bold text-primary-600 hover:underline">
                        تعليم كمقروء
                    </button>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-gray-100 p-8 text-center text-gray-500">
                لا توجد إشعارات.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
