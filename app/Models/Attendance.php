<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'section_id',
        'teacher_id',
        'academic_year_id',
        'date',
        'status',
        'note',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => 'string',
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /** المستخدم الذي سجّل الحضور. */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('status', 'absent');
    }

    public function scopePresent(Builder $query): Builder
    {
        return $query->where('status', 'present');
    }

    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeBySection(Builder $query, int $sectionId): Builder
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * نسبة الحضور لطالب في مادة (الحاضر + المتأخر يُحتسبان حضورًا).
     */
    public static function getAttendancePercentage(int $studentId, int $subjectId): float
    {
        $total = static::query()
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->count();

        if ($total === 0) {
            return 0.0;
        }

        $present = static::query()
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($present / $total) * 100, 1);
    }
}
