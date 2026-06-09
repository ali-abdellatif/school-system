<?php

namespace App\Filament\Parent\Resources\Attendances;

use App\Filament\Parent\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Parent\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'متابعة أبنائي';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'الحضور';

    protected static ?string $modelLabel = 'سجل حضور';

    protected static ?string $pluralModelLabel = 'الحضور';

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
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

    /** نطاق ولي الأمر: حضور أبنائه فقط. */
    public static function getEloquentQuery(): Builder
    {
        $childIds = auth()->user()?->students()->pluck('id')->all() ?? [];

        return parent::getEloquentQuery()->whereIn('student_id', $childIds ?: [0]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
        ];
    }
}
