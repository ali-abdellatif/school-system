<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\AcademicYear;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
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
                Select::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::current()->value('id'))
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->label('التاريخ')
                    ->required()
                    ->native(false)
                    ->default(today()),
                Select::make('status')
                    ->label('الحالة')
                    ->required()
                    ->default('present')
                    ->options([
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'بعذر',
                    ]),
                Textarea::make('note')
                    ->label('ملاحظة')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
