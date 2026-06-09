<?php

namespace App\Filament\Parent\Resources\GradeRecords\Pages;

use App\Filament\Parent\Resources\GradeRecords\GradeRecordResource;
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
