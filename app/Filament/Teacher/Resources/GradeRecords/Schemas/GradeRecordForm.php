<?php

namespace App\Filament\Teacher\Resources\GradeRecords\Schemas;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subject;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        $teacher = auth()->user()?->teacher;
        $sectionIds = $teacher ? $teacher->assignedSectionIds() : [];
        $subjectIds = $teacher ? $teacher->assignedSubjectIds() : [];

        return $schema
            ->columns(2)
            ->components([
                Hidden::make('entered_by')->default(fn () => auth()->id()),
                Select::make('student_id')
                    ->label('الطالب')
                    ->options(fn () => Student::query()->whereIn('section_id', $sectionIds ?: [0])->get()
                        ->mapWithKeys(fn (Student $s) => [$s->id => $s->full_name]))
                    ->required()
                    ->searchable(),
                Select::make('subject_id')
                    ->label('المادة')
                    ->options(fn () => Subject::query()->whereIn('id', $subjectIds ?: [0])->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name', fn ($query) => $query->whereIn('id', $sectionIds ?: [0]))
                    ->searchable(),
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::current()->value('id')),
                Select::make('term')
                    ->label('الفصل الدراسي')
                    ->required()
                    ->default('first')
                    ->options(['first' => 'الفصل الأول', 'second' => 'الفصل الثاني']),
                Select::make('exam_type')
                    ->label('نوع التقييم')
                    ->required()
                    ->default('monthly1')
                    ->options([
                        'monthly1' => 'شهري 1', 'monthly2' => 'شهري 2', 'midterm' => 'نصف الفصل',
                        'final' => 'النهائي', 'assignment' => 'واجب', 'oral' => 'شفهي',
                    ]),
                TextInput::make('score')->label('الدرجة')->numeric()->minValue(0)->required(),
                TextInput::make('max_score')->label('الدرجة العظمى')->numeric()->minValue(1)->default(100)->required(),
                Textarea::make('notes')->label('ملاحظات')->rows(2)->columnSpanFull(),
            ]);
    }
}
