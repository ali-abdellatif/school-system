<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('student_id')->constrained('teachers')->nullOnDelete(); // المعلم المسجِّل
            $table->foreignId('subject_id')->nullable()->after('teacher_id')->constrained('subjects')->nullOnDelete();
        });

        Schema::table('grade_records', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('student_id')->constrained('subjects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
            $table->dropConstrainedForeignId('subject_id');
        });

        Schema::table('grade_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
        });
    }
};
