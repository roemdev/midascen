<?php

namespace App\Observers;

use App\Models\DeviceMovement;

class DeviceMovementObserver
{
    public function creating(DeviceMovement $movement): void
    {
        if ($movement->tipo !== 'salida') return;
        if (!$movement->recipient_id) return;

        $categoryId = $movement->device->deviceModel->category_id;

        $tieneAsignado = DeviceMovement::where('recipient_id', $movement->recipient_id)
            ->where('tipo', 'salida')
            ->whereNull('fecha_devolucion')
            ->whereHas('device.deviceModel', fn ($q) =>
                $q->where('category_id', $categoryId)
            )
            ->exists();

        if ($tieneAsignado) {
            throw new \Exception(
                "Este ejecutivo ya tiene un equipo de esta categoría asignado."
            );
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