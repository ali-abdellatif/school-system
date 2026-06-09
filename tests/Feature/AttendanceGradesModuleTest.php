<?php

use App\Filament\Pages\BulkAttendance;
use App\Filament\Pages\StudentReport;
use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Resources\GradeRecords\Pages\ListGradeRecords;
use App\Filament\Widgets\AttendanceTodayWidget;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\GradeRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create());

    $this->year = AcademicYear::create(['name' => '2024-2025', 'is_current' => true]);
    $this->grade = Grade::create(['name' => 'الصف الأول', 'level' => 1, 'academic_year_id' => $this->year->id]);
    $this->section = Section::create(['name' => 'أ', 'grade_id' => $this->grade->id, 'academic_year_id' => $this->year->id, 'max_students' => 30]);
    $this->subject = Subject::create(['name' => 'الرياضيات', 'code' => 'MATH-1', 'grade_id' => $this->grade->id, 'weekly_hours' => 4]);
    $this->student = Student::create([
        'first_name' => 'محمد', 'last_name' => 'علي', 'birth_date' => '2016-01-01',
        'gender' => 'male', 'status' => 'active', 'section_id' => $this->section->id, 'academic_year_id' => $this->year->id,
    ]);
});

it('renders attendance & grade resources, bulk page, and widget', function () {
    Livewire::test(ListAttendances::class)->assertOk();
    Livewire::test(ListGradeRecords::class)->assertOk();
    Livewire::test(BulkAttendance::class)->assertOk();
    Livewire::test(AttendanceTodayWidget::class)->assertOk();
});

it('computes attendance percentage (present + late count as attended)', function () {
    foreach (['present', 'present', 'late', 'absent'] as $i => $status) {
        Attendance::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'date' => now()->subDays($i)->toDateString(),
            'status' => $status,
        ]);
    }

    expect(Attendance::getAttendancePercentage($this->student->id, $this->subject->id))->toBe(75.0);
});

it('computes grade percentage, letter grade, and student average', function () {
    $g1 = GradeRecord::create([
        'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
        'academic_year_id' => $this->year->id, 'exam_type' => 'monthly1', 'term' => 'first',
        'score' => 85, 'max_score' => 100,
    ]);
    GradeRecord::create([
        'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
        'academic_year_id' => $this->year->id, 'exam_type' => 'midterm', 'term' => 'first',
        'score' => 45, 'max_score' => 50,
    ]);

    expect($g1->percentage)->toBe(85.0)
        ->and($g1->letter_grade)->toBe('جيد جداً')
        // (85 + 90) / 2 = 87.5
        ->and(GradeRecord::getStudentAverage($this->student->id, $this->subject->id, 'first', $this->year->id))->toBe(87.5);
});

it('enforces unique attendance per student/subject/date', function () {
    $payload = [
        'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
        'date' => today()->toDateString(), 'status' => 'present',
    ];
    Attendance::create($payload);

    expect(fn () => Attendance::create($payload))->toThrow(\Illuminate\Database\QueryException::class);
});

it('saves bulk attendance via the page', function () {
    Livewire::test(BulkAttendance::class)
        ->fillForm([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
            'date' => today()->toDateString(),
            'rows' => [
                ['student_id' => $this->student->id, 'student_name' => $this->student->full_name, 'status' => 'absent', 'note' => 'مريض'],
            ],
        ])
        ->call('save');

    $record = Attendance::where('student_id', $this->student->id)->where('subject_id', $this->subject->id)->first();
    expect($record)->not->toBeNull()
        ->and($record->status)->toBe('absent')
        ->and($record->note)->toBe('مريض')
        ->and($record->section_id)->toBe($this->section->id)
        ->and($record->recorded_by)->not->toBeNull();
});

it('builds a student academic report with gpa', function () {
    Attendance::create(['student_id' => $this->student->id, 'subject_id' => $this->subject->id, 'date' => today()->toDateString(), 'status' => 'present']);
    GradeRecord::create([
        'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
        'academic_year_id' => $this->year->id, 'exam_type' => 'final', 'term' => 'first',
        'score' => 90, 'max_score' => 100,
    ]);

    Livewire::test(StudentReport::class, ['student' => $this->student])
        ->assertOk()
        ->assertSet('gpa', 90.0);
});
