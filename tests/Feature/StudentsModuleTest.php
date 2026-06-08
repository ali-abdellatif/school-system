<?php

use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Grades\Pages\ListGrades;
use App\Filament\Resources\Sections\Pages\ListSections;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Widgets\SchoolOverviewWidget;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create());

    $this->year = AcademicYear::create(['name' => '2024-2025', 'is_current' => true]);
    $this->grade = Grade::create(['name' => 'الصف الأول الابتدائي', 'level' => 1, 'academic_year_id' => $this->year->id]);
    $this->section = Section::create(['name' => 'أ', 'grade_id' => $this->grade->id, 'academic_year_id' => $this->year->id, 'max_students' => 30]);
});

it('renders all module list pages and the dashboard widget', function () {
    Livewire::test(ListAcademicYears::class)->assertOk();
    Livewire::test(ListGrades::class)->assertOk();
    Livewire::test(ListSections::class)->assertOk();
    Livewire::test(ListStudents::class)->assertOk();
    Livewire::test(SchoolOverviewWidget::class)->assertOk();
});

it('setting a year as current unsets the others', function () {
    $other = AcademicYear::create(['name' => '2025-2026', 'is_current' => false]);

    $other->update(['is_current' => true]);

    expect($other->fresh()->is_current)->toBeTrue()
        ->and($this->year->fresh()->is_current)->toBeFalse();
});

it('set-current table action works', function () {
    $other = AcademicYear::create(['name' => '2025-2026', 'is_current' => false]);

    Livewire::test(ListAcademicYears::class)
        ->callTableAction('setCurrent', $other)
        ->assertHasNoTableActionErrors();

    expect($other->fresh()->is_current)->toBeTrue()
        ->and($this->year->fresh()->is_current)->toBeFalse();
});

it('converts an approved application into a student', function () {
    $app = Application::create([
        'first_name' => 'كريم', 'last_name' => 'سمير', 'birth_date' => '2016-05-01',
        'gender' => 'male', 'parent_name' => 'سمير', 'parent_phone' => '0500001111',
        'parent_relation' => 'father', 'address' => 'القاهرة', 'status' => 'approved',
    ]);

    Livewire::test(ListApplications::class)
        ->callTableAction('convertToStudent', $app, data: ['section_id' => $this->section->id])
        ->assertHasNoTableActionErrors();

    $student = Student::where('application_id', $app->id)->first();

    expect($student)->not->toBeNull()
        ->and($student->full_name)->toBe('كريم سمير')
        ->and($student->section_id)->toBe($this->section->id)
        ->and($student->academic_year_id)->toBe($this->year->id)
        ->and($student->status)->toBe('active')
        ->and($student->phone)->toBe('0500001111');
});

it('hides convert action once a student already exists for the application', function () {
    $app = Application::create([
        'first_name' => 'سما', 'last_name' => 'علي', 'birth_date' => '2017-01-01',
        'gender' => 'female', 'parent_name' => 'علي', 'parent_phone' => '0500002222',
        'parent_relation' => 'father', 'status' => 'approved',
    ]);
    Student::create([
        'first_name' => 'سما', 'last_name' => 'علي', 'birth_date' => '2017-01-01',
        'gender' => 'female', 'status' => 'active', 'application_id' => $app->id,
    ]);

    Livewire::test(ListApplications::class)
        ->assertTableActionHidden('convertToStudent', $app);
});

it('transfer action moves a student to another section', function () {
    $sectionB = Section::create(['name' => 'ب', 'grade_id' => $this->grade->id, 'academic_year_id' => $this->year->id, 'max_students' => 30]);
    $student = Student::create([
        'first_name' => 'يوسف', 'last_name' => 'محمد', 'birth_date' => '2016-03-03',
        'gender' => 'male', 'status' => 'active', 'section_id' => $this->section->id,
    ]);

    Livewire::test(ListStudents::class)
        ->callTableAction('transfer', $student, data: ['section_id' => $sectionB->id, 'note' => 'نقل بناءً على طلب ولي الأمر'])
        ->assertHasNoTableActionErrors();

    expect($student->fresh()->section_id)->toBe($sectionB->id);
});

it('graduate bulk action sets status to graduated', function () {
    $s1 = Student::create(['first_name' => 'أ', 'last_name' => 'ب', 'birth_date' => '2010-01-01', 'gender' => 'male', 'status' => 'active', 'section_id' => $this->section->id]);
    $s2 = Student::create(['first_name' => 'ج', 'last_name' => 'د', 'birth_date' => '2010-01-01', 'gender' => 'female', 'status' => 'active', 'section_id' => $this->section->id]);

    Livewire::test(ListStudents::class)
        ->callTableBulkAction('graduate', [$s1->getKey(), $s2->getKey()])
        ->assertHasNoTableBulkActionErrors();

    expect($s1->fresh()->status)->toBe('graduated')
        ->and($s2->fresh()->status)->toBe('graduated');
});

it('exposes student accessors (full name, age, grade via section)', function () {
    $student = Student::create([
        'first_name' => 'لينا', 'last_name' => 'خالد', 'birth_date' => now()->subYears(8)->toDateString(),
        'gender' => 'female', 'status' => 'active', 'section_id' => $this->section->id,
    ]);

    expect($student->full_name)->toBe('لينا خالد')
        ->and($student->age)->toBe(8)
        ->and($student->grade->id)->toBe($this->grade->id);
});
