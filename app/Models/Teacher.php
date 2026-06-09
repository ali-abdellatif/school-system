<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'employee_number',
        'specialization',
        'qualification',
        'phone',
        'address',
        'hire_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** المواد التي يُدرّسها المعلم (مؤهّل لها). */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')
            ->withPivot('academic_year_id')
            ->withTimestamps();
    }

    /** تعيينات المعلم على الفصول (مادة + فصل + سنة). */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'teacher_section')
            ->withPivot(['subject_id', 'academic_year_id'])
            ->withTimestamps();
    }

    /** سجلات الحضور التي سجّلها هذا المعلم. */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'teacher_id');
    }

    /** الاسم الكامل للمعلم عبر حساب المستخدم. */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->user?->name);
    }

    /** عدد المواد (يستخدم القيمة المحمّلة عبر withCount إن وُجدت). */
    protected function activeSubjectsCount(): Attribute
    {
        return Attribute::get(fn (): int => $this->attributes['subjects_count'] ?? $this->subjects()->count());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** معرّفات الفصول المُعيّن عليها المعلم. */
    public function assignedSectionIds(): array
    {
        return \Illuminate\Support\Facades\DB::table('teacher_section')
            ->where('teacher_id', $this->id)
            ->distinct()
            ->pluck('section_id')
            ->all();
    }

    /** معرّفات المواد التي يُدرّسها المعلم (من التعيينات). */
    public function assignedSubjectIds(): array
    {
        return \Illuminate\Support\Facades\DB::table('teacher_section')
            ->where('teacher_id', $this->id)
            ->distinct()
            ->pluck('subject_id')
            ->all();
    }

    protected static function booted(): void
    {
        // توليد رقم الموظف تلقائيًا: EMP-001, EMP-002 ...
        static::created(function (Teacher $teacher): void {
            if (blank($teacher->employee_number)) {
                $teacher->employee_number = 'EMP-' . str_pad((string) $teacher->id, 3, '0', STR_PAD_LEFT);
                $teacher->saveQuietly();
            }
        });
    }
}
