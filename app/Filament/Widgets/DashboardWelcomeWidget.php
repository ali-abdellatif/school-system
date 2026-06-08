<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\Sections\SectionResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\AcademicYear;
use App\Models\Application;
use Filament\Widgets\Widget;

class DashboardWelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-welcome';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'date' => now()->locale('ar')->isoFormat('dddd، D MMMM YYYY'),
            'academicYear' => AcademicYear::current()->value('name') ?? 'لم تُحدّد',
            'pendingCount' => Application::query()->where('status', 'pending')->count(),
            'applicationsUrl' => ApplicationResource::getUrl('index'),
            'studentCreateUrl' => StudentResource::getUrl('create'),
            'sectionsUrl' => SectionResource::getUrl('index'),
            'schoolName' => school('name'),
        ];
    }
}
