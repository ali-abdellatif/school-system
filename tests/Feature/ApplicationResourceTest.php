<?php

use App\Filament\Resources\Applications\Pages\CreateApplication;
use App\Filament\Resources\Applications\Pages\EditApplication;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Filament\Widgets\ApplicationsOverviewWidget;
use App\Models\Application;
use App\Models\Grade;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create());
});

function makeApplication(array $overrides = []): Application
{
    $grade = Grade::create(['name' => 'الصف الأول الابتدائي', 'level' => 1]);

    return Application::create(array_merge([
        'first_name' => 'محمد',
        'last_name' => 'أحمد',
        'birth_date' => '2015-03-01',
        'gender' => 'male',
        'parent_name' => 'أحمد علي',
        'parent_phone' => '0500000001',
        'parent_relation' => 'father',
        'grade_applying_for' => $grade->id,
        'status' => 'rejected',
        'rejection_reason' => 'سبب تجريبي',
    ], $overrides));
}

it('renders the applications list page', function () {
    Livewire::test(ListApplications::class)->assertOk();
});

it('forbids the create page (applications come from the public site only)', function () {
    Livewire::test(CreateApplication::class)->assertForbidden();
});

it('renders the edit page with the status section populated', function () {
    $record = makeApplication();

    Livewire::test(EditApplication::class, ['record' => $record->getKey()])
        ->assertOk()
        ->assertSchemaStateSet(['status' => 'rejected']);
});

it('renders the view page infolist', function () {
    $record = makeApplication();

    Livewire::test(ViewApplication::class, ['record' => $record->getKey()])->assertOk();
});

it('approve action sets status, reviewer and timestamp', function () {
    $record = makeApplication(['status' => 'pending', 'rejection_reason' => null]);

    Livewire::test(ListApplications::class)
        ->callTableAction('approve', $record)
        ->assertHasNoTableActionErrors();

    $record->refresh();
    expect($record->status)->toBe('approved')
        ->and($record->reviewed_by)->not->toBeNull()
        ->and($record->reviewed_at)->not->toBeNull();
});

it('renders the overview widget', function () {
    makeApplication(['status' => 'approved']);

    Livewire::test(ApplicationsOverviewWidget::class)->assertOk();
});
