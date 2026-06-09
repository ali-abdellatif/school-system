<?php

use App\Filament\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create());

    $this->year = AcademicYear::create(['name' => '2024-2025', 'is_current' => true]);
    $this->grade = Grade::create(['name' => 'الصف الأول', 'level' => 1, 'academic_year_id' => $this->year->id]);
    $this->section = Section::create(['name' => 'أ', 'grade_id' => $this->grade->id, 'academic_year_id' => $this->year->id, 'max_students' => 30]);
    $this->subject = Subject::create(['name' => 'الرياضيات', 'code' => 'MATH-1', 'grade_id' => $this->grade->id, 'weekly_hours' => 4]);
});

it('renders teacher and subject list pages', function () {
    Livewire::test(ListTeachers::class)->assertOk();
    Livewire::test(ListSubjects::class)->assertOk();
});

it('auto-generates the employee number on create', function () {
    $user = User::factory()->create();
    $teacher = Teacher::create(['user_id' => $user->id, 'specialization' => 'رياضيات', 'status' => 'active']);

    expect($teacher->fresh()->employee_number)->toBe('EMP-' . str_pad((string) $teacher->id, 3, '0', STR_PAD_LEFT));
});

it('creates a teacher with a brand-new user account', function () {
    Livewire::test(CreateTeacher::class)
        ->fillForm([
            'account_mode' => 'new',
            'name' => 'أستاذ محمد',
            'email' => 'teacher.new@example.com',
            'password' => 'password123',
            'specialization' => 'لغة عربية',
            'qualification' => 'ماجستير',
            'status' => 'active',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'teacher.new@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->teacher)->not->toBeNull()
        ->and($user->teacher->specialization)->toBe('لغة عربية')
        ->and($user->teacher->employee_number)->toStartWith('EMP-');
});

it('creates a teacher linked to an existing user', function () {
    $user = User::factory()->create(['name' => 'أستاذة سارة']);

    Livewire::test(CreateTeacher::class)
        ->fillForm([
            'account_mode' => 'existing',
            'user_id' => $user->id,
            'specialization' => 'علوم',
            'status' => 'active',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Teacher::where('user_id', $user->id)->exists())->toBeTrue();
});

it('syncs taught subjects via the edit form', function () {
    $teacher = Teacher::create(['user_id' => User::factory()->create()->id, 'specialization' => 'رياضيات', 'status' => 'active']);

    Livewire::test(EditTeacher::class, ['record' => $teacher->getKey()])
        ->fillForm(['subjects' => [$this->subject->id]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($teacher->fresh()->subjects)->toHaveCount(1)
        ->and($teacher->fresh()->subjects->first()->id)->toBe($this->subject->id);
});

it('assigns a subject+section to a teacher and then unassigns it', function () {
    $teacher = Teacher::create(['user_id' => User::factory()->create()->id, 'specialization' => 'رياضيات', 'status' => 'active']);
    $teacher->subjects()->attach($this->subject->id);

    Livewire::test(ListTeachers::class)
        ->callTableAction('assignSubject', $teacher, data: [
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'academic_year_id' => $this->year->id,
        ])
        ->assertHasNoTableActionErrors();

    $row = DB::table('teacher_section')->where('teacher_id', $teacher->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->subject_id)->toBe($this->subject->id)
        ->and($row->section_id)->toBe($this->section->id);

    Livewire::test(ListTeachers::class)
        ->callTableAction('unassign', $teacher, data: ['assignment_id' => $row->id])
        ->assertHasNoTableActionErrors();

    expect(DB::table('teacher_section')->where('id', $row->id)->exists())->toBeFalse();
});

it('exposes the user hasOne teacher relationship', function () {
    $user = User::factory()->create();
    Teacher::create(['user_id' => $user->id, 'specialization' => 'فيزياء', 'status' => 'active']);

    expect($user->fresh()->teacher)->not->toBeNull()
        ->and($user->fresh()->teacher->full_name)->toBe($user->name);
});
