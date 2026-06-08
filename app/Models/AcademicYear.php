<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** السنة الدراسية الحالية. */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    protected static function booted(): void
    {
        // عند تعيين سنة كحالية، تُلغى الحالية عن البقية (تحديث مباشر بلا أحداث = بلا تكرار).
        static::saved(function (AcademicYear $year): void {
            if ($year->is_current) {
                static::query()
                    ->whereKeyNot($year->getKey())
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }
        });
    }
}
