<?php

namespace App\Filament\Resources\GradeRecords;

use App\Filament\Resources\GradeRecords\Pages\CreateGradeRecord;
use App\Filament\Resources\GradeRecords\Pages\EditGradeRecord;
use App\Filament\Resources\GradeRecords\Pages\ListGradeRecords;
use App\Filament\Resources\GradeRecords\Schemas\GradeRecordForm;
use App\Filament\Resources\GradeRecords\Tables\GradeRecordsTable;
use App\Models\GradeRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeRecordResource extends Resource
{
    protected static ?string $model = GradeRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'الحضور والدرجات';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'الدرجات';

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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
