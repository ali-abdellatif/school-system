<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->boolean('admission_open')->default(true);
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
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
