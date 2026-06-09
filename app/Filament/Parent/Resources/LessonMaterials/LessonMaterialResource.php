<?php

namespace App\Filament\Parent\Resources\LessonMaterials;

use App\Filament\Parent\Resources\LessonMaterials\Pages\ListLessonMaterials;
use App\Filament\Parent\Resources\LessonMaterials\Tables\LessonMaterialsTable;
use App\Models\LessonMaterial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LessonMaterialResource extends Resource
{
    protected static ?string $model = LessonMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|UnitEnum|null $navigationGroup = 'متابعة أبنائي';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'المواد التعليمية';

    protected static ?string $modelLabel = 'مادة تعليمية';

    protected static ?string $pluralModelLabel = 'المواد التعليمية';

    public static function table(Table $table): Table
    {
        return LessonMaterialsTable::configure($table);
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

    /** المواد المنشورة لفصول الأبناء (أو العامة لكل الفصول). */
    public static function getEloquentQuery(): Builder
    {
        $sectionIds = auth()->user()?->students()->pluck('section_id')->filter()->all() ?? [];

        return parent::getEloquentQuery()
            ->where('is_published', true)
            ->where(function (Builder $q) use ($sectionIds): void {
                $q->whereNull('section_id')->orWhereIn('section_id', $sectionIds ?: [0]);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLessonMaterials::route('/'),
        ];
    }
}
