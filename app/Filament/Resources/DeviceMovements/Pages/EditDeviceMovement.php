<?php

namespace App\Filament\Resources\DeviceMovements\Pages;

use App\Filament\Resources\DeviceMovements\DeviceMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeviceMovement extends EditRecord
{
    protected static string $resource = DeviceMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
