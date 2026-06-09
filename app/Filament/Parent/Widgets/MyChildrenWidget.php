<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Models\Student;
use Filament\Widgets\Widget;

class MyChildrenWidget extends Widget
{
    protected string $view = 'filament.parent.widgets.my-children';

    protected static ?int $sort = -9;

    protected int|string|array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $children = auth()->user()?->students()->with('section.grade')->get() ?? collect();
        $monthStart = now()->startOfMonth()->toDateString();

        $cards = $children->map(function (Student $child) use ($monthStart): array {
            $total = Attendance::query()->where('student_id', $child->id)->whereDate('date', '>=', $monthStart)->count();
            $present = Attendance::query()->where('student_id', $child->id)->whereDate('date', '>=', $monthStart)->whereIn('status', ['present', 'late'])->count();
            $attPct = $total > 0 ? round(($present / $total) * 100, 1) : null;

            $grades = GradeRecord::query()->where('student_id', $child->id)->get();
            $avg = $grades->isEmpty() ? null : round($grades->avg(fn (GradeRecord $g): float => $g->percentage), 1);

            return [
                'name' => $child->full_name,
                'photo' => $child->photo,
                'initial' => mb_substr($child->first_name, 0, 1),
                'grade' => $child->section?->grade?->name,
                'section' => $child->section?->name,
                'attendance' => $attPct,
                'average' => $avg,
            ];
        });

        return ['cards' => $cards];
    }
}
