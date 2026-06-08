<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Section;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SchoolOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الطلاب', Student::query()->where('status', 'active')->count())
                ->description('الطلاب النشطون')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('الفصول الدراسية', Section::query()->count())
                ->description('إجمالي الفصول')
                ->icon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('طلبات القبول الجديدة', Application::query()->where('status', 'pending')->count())
                ->description('قيد الانتظار')
                ->icon('heroicon-o-document-text')
                ->color('warning'),

            Stat::make('السنة الدراسية الحالية', AcademicYear::current()->value('name') ?? '—')
                ->description('السنة المفعّلة')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
        ];
    }
}
