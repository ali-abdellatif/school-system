<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // مستخدم تجريبي للدخول
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => Hash::make('password')],
        );

        // السنة الدراسية الحالية
        AcademicYear::query()->update(['is_current' => false]);
        $year = AcademicYear::firstOrCreate(
            ['name' => '2024-2025'],
            ['start_date' => '2024-09-01', 'end_date' => '2025-06-30', 'is_current' => true],
        );
        $year->update(['is_current' => true]);

        // 6 صفوف ابتدائية + فصلان (أ، ب) لكل صف
        $gradeNames = [
            1 => 'الصف الأول الابتدائي',
            2 => 'الصف الثاني الابتدائي',
            3 => 'الصف الثالث الابتدائي',
            4 => 'الصف الرابع الابتدائي',
            5 => 'الصف الخامس الابتدائي',
            6 => 'الصف السادس الابتدائي',
        ];

        foreach ($gradeNames as $level => $name) {
            $grade = Grade::firstOrCreate(
                ['name' => $name, 'academic_year_id' => $year->id],
                ['level' => $level],
            );

            foreach (['أ', 'ب'] as $sectionName) {
                Section::firstOrCreate(
                    ['name' => $sectionName, 'grade_id' => $grade->id, 'academic_year_id' => $year->id],
                    ['max_students' => 30],
                );
            }
        }
    }
}
