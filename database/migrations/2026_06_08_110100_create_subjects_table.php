<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المادة
            $table->string('code')->unique(); // رمز المادة
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete(); // المعلم المسؤول
            $table->unsignedInteger('weekly_hours')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
