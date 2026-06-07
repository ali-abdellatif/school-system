<?php

namespace App\Support\Filament;

use Filament\Panel;
use Filament\Support\Colors\Color;

trait ConfiguresSchoolPanel
{
    protected function configureSchoolBrand(Panel $panel): Panel
    {
        return $panel
            ->brandName(config('school.name'))
            ->favicon(asset('favicon.svg'))
            ->font('Tajawal', 'https://fonts.bunny.net/css?family=tajawal:400,500,700,800,900')
            ->colors([
                'primary' => Color::hex('#1e3a5f'),
                'warning' => Color::hex('#f59e0b'),
                'success' => Color::Green,
                'danger' => Color::Red,
            ]);
    }
}
