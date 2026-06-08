<?php

namespace App\Filament\Resources\Grades\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الصف')
                    ->placeholder('الصف الأول الابتدائي')
                    ->required()
                    ->maxLength(255),
                TextInput::make('level')
                    ->label('الترتيب (المستوى)')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('رقم لترتيب الصفوف تصاعديًا.'),
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::current()->value('id'))
                    ->searchable()
                    ->preload(),
            ]);
    }
}
