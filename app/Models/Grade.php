<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'level',
        'academic_year_id',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /** طلاب الصف عبر الفصول (الطالب مرتبط بالصف من خلال الفصل). */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, Section::class);
    }

    /**
     * طلبات القبول المقدمة لهذا الصف.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'grade_applying_for');
    }
}
