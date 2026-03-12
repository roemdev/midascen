<?php

namespace App\Filament\Resources\DeviceMovements\Pages;

use App\Filament\Resources\DeviceMovements\DeviceMovementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeviceMovement extends CreateRecord
{
    protected static string $resource = DeviceMovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}