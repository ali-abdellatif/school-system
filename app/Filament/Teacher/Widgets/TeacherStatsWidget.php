<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TeacherStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -9;

    protected function getStats(): array
    {
        $teacher = auth()->user()?->teacher;

        if (! $teacher) {
            return [];
        }

        $sectionIds = DB::table('teacher_section')->where('teacher_id', $teacher->id)->distinct()->pluck('section_id');
        $classesCount = DB::table('teacher_section')->where('teacher_id', $teacher->id)->count();
        $studentsCount = Student::query()->whereIn('section_id', $sectionIds)->where('status', 'active')->count();
        $recordedToday = Attendance::query()->where('teacher_id', $teacher->id)->whereDate('date', today())->exists();

        return [
            Stat::make('فصولي وموادي', $classesCount)
                ->description('التعيينات الحالية')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make('إجمالي طلابي', $studentsCount)
                ->description('في كل فصولي')
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('حضور اليوم', $recordedToday ? 'تم التسجيل' : 'لم يُسجَّل بعد')
                ->description($recordedToday ? 'سُجّل الحضور اليوم' : 'بانتظار التسجيل')
                ->icon('heroicon-o-clipboard-document-check')
                ->color($recordedToday ? 'success' : 'warning'),
        ];
    }
}
