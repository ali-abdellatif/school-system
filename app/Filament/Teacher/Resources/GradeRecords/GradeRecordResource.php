<?php

namespace App\Filament\Teacher\Resources\GradeRecords;

use App\Filament\Teacher\Resources\GradeRecords\Pages\CreateGradeRecord;
use App\Filament\Teacher\Resources\GradeRecords\Pages\EditGradeRecord;
use App\Filament\Teacher\Resources\GradeRecords\Pages\ListGradeRecords;
use App\Filament\Teacher\Resources\GradeRecords\Schemas\GradeRecordForm;
use App\Filament\Teacher\Resources\GradeRecords\Tables\GradeRecordsTable;
use App\Models\GradeRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GradeRecordResource extends Resource
{
    protected static ?string $model = GradeRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'رصد الدرجات';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'درجة';

    protected static ?string $pluralModelLabel = 'الدرجات';

    public static function form(Schema $schema): Schema
    {
        return GradeRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradeRecordsTable::configure($table);
    }

    /** المعلم لا يحذف الدرجات، فقط يضيف/يعدّل. */
    public static function canDelete($record): bool
    {
        return false;
    }

    /** نطاق المعلم: درجات مواده وفصوله فقط. */
    public static function getEloquentQuery(): Builder
    {
        $teacher = auth()->user()?->teacher;
        $subjectIds = $teacher ? $teacher->assignedSubjectIds() : [];

        return parent::getEloquentQuery()->whereIn('subject_id', $subjectIds ?: [0]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGradeRecords::route('/'),
            'create' => CreateGradeRecord::route('/create'),
            'edit' => EditGradeRecord::route('/{record}/edit'),
        ];
    }
}
