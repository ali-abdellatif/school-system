<?php

namespace App\Filament\Parent\Resources\LessonMaterials\Pages;

use App\Filament\Parent\Resources\LessonMaterials\LessonMaterialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLessonMaterial extends CreateRecord
{
    protected static string $resource = LessonMaterialResource::class;
}
