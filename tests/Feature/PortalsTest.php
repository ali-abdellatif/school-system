<?php

use App\Filament\Parent\Pages\Notifications as ParentNotifications;
use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    config(['app.env' => 'local']); // Filament يسمح بالوصول الأساسي في local
});

function makeTeacherUser(): User
{
    $user = User::factory()->create();
    Teacher::create(['user_id' => $user->id, 'specialization' => 'رياضيات', 'status' => 'active']);

    return $user;
}

function makeParentWithChild(): array
{
    $parent = User::factory()->create();
    $child = Student::create([
        'first_name' => 'ابن', 'last_name' => 'تجريبي', 'birth_date' => '2015-01-01',
        'gender' => 'male', 'status' => 'active', 'parent_user_id' => $parent->id,
    ]);

    return [$parent, $child];
}

it('blocks a non-teacher from the teacher panel', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/teacher')->assertForbidden();
});

it('allows a teacher into the teacher panel', function () {
    $this->actingAs(makeTeacherUser());
    $this->get('/teacher')->assertSuccessful();
});

it('blocks a non-parent from the parent panel', function () {
    $this->actingAs(makeTeacherUser()); // معلم بلا أبناء
    $this->get('/parent')->assertForbidden();
});

it('allows a parent into the parent panel', function () {
    [$parent] = makeParentWithChild();
    $this->actingAs($parent);
    $this->get('/parent')->assertSuccessful();
});

it('notifies the parent when their child is marked absent', function () {
    [$parent, $child] = makeParentWithChild();
    $subject = Subject::create(['name' => 'علوم', 'code' => 'SCI', 'grade_id' => \App\Models\Grade::create(['name' => 'ص', 'level' => 1])->id]);

    Attendance::create([
        'student_id' => $child->id, 'subject_id' => $subject->id,
        'date' => today()->toDateString(), 'status' => 'absent',
    ]);

    $parent->refresh();
    expect($parent->notifications()->count())->toBe(1)
        ->and($parent->notifications()->first()->data['type'])->toBe('absence');
});

it('does not notify on a present attendance', function () {
    [$parent, $child] = makeParentWithChild();
    $subject = Subject::create(['name' => 'علوم', 'code' => 'SCI', 'grade_id' => \App\Models\Grade::create(['name' => 'ص', 'level' => 1])->id]);

    Attendance::create(['student_id' => $child->id, 'subject_id' => $subject->id, 'date' => today()->toDateString(), 'status' => 'present']);

    expect($parent->fresh()->notifications()->count())->toBe(0);
});

it('notifies the parent when a grade is entered', function () {
    [$parent, $child] = makeParentWithChild();
    $subject = Subject::create(['name' => 'علوم', 'code' => 'SCI', 'grade_id' => \App\Models\Grade::create(['name' => 'ص', 'level' => 1])->id]);

    GradeRecord::create([
        'student_id' => $child->id, 'subject_id' => $subject->id,
        'exam_type' => 'final', 'term' => 'first', 'score' => 90, 'max_score' => 100,
    ]);

    $parent->refresh();
    expect($parent->notifications()->count())->toBe(1)
        ->and($parent->notifications()->first()->data['type'])->toBe('grade');
});

it('marks all notifications as read from the parent notifications page', function () {
    [$parent, $child] = makeParentWithChild();
    $subject = Subject::create(['name' => 'علوم', 'code' => 'SCI', 'grade_id' => \App\Models\Grade::create(['name' => 'ص', 'level' => 1])->id]);
    Attendance::create(['student_id' => $child->id, 'subject_id' => $subject->id, 'date' => today()->toDateString(), 'status' => 'absent']);

    expect($parent->fresh()->unreadNotifications()->count())->toBe(1);

    Filament::setCurrentPanel(Filament::getPanel('parent'));
    $this->actingAs($parent);
    Livewire::test(ParentNotifications::class)->call('markAllRead');

    expect($parent->fresh()->unreadNotifications()->count())->toBe(0);
});
