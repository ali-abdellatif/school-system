<?php

namespace App\Filament\Resources\GradeRecords\Schemas;

use App\Models\AcademicYear;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('student_id')
                    ->label('الطالب')
                    ->options(fn () => Student::query()->with('section')->get()
                        ->mapWithKeys(fn (Student $s) => [$s->id => $s->full_name . ' — ' . ($s->section?->name ?? 'بدون فصل')]))
                    ->required()
                    ->searchable(),
                Select::make('subject_id')
                    ->label('المادة')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::current()->value('id'))
                    ->searchable()
                    ->preload(),
                Select::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('term')
                    ->label('الفصل الدراسي')
                    ->required()
                    ->default('first')
                    ->options([
                        'first' => 'الفصل الأول',
                        'second' => 'الفصل الثاني',
                    ]),
                Select::make('exam_type')
                    ->label('نوع التقييم')
                    ->required()
                    ->default('monthly1')
                    ->options([
                        'monthly1' => 'شهري 1',
                        'monthly2' => 'شهري 2',
                        'midterm' => 'نصف الفصل',
                        'final' => 'النهائي',
                        'assignment' => 'واجب',
                        'oral' => 'شفهي',
                    ]),
                TextInput::make('score')
                    ->label('الدرجة')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                TextInput::make('max_score')
                    ->label('الدرجة العظمى')
                    ->numeric()
                    ->minValue(1)
                    ->default(100)
                    ->required(),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
