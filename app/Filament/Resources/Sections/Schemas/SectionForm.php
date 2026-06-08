<?php

namespace App\Filament\Resources\Sections\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الفصل')
                    ->placeholder('أ')
                    ->required()
                    ->maxLength(255),
                Select::make('grade_id')
                    ->label('الصف')
                    ->relationship('grade', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::current()->value('id'))
                    ->searchable()
                    ->preload(),
                TextInput::make('max_students')
                    ->label('السعة القصوى')
                    ->numeric()
                    ->minValue(1)
                    ->default(30)
                    ->required(),
                Select::make('teacher_id')
                    ->label('مربّي الفصل')
                    ->relationship('homeroomTeacher', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
