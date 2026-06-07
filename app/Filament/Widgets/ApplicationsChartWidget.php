<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ApplicationsChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'طلبات القبول — آخر 30 يوماً';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $dates = collect(range(0, 29))->map(fn (int $day) => $start->copy()->addDays($day)->format('Y-m-d'));

        $counts = Application::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'طلبات جديدة',
                    'data' => $dates->map(fn (string $date) => (int) ($counts[$date] ?? 0))->values()->all(),
                    'borderColor' => '#1e3a5f',
                    'backgroundColor' => 'rgba(30, 58, 95, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates->map(fn (string $date) => Carbon::parse($date)->locale('ar')->translatedFormat('d M'))->all(),
        ];
    }
}
