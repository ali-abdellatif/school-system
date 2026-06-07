<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // بيانات الطالب
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('nationality')->nullable();
            $table->string('previous_school')->nullable();
            $table->foreignId('grade_applying_for')
                ->nullable()
                ->constrained('grades')
                ->nullOnDelete();

            // بيانات ولي الأمر
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->enum('parent_relation', ['father', 'mother', 'guardian']);
            $table->text('address')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // حالة الطلب
            $table->enum('status', ['pending', 'reviewing', 'approved', 'rejected'])
                ->default('pending');
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
