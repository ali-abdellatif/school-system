<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم السنة الدراسية')
                    ->placeholder('2024-2025')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('تاريخ البداية')
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('تاريخ النهاية')
                    ->native(false)
                    ->afterOrEqual('start_date'),
                Toggle::make('is_current')
                    ->label('السنة الحالية')
                    ->helperText('تعيينها كحالية يُلغي الحالية تلقائيًا عن بقية السنوات.')
                    ->columnSpanFull(),
            ]);
    }
}
