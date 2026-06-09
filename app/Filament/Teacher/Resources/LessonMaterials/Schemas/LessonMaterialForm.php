<?php

namespace App\Filament\Teacher\Resources\LessonMaterials\Schemas;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Subject;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LessonMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        $teacher = auth()->user()?->teacher;
        $sectionIds = $teacher ? $teacher->assignedSectionIds() : [];
        $subjectIds = $teacher ? $teacher->assignedSubjectIds() : [];

        return $schema
            ->columns(2)
            ->components([
                Hidden::make('teacher_id')->default(fn () => auth()->user()?->teacher?->id),
                Hidden::make('academic_year_id')->default(fn () => AcademicYear::current()->value('id')),

                TextInput::make('title')->label('عنوان الدرس')->required()->maxLength(255)->columnSpanFull(),
                Select::make('subject_id')
                    ->label('المادة')
                    ->options(fn () => Subject::query()->whereIn('id', $subjectIds ?: [0])->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('section_id')
                    ->label('الفصل (اتركه فارغًا = كل الفصول)')
                    ->options(fn () => Section::query()->whereIn('id', $sectionIds ?: [0])->pluck('name', 'id'))
                    ->placeholder('كل الفصول')
                    ->searchable(),
                Select::make('type')
                    ->label('النوع')
                    ->required()
                    ->default('pdf')
                    ->live()
                    ->options([
                        'pdf' => 'ملف PDF', 'document' => 'مستند', 'image' => 'صورة',
                        'video' => 'فيديو', 'link' => 'رابط خارجي',
                    ]),
                Toggle::make('is_published')->label('منشور')->default(true)->inline(false),

                FileUpload::make('file_path')
                    ->label('الملف')
                    ->directory('lesson-materials')
                    ->visibility('public')
                    ->visible(fn (Get $get): bool => in_array($get('type'), ['pdf', 'document', 'image']))
                    ->columnSpanFull(),
                TextInput::make('external_url')
                    ->label('الرابط الخارجي')
                    ->url()
                    ->visible(fn (Get $get): bool => in_array($get('type'), ['video', 'link']))
                    ->columnSpanFull(),

                Textarea::make('description')->label('الوصف')->rows(3)->columnSpanFull(),
            ]);
    }
}
