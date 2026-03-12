<?php

namespace App\Filament\Resources\DeviceMovements\Pages;

use App\Filament\Resources\DeviceMovements\DeviceMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeviceMovements extends ListRecords
{
    protected static string $resource = DeviceMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
