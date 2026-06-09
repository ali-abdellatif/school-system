<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Widgets\Widget;

class TeacherGreetingWidget extends Widget
{
    protected string $view = 'filament.teacher.widgets.greeting';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'name' => auth()->user()?->name,
            'date' => now()->locale('ar')->isoFormat('dddd، D MMMM YYYY'),
        ];
    }
}
