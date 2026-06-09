<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_number')->nullable()->unique();
            $table->string('specialization')->nullable(); // التخصص
            $table->string('qualification')->nullable();   // المؤهل الدراسي
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
