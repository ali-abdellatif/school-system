<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\Widget;

class ParentWelcomeWidget extends Widget
{
    protected string $view = 'filament.parent.widgets.welcome';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'name' => auth()->user()?->name,
            'date' => now()->locale('ar')->isoFormat('dddd، D MMMM YYYY'),
            'childrenCount' => auth()->user()?->students()->count() ?? 0,
        ];
    }
}
