<?php

namespace App\Filament\Teacher\Resources\Students\Pages;

use App\Filament\Teacher\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
