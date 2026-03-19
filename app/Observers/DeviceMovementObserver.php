<?php

namespace App\Observers;

use App\Models\DeviceMovement;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class DeviceMovementObserver
{
    /**
     * Se ejecuta antes de crear el movimiento.
     * Aquí validamos si el ejecutivo ya tiene un equipo de la misma categoría.
     */
    public function creating(DeviceMovement $movement): void
    {
        // Solo validamos si es una salida y hay un receptor asignado
        if ($movement->tipo !== 'salida' || !$movement->recipient_id) {
            return;
        }

        // Obtenemos la categoría del equipo que se intenta entregar
        // Nota: Asegúrate de que la relación se llame 'deviceModel' en tu modelo Device
        $categoryId = $movement->device->deviceModel->category_id;
        $recipient = $movement->recipient;

        // 1. Verificar si el ejecutivo tiene una excepción para esta categoría específica
        $tieneExcepcion = $recipient->exceptionCategories()
            ->where('categories.id', $categoryId)
            ->exists();

        if ($tieneExcepcion) {
            return;
        }

        // 2. Validación normal: Verificar si ya tiene un equipo activo de esa categoría
        $tieneAsignado = DeviceMovement::where('recipient_id', $movement->recipient_id)
            ->where('tipo', 'salida')
            ->whereNull('fecha_devolucion')
            ->whereHas('device.deviceModel', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->exists();

        if ($tieneAsignado) {
            // Notificación visual de Filament
            Notification::make()
                ->title('Asignación no permitida')
                ->body('Este ejecutivo ya tiene un equipo de esta categoría asignado y no cuenta con una excepción.')
                ->danger()
                ->send();

            // Lanzar excepción de validación para detener el proceso de guardado
            throw ValidationException::withMessages([
                'device_id' => 'El receptor ya posee un equipo de esta categoría.',
            ]);
        }
    }

    /**
     * Se ejecuta después de que el movimiento se ha guardado.
     * Actualiza el estado de disponibilidad del equipo.
     */
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