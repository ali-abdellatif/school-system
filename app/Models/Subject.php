<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'grade_id',
        'teacher_id',
        'weekly_hours',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'weekly_hours' => 'integer',
        ];
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /** المعلم المسؤول الأساسي عن المادة. */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** كل المعلمين المؤهلين لتدريس المادة. */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject')
            ->withPivot('academic_year_id')
            ->withTimestamps();
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'teacher_section')
            ->withPivot(['teacher_id', 'academic_year_id'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'subject_id');
    }

    public function gradeRecords(): HasMany
    {
        return $this->hasMany(GradeRecord::class, 'subject_id');
    }
}
