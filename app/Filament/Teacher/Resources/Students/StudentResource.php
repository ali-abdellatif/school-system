<?php

namespace App\Filament\Teacher\Resources\Students;

use App\Filament\Teacher\Resources\Students\Pages\ListStudents;
use App\Filament\Teacher\Resources\Students\Pages\ViewStudent;
use App\Filament\Teacher\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Teacher\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'طلابي';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'طالب';

    protected static ?string $pluralModelLabel = 'طلابي';

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    /** قراءة فقط — لا إنشاء/تعديل/حذف. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /** نطاق المعلم: طلاب فصوله فقط. */
    public static function getEloquentQuery(): Builder
    {
        $teacher = auth()->user()?->teacher;
        $sectionIds = $teacher ? $teacher->assignedSectionIds() : [];

        return parent::getEloquentQuery()->whereIn('section_id', $sectionIds ?: [0]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'view' => ViewStudent::route('/{record}'),
        ];
    }
}
