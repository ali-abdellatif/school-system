<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\GradeRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoPortalsSeeder extends Seeder
{
    /**
     * حسابات تجريبية للدخول السريع إلى بوابتي المعلم وولي الأمر.
     * قابل لإعادة التشغيل (idempotent).
     */
    public function run(): void
    {
        $year = AcademicYear::current()->first()
            ?? AcademicYear::firstOrCreate(['name' => '2024-2025'], ['is_current' => true]);

        $grade = Grade::where('academic_year_id', $year->id)->orderBy('level')->first()
            ?? Grade::firstOrCreate(['name' => 'الصف الأول الابتدائي', 'academic_year_id' => $year->id], ['level' => 1]);

        $section = Section::where('grade_id', $grade->id)->orderBy('name')->first()
            ?? Section::firstOrCreate(['name' => 'أ', 'grade_id' => $grade->id, 'academic_year_id' => $year->id], ['max_students' => 30]);

        $subject = Subject::firstOrCreate(
            ['code' => 'MATH-1'],
            ['name' => 'الرياضيات', 'grade_id' => $grade->id, 'weekly_hours' => 4],
        );

        // ── حساب المعلم ──
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@demo.test'],
            ['name' => 'أ. أحمد المعلّم', 'password' => Hash::make('password')],
        );
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            ['specialization' => 'الرياضيات', 'qualification' => 'بكالوريوس', 'status' => 'active', 'hire_date' => now()->subYears(3)],
        );
        $teacher->subjects()->syncWithoutDetaching([$subject->id]);
        $subject->update(['teacher_id' => $teacher->id]);
        DB::table('teacher_section')->updateOrInsert(
            ['teacher_id' => $teacher->id, 'section_id' => $section->id, 'subject_id' => $subject->id, 'academic_year_id' => $year->id],
            ['created_at' => now(), 'updated_at' => now()],
        );

        // ── حساب ولي الأمر + ابنه ──
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@demo.test'],
            ['name' => 'ولي الأمر محمد', 'password' => Hash::make('password')],
        );
        $child = Student::firstOrCreate(
            ['national_id' => 'DEMO-0001'],
            [
                'first_name' => 'سالم', 'last_name' => 'محمد', 'birth_date' => '2016-05-10',
                'gender' => 'male', 'status' => 'active',
                'section_id' => $section->id, 'academic_year_id' => $year->id, 'parent_user_id' => $parentUser->id,
            ],
        );

        // طالب ثانٍ في نفس الفصل (بدون ولي أمر) لإثراء قوائم المعلم
        Student::firstOrCreate(
            ['national_id' => 'DEMO-0002'],
            [
                'first_name' => 'ريم', 'last_name' => 'خالد', 'birth_date' => '2016-09-01',
                'gender' => 'female', 'status' => 'active',
                'section_id' => $section->id, 'academic_year_id' => $year->id,
            ],
        );

        // ── بيانات تجريبية: حضور + درجات (تُطلق إشعارات لولي الأمر) ──
        foreach ([0 => 'present', 1 => 'present', 2 => 'absent', 3 => 'late'] as $daysAgo => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $child->id, 'subject_id' => $subject->id, 'date' => now()->subDays($daysAgo)->toDateString()],
                ['section_id' => $section->id, 'teacher_id' => $teacher->id, 'academic_year_id' => $year->id, 'status' => $status, 'recorded_by' => $teacherUser->id],
            );
        }

        foreach (['monthly1' => 18, 'midterm' => 27] as $examType => $score) {
            GradeRecord::updateOrCreate(
                ['student_id' => $child->id, 'subject_id' => $subject->id, 'exam_type' => $examType, 'term' => 'first', 'academic_year_id' => $year->id],
                ['section_id' => $section->id, 'score' => $score, 'max_score' => $examType === 'midterm' ? 30 : 20, 'entered_by' => $teacherUser->id],
            );
        }

        // إشعارات تجريبية لولي الأمر (الـobservers معطّلة أثناء التهيئة، فنرسلها صراحةً مرة واحدة)
        if ($parentUser->notifications()->count() === 0) {
            if ($absent = Attendance::where('student_id', $child->id)->where('status', 'absent')->first()) {
                $parentUser->notify(new \App\Notifications\AbsenceNotification($absent));
            }
            if ($grade = GradeRecord::where('student_id', $child->id)->first()) {
                $parentUser->notify(new \App\Notifications\NewGradeNotification($grade));
            }
        }

        $this->command?->info('حسابات تجريبية جاهزة:');
        $this->command?->line('  المعلم   → teacher@demo.test / password  (/teacher)');
        $this->command?->line('  ولي الأمر → parent@demo.test  / password  (/parent)');
    }
}
