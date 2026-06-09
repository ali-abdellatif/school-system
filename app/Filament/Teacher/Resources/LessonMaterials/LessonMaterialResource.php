<?php

namespace App\Filament\Teacher\Resources\LessonMaterials;

use App\Filament\Teacher\Resources\LessonMaterials\Pages\CreateLessonMaterial;
use App\Filament\Teacher\Resources\LessonMaterials\Pages\EditLessonMaterial;
use App\Filament\Teacher\Resources\LessonMaterials\Pages\ListLessonMaterials;
use App\Filament\Teacher\Resources\LessonMaterials\Schemas\LessonMaterialForm;
use App\Filament\Teacher\Resources\LessonMaterials\Tables\LessonMaterialsTable;
use App\Models\LessonMaterial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonMaterialResource extends Resource
{
    protected static ?string $model = LessonMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'المواد التعليمية';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'مادة تعليمية';

    protected static ?string $pluralModelLabel = 'المواد التعليمية';

    /** نطاق المعلم: مواده التعليمية فقط. */
    public static function getEloquentQuery(): Builder
    {
        $teacher = auth()->user()?->teacher;

        return parent::getEloquentQuery()->where('teacher_id', $teacher?->id ?? 0);
    }

    public static function form(Schema $schema): Schema
    {
        return LessonMaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonMaterialsTable::configure($table);
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
            'index' => ListLessonMaterials::route('/'),
            'create' => CreateLessonMaterial::route('/create'),
            'edit' => EditLessonMaterial::route('/{record}/edit'),
        ];
    }
}
