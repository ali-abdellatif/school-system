<?php

use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Livewire\ApplicationStatus;
use App\Livewire\PublicApplicationForm;
use App\Models\Application;
use App\Models\Grade;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

it('disables creation in the resource', function () {
    expect(ApplicationResource::canCreate())->toBeFalse();
});

it('returns an empty header actions array', function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create());

    $page = new ListApplications();
    $method = (new ReflectionMethod($page, 'getHeaderActions'));
    $method->setAccessible(true);

    expect($method->invoke($page))->toBe([]);
});

it('walks the multi-step public form and stores a pending application', function () {
    $grade = Grade::create(['name' => 'الصف الأول الابتدائي', 'level' => 1]);

    Livewire::test(PublicApplicationForm::class)
        // step 1
        ->set('first_name', 'محمد')
        ->set('last_name', 'أحمد')
        ->set('birth_date', '2015-03-01')
        ->set('gender', 'male')
        ->set('grade_applying_for', (string) $grade->id)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 2)
        // step 2
        ->set('parent_name', 'أحمد علي')
        ->set('parent_phone', '0500000001')
        ->set('parent_relation', 'father')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 3)
        // step 3 submit
        ->set('notes', 'لا يوجد')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $application = Application::query()->latest('id')->first();
    expect($application)->not->toBeNull()
        ->and($application->status)->toBe('pending')
        ->and($application->first_name)->toBe('محمد')
        ->and($application->grade_applying_for)->toBe($grade->id);
});

it('blocks advancing past step 1 when required fields are missing', function () {
    Livewire::test(PublicApplicationForm::class)
        ->call('nextStep')
        ->assertHasErrors(['first_name', 'last_name', 'birth_date', 'gender'])
        ->assertSet('currentStep', 1);
});

it('finds an application by id and matching parent phone', function () {
    $app = Application::create([
        'first_name' => 'سارة', 'last_name' => 'خالد', 'birth_date' => '2016-01-01',
        'gender' => 'female', 'parent_name' => 'خالد', 'parent_phone' => '0512345678',
        'parent_relation' => 'father', 'status' => 'approved',
    ]);

    Livewire::test(ApplicationStatus::class)
        ->set('application_id', (string) $app->id)
        ->set('parent_phone', '0512345678')
        ->call('check')
        ->assertHasNoErrors()
        ->assertSet('searched', true)
        ->assertSet('application.id', $app->id);
});

it('returns no application when phone does not match', function () {
    $app = Application::create([
        'first_name' => 'سارة', 'last_name' => 'خالد', 'birth_date' => '2016-01-01',
        'gender' => 'female', 'parent_name' => 'خالد', 'parent_phone' => '0512345678',
        'parent_relation' => 'father', 'status' => 'pending',
    ]);

    Livewire::test(ApplicationStatus::class)
        ->set('application_id', (string) $app->id)
        ->set('parent_phone', '0000000000')
        ->call('check')
        ->assertSet('searched', true)
        ->assertSet('application', null);
});
