<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class MyClassesWidget extends Widget
{
    protected string $view = 'filament.teacher.widgets.my-classes';

    protected static ?int $sort = -8;

    protected int|string|array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $teacher = auth()->user()?->teacher;

        $rows = collect();
        if ($teacher) {
            $rows = DB::table('teacher_section as ts')
                ->where('ts.teacher_id', $teacher->id)
                ->leftJoin('sections', 'sections.id', '=', 'ts.section_id')
                ->leftJoin('grades', 'grades.id', '=', 'sections.grade_id')
                ->leftJoin('subjects', 'subjects.id', '=', 'ts.subject_id')
                ->selectRaw('subjects.name as subject, grades.name as grade, sections.name as section')
                ->orderBy('grades.level')
                ->get();
        }

        return ['rows' => $rows];
    }
}
