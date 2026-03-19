<?php

namespace App\Observers;

use App\Models\DeviceMovement;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class DeviceMovementObserver
{
    public function creating(DeviceMovement $movement): void
    {
        if ($movement->tipo !== 'salida' || !$movement->recipient_id) {
            return;
        }

        $categoryId = $movement->device->deviceModel->category_id;
        $recipient = $movement->recipient;

        $tieneExcepcion = $recipient->exceptionCategories()
            ->where('categories.id', $categoryId)
            ->exists();

        if ($tieneExcepcion) {
            return;
        }

        $tieneAsignado = DeviceMovement::where('recipient_id', $movement->recipient_id)
            ->where('tipo', 'salida')
            ->whereNull('fecha_devolucion')
            ->whereHas('device.deviceModel', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->exists();

        if ($tieneAsignado) {
            Notification::make()
                ->title('Asignación no permitida')
                ->body('Este ejecutivo ya tiene un equipo de esta categoría asignado y no cuenta con una excepción.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'device_id' => 'El receptor ya posee un equipo de esta categoría.',
            ]);
        }
    }

    public function created(DeviceMovement $movement): void
    {
        $device = $movement->device;

        if ($movement->tipo === 'salida') {
            $device->update(['disponibilidad' => 'asignado']);
        }

        if ($movement->tipo === 'entrada') {
            $device->update(['disponibilidad' => 'disponible']);
        }
    }
}