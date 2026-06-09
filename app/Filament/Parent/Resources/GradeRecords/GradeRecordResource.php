<?php

namespace App\Filament\Parent\Resources\GradeRecords;

use App\Filament\Parent\Resources\GradeRecords\Pages\ListGradeRecords;
use App\Filament\Parent\Resources\GradeRecords\Tables\GradeRecordsTable;
use App\Models\GradeRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class GradeRecordResource extends Resource
{
    protected static ?string $model = GradeRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'متابعة أبنائي';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'الدرجات';

    protected static ?string $modelLabel = 'درجة';

    protected static ?string $pluralModelLabel = 'الدرجات';

    public static function table(Table $table): Table
    {
        return GradeRecordsTable::configure($table);
    }

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

    public static function getEloquentQuery(): Builder
    {
        $childIds = auth()->user()?->students()->pluck('id')->all() ?? [];

        return parent::getEloquentQuery()->whereIn('student_id', $childIds ?: [0]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGradeRecords::route('/'),
        ];
    }
}
