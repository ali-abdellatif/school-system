<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('اسم المادة')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('رمز المادة')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('grade_id')
                    ->label('الصف')
                    ->relationship('grade', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('teacher_id')
                    ->label('المعلم المسؤول')
                    ->relationship('teacher', 'employee_number', fn ($query) => $query->where('status', 'active'))
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->user?->name ?? $record->employee_number)
                    ->searchable()
                    ->preload()
                    ->placeholder('غير محدد'),
                TextInput::make('weekly_hours')
                    ->label('عدد الحصص الأسبوعية')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(1)
                    ->required(),
                Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
