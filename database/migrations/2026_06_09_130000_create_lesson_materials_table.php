<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الدرس
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete(); // null = كل الفصول
            $table->enum('type', ['pdf', 'video', 'document', 'link', 'image'])->default('pdf');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_materials');
    }
};
