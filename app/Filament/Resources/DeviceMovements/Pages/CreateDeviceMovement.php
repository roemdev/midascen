<?php

namespace App\Filament\Resources\DeviceMovements\Pages;

use App\Filament\Resources\DeviceMovements\DeviceMovementResource;
use App\Models\DeviceMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateDeviceMovement extends CreateRecord
{
    protected static string $resource = DeviceMovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Forzamos a que sea un array por la selección múltiple
        $deviceIds = (array) $data['device_id'];
        $record = null;

        // Creamos un registro independiente por cada dispositivo seleccionado
        foreach ($deviceIds as $id) {
            $record = DeviceMovement::create([
                'device_id'        => $id,
                'user_id'          => Auth::id(),
                'recipient_id'     => $data['recipient_id'] ?? null,
                'tipo'             => $data['tipo'],
                'fecha_entrega'    => $data['fecha_entrega'],
                'fecha_devolucion' => $data['fecha_devolucion'] ?? null,
                'motivo'           => $data['motivo'] ?? null,
                'referencia'       => $data['referencia'] ?? null,
            ]);
        }

        // Retornamos el último registro para que Filament complete el flujo sin errores
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}