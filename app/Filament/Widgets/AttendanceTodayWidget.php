<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\BulkAttendance;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceTodayWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today = today()->toDateString();

        $totalStudents = Student::query()->where('status', 'active')->count();

        $recordedStudents = Attendance::query()->whereDate('date', $today)->distinct()->count('student_id');
        $presentStudents = Attendance::query()->whereDate('date', $today)->whereIn('status', ['present', 'late'])->distinct()->count('student_id');
        $absentStudents = Attendance::query()->whereDate('date', $today)->where('status', 'absent')->distinct()->count('student_id');
        $notRecorded = max(0, $totalStudents - $recordedStudents);

        return [
            Stat::make('إجمالي الطلاب', $totalStudents)
                ->description('الطلاب النشطون')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('حاضرون اليوم', $presentStudents)
                ->description('سُجّل لهم حضور اليوم')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('غائبون اليوم', $absentStudents)
                ->description('سُجّل لهم غياب اليوم')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('لم يُسجَّل بعد', $notRecorded)
                ->description('اضغط لتسجيل الحضور')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning')
                ->url(BulkAttendance::getUrl()),
        ];
    }
}
