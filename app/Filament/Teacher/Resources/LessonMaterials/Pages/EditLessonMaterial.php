<?php

namespace App\Filament\Teacher\Resources\LessonMaterials\Pages;

use App\Filament\Teacher\Resources\LessonMaterials\LessonMaterialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLessonMaterial extends EditRecord
{
    protected static string $resource = LessonMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
