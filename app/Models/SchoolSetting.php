<?php

namespace App\Models;

use App\Support\SchoolConfig;
use Illuminate\Database\Eloquent\Model;

/**
 * الإعدادات الأساسية للموقع: الهوية، التواصل، وتحسين محركات البحث (SEO).
 * محتوى الصفحة الرئيسية مفصول في موديل HomepageContent.
 */
class SchoolSetting extends Model
{
    protected $fillable = [
        'name',
        'tagline',
        'phone',
        'whatsapp',
        'email',
        'address',
        'map_embed_url',
        'admission_open',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected function casts(): array
    {
        return [
            'admission_open' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'name' => config('school.name'),
            'tagline' => config('school.tagline'),
            'phone' => config('school.phone'),
            'whatsapp' => config('school.whatsapp'),
            'email' => config('school.email'),
            'address' => config('school.address'),
            'map_embed_url' => config('school.map_embed_url'),
            'admission_open' => config('school.admission_open'),
            'meta_title' => config('school.seo.meta_title'),
            'meta_description' => config('school.seo.meta_description'),
            'meta_keywords' => config('school.seo.meta_keywords'),
        ];
    }

    public function toConfigArray(): array
    {
        return [
            'name' => $this->name,
            'tagline' => $this->tagline,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'address' => $this->address,
            'map_embed_url' => $this->map_embed_url,
            'admission_open' => $this->admission_open,
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ],
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => SchoolConfig::clearCache());
        static::deleted(fn () => SchoolConfig::clearCache());
    }
}
