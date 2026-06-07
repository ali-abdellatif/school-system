<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * فصل الإعدادات الأساسية عن محتوى الصفحة الرئيسية، وإضافة حقول SEO.
     */
    public function up(): void
    {
        // 1) حقول SEO على جدول الإعدادات الأساسية
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('admission_open');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
        });

        // 2) جدول محتوى الصفحة الرئيسية
        Schema::create('homepage_contents', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image')->nullable();
            $table->string('classroom_image')->nullable();
            $table->string('lab_image')->nullable();
            $table->string('library_image')->nullable();
            $table->json('stats')->nullable();
            $table->json('accreditations')->nullable();
            $table->json('testimonials')->nullable();
            $table->json('gallery')->nullable();
            $table->json('faq')->nullable();
            $table->timestamps();
        });

        // 3) نقل بيانات الصفحة الرئيسية من الجدول القديم (إن وُجدت)
        $existing = DB::table('school_settings')->first();
        if ($existing && Schema::hasColumn('school_settings', 'stats')) {
            DB::table('homepage_contents')->insert([
                'hero_image' => $existing->hero_image ?? null,
                'classroom_image' => $existing->classroom_image ?? null,
                'lab_image' => $existing->lab_image ?? null,
                'library_image' => $existing->library_image ?? null,
                'stats' => $existing->stats ?? null,
                'accreditations' => $existing->accreditations ?? null,
                'testimonials' => $existing->testimonials ?? null,
                'gallery' => $existing->gallery ?? null,
                'faq' => $existing->faq ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4) إزالة أعمدة محتوى الصفحة الرئيسية من جدول الإعدادات الأساسية
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_image', 'classroom_image', 'lab_image', 'library_image',
                'stats', 'accreditations', 'testimonials', 'gallery', 'faq',
            ]);
        });
    }

    public function down(): void
    {
        // إعادة الأعمدة إلى جدول الإعدادات الأساسية
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('hero_image')->nullable();
            $table->string('classroom_image')->nullable();
            $table->string('lab_image')->nullable();
            $table->string('library_image')->nullable();
            $table->json('stats')->nullable();
            $table->json('accreditations')->nullable();
            $table->json('testimonials')->nullable();
            $table->json('gallery')->nullable();
            $table->json('faq')->nullable();
        });

        // استرجاع البيانات إن أمكن
        $home = DB::table('homepage_contents')->first();
        $settings = DB::table('school_settings')->first();
        if ($home && $settings) {
            DB::table('school_settings')->where('id', $settings->id)->update([
                'hero_image' => $home->hero_image,
                'classroom_image' => $home->classroom_image,
                'lab_image' => $home->lab_image,
                'library_image' => $home->library_image,
                'stats' => $home->stats,
                'accreditations' => $home->accreditations,
                'testimonials' => $home->testimonials,
                'gallery' => $home->gallery,
                'faq' => $home->faq,
            ]);
        }

        Schema::dropIfExists('homepage_contents');

        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords']);
        });
    }
};
