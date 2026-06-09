<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\Subject;
use Filament\Pages\Page;
use UnitEnum;

class StudentReport extends Page
{
    protected string $view = 'filament.pages.student-report';

    protected static string|UnitEnum|null $navigationGroup = 'الحضور والدرجات';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'students/{student}/report';

    public Student $student;

    public string $term = 'first';

    /** @var array<int, array<string, mixed>> */
    public array $attendanceSummary = [];

    /** @var array<int, array<string, mixed>> */
    public array $gradesBySubject = [];

    public ?float $gpa = null;

    public function mount(Student $student): void
    {
        $this->student = $student->load('section.grade', 'academicYear');
        $this->buildReport();
    }

    public function getTitle(): string
    {
        return 'التقرير الأكاديمي — ' . $this->student->full_name;
    }

    public function updatedTerm(): void
    {
        $this->buildReport();
    }

    protected function buildReport(): void
    {
        $studentId = $this->student->id;
        $subjects = Subject::query()->pluck('name', 'id');

        // ملخّص الحضور لكل مادة
        $attendance = Attendance::query()
            ->where('student_id', $studentId)
            ->selectRaw('subject_id, COUNT(*) as total, SUM(status IN ("present","late")) as present_count, SUM(status = "absent") as absent_count')
            ->groupBy('subject_id')
            ->get();

        $this->attendanceSummary = $attendance->map(function ($row) use ($subjects): array {
            $total = (int) $row->total;
            $present = (int) $row->present_count;
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0.0;

            return [
                'subject' => $subjects[$row->subject_id] ?? 'مادة',
                'total' => $total,
                'present' => $present,
                'absent' => (int) $row->absent_count,
                'percentage' => $percentage,
            ];
        })->all();

        // الدرجات لكل مادة في الفصل المحدد
        $grades = GradeRecord::query()
            ->where('student_id', $studentId)
            ->where('term', $this->term)
            ->get()
            ->groupBy('subject_id');

        $subjectAverages = [];
        $this->gradesBySubject = $grades->map(function ($records, $subjectId) use ($subjects, &$subjectAverages): array {
            $avg = round($records->avg(fn (GradeRecord $r): float => $r->percentage), 1);
            $subjectAverages[] = $avg;

            return [
                'subject' => $subjects[$subjectId] ?? 'مادة',
                'records' => $records->map(fn (GradeRecord $r): array => [
                    'exam_type' => static::examTypeLabel($r->exam_type),
                    'score' => (float) $r->score,
                    'max_score' => (float) $r->max_score,
                    'percentage' => $r->percentage,
                ])->all(),
                'average' => $avg,
                'letter' => static::letterForPercentage($avg),
            ];
        })->values()->all();

        $this->gpa = empty($subjectAverages) ? null : round(array_sum($subjectAverages) / count($subjectAverages), 1);
    }

    public static function examTypeLabel(string $type): string
    {
        return [
            'monthly1' => 'شهري 1',
            'monthly2' => 'شهري 2',
            'midterm' => 'نصف الفصل',
            'final' => 'النهائي',
            'assignment' => 'واجب',
            'oral' => 'شفهي',
        ][$type] ?? $type;
    }

    public static function letterForPercentage(float $p): string
    {
        return match (true) {
            $p >= 90 => 'ممتاز',
            $p >= 80 => 'جيد جداً',
            $p >= 70 => 'جيد',
            $p >= 60 => 'مقبول',
            default => 'ضعيف',
        };
    }

    /** لون حسب نسبة الحضور. */
    public static function attendanceColor(float $p): string
    {
        return match (true) {
            $p > 90 => 'text-green-600',
            $p > 75 => 'text-amber-600',
            default => 'text-red-600',
        };
    }
}
