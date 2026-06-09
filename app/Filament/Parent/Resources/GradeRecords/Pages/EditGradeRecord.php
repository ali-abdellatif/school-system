<?php

namespace App\Filament\Parent\Resources\GradeRecords\Pages;

use App\Filament\Parent\Resources\GradeRecords\GradeRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeRecord extends EditRecord
{
    protected static string $resource = GradeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
