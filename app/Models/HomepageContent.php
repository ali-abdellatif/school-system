<?php

namespace App\Models;

use App\Support\SchoolConfig;
use Illuminate\Database\Eloquent\Model;

/**
 * محتوى الصفحة الرئيسية: الصور، الإحصائيات، الاعتمادات، الآراء، المعرض، والأسئلة الشائعة.
 * مفصول عن الإعدادات الأساسية (SchoolSetting).
 */
class HomepageContent extends Model
{
    protected $fillable = [
        'hero_image',
        'classroom_image',
        'lab_image',
        'library_image',
        'stats',
        'accreditations',
        'testimonials',
        'gallery',
        'faq',
    ];

    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'accreditations' => 'array',
            'testimonials' => 'array',
            'gallery' => 'array',
            'faq' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'hero_image' => config('school.images.hero'),
            'classroom_image' => config('school.images.classroom'),
            'lab_image' => config('school.images.lab'),
            'library_image' => config('school.images.library'),
            'stats' => config('school.stats'),
            'accreditations' => config('school.accreditations'),
            'testimonials' => config('school.testimonials'),
            'gallery' => config('school.gallery'),
            'faq' => config('school.faq'),
        ];
    }

    public function toConfigArray(): array
    {
        return [
            'images' => [
                'hero' => $this->hero_image,
                'classroom' => $this->classroom_image,
                'lab' => $this->lab_image,
                'library' => $this->library_image,
            ],
            'stats' => $this->stats ?? [],
            'accreditations' => $this->accreditations ?? [],
            'testimonials' => $this->testimonials ?? [],
            'gallery' => $this->gallery ?? [],
            'faq' => $this->faq ?? [],
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => SchoolConfig::clearCache());
        static::deleted(fn () => SchoolConfig::clearCache());
    }
}
