<?php

namespace App\Support;

use App\Models\HomepageContent;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;

class SchoolConfig
{
    /**
     * مفاتيح القوائم التي يجب استبدالها بالكامل (لا دمجها بالفهرس).
     */
    private const LIST_KEYS = ['stats', 'accreditations', 'testimonials', 'gallery', 'faq'];

    public static function all(): array
    {
        return cache()->remember('school_config', 3600, function (): array {
            $config = config('school');

            try {
                // الإعدادات الأساسية + SEO
                if (Schema::hasTable('school_settings') && $settings = SchoolSetting::query()->first()) {
                    $config = static::merge($config, $settings->toConfigArray());
                }

                // محتوى الصفحة الرئيسية
                if (Schema::hasTable('homepage_contents') && $home = HomepageContent::query()->first()) {
                    $config = static::merge($config, $home->toConfigArray());
                }
            } catch (\Throwable) {
                // قاعدة البيانات غير جاهزة بعد (تثبيت أو migrate)
            }

            return $config;
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return data_get(static::all(), $key, $default);
    }

    public static function clearCache(): void
    {
        cache()->forget('school_config');
    }

    /**
     * دمج التجاوزات فوق الإعدادات: دمج تعاودي للقيم المتداخلة،
     * مع استبدال كامل لمفاتيح القوائم، وتجاهل قيم null.
     */
    private static function merge(array $base, array $overrides): array
    {
        $lists = [];
        foreach (self::LIST_KEYS as $key) {
            if (array_key_exists($key, $overrides) && ! empty($overrides[$key])) {
                $lists[$key] = $overrides[$key];
                unset($overrides[$key]);
            }
        }

        // القيم الفارغة (null) في قاعدة البيانات يجب ألا تتجاوز الافتراضيات.
        $overrides = static::stripNulls($overrides);

        return array_replace(array_replace_recursive($base, $overrides), $lists);
    }

    /**
     * إزالة قيم null المتداخلة (مع الإبقاء على false و 0 و "").
     */
    private static function stripNulls(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = static::stripNulls($value);
            } elseif ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
