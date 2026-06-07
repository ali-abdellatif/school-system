<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $counts = Application::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = (int) $counts->sum();
        $applicationsUrl = ApplicationResource::getUrl('index');

        return [
            Stat::make('إجمالي الطلبات', $total)
                ->description('جميع طلبات القبول')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->url($applicationsUrl),

            Stat::make('قيد الانتظار', (int) ($counts['pending'] ?? 0))
                ->description('تحتاج مراجعة')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url($applicationsUrl . '?tableFilters[status][value]=pending'),

            Stat::make('مقبول', (int) ($counts['approved'] ?? 0))
                ->description('طلبات مقبولة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->url($applicationsUrl . '?tableFilters[status][value]=approved'),

            Stat::make('مرفوض', (int) ($counts['rejected'] ?? 0))
                ->description('طلبات مرفوضة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->url($applicationsUrl . '?tableFilters[status][value]=rejected'),
        ];
    }
}
