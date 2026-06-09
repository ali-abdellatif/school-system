<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeRecord extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_year_id',
        'section_id',
        'exam_type',
        'score',
        'max_score',
        'term',
        'notes',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /** النسبة المئوية للدرجة. */
    protected function percentage(): Attribute
    {
        return Attribute::get(function (): float {
            $max = (float) $this->max_score;

            return $max > 0 ? round(((float) $this->score / $max) * 100, 1) : 0.0;
        });
    }

    /** التقدير الحرفي بناءً على النسبة. */
    protected function letterGrade(): Attribute
    {
        return Attribute::get(function (): string {
            $p = $this->percentage;

            return match (true) {
                $p >= 90 => 'ممتاز',
                $p >= 80 => 'جيد جداً',
                $p >= 70 => 'جيد',
                $p >= 60 => 'مقبول',
                default => 'ضعيف',
            };
        });
    }

    /**
     * متوسط درجات الطالب في مادة لفصل وسنة (كنسبة مئوية).
     */
    public static function getStudentAverage(int $studentId, int $subjectId, string $term, int $academicYearId): float
    {
        $records = static::query()
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term', $term)
            ->where('academic_year_id', $academicYearId)
            ->get();

        if ($records->isEmpty()) {
            return 0.0;
        }

        return round($records->avg(fn (GradeRecord $r): float => $r->percentage), 1);
    }
}
