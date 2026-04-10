<?php

namespace App\Filament\Resources\DeviceMovements\Pages;

use App\Filament\Resources\DeviceMovements\DeviceMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditDeviceMovement extends EditRecord
{
    protected static string $resource = DeviceMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Sobreescribimos el form para usar los componentes de edición,
     * donde device_id es un Select simple (no múltiple).
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components(
            DeviceMovementResource::editFormComponents()
        );
    }
}
