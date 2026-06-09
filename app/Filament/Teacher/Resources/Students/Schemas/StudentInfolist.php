<?php

namespace App\Filament\Teacher\Resources\Students\Schemas;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الطالب')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('full_name')->label('الاسم'),
                        TextEntry::make('section.grade.name')->label('الصف')->placeholder('—'),
                        TextEntry::make('section.name')->label('الفصل')->placeholder('—'),
                        TextEntry::make('age')->label('العمر')->suffix(' سنة'),
                        TextEntry::make('phone')->label('الهاتف')->placeholder('—'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط', 'inactive' => 'غير نشط', 'graduated' => 'متخرج', 'transferred' => 'محوّل', default => $state,
                            }),
                    ]),

                Section::make('ملخّص الحضور (لموادي)')
                    ->schema([
                        TextEntry::make('attendance_summary')
                            ->label('')
                            ->state(function (Student $record): string {
                                $teacher = auth()->user()?->teacher;
                                $subjectIds = $teacher ? $teacher->assignedSubjectIds() : [];
                                if (empty($subjectIds)) {
                                    return 'لا توجد مواد معيّنة.';
                                }
                                $total = Attendance::query()->where('student_id', $record->id)->whereIn('subject_id', $subjectIds)->count();
                                $present = Attendance::query()->where('student_id', $record->id)->whereIn('subject_id', $subjectIds)->whereIn('status', ['present', 'late'])->count();
                                $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                                return "الحضور: {$present} من {$total} حصة ({$pct}%)";
                            }),
                    ]),
            ]);
    }
}
