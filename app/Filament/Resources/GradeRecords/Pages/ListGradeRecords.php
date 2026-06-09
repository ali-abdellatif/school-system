<?php

namespace App\Filament\Resources\GradeRecords\Pages;

use App\Filament\Resources\GradeRecords\GradeRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradeRecords extends ListRecords
{
    protected static string $resource = GradeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
