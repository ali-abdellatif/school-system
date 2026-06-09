<?php

namespace App\Filament\Parent\Resources\Attendances\Pages;

use App\Filament\Parent\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
