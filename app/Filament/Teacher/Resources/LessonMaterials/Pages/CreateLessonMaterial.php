<?php

namespace App\Filament\Teacher\Resources\LessonMaterials\Pages;

use App\Filament\Teacher\Resources\LessonMaterials\LessonMaterialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLessonMaterial extends CreateRecord
{
    protected static string $resource = LessonMaterialResource::class;
}
