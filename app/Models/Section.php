<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'name',
        'grade_id',
        'academic_year_id',
        'max_students',
        'teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'max_students' => 'integer',
        ];
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** مربّي الفصل. */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * عدد طلاب الفصل. يستخدم القيمة المحمّلة عبر withCount إن وُجدت لتفادي الاستعلامات الزائدة.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->attributes['students_count'] ?? $this->students()->count();
    }
}
