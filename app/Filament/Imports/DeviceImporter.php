<?php

namespace App\Filament\Imports;

use App\Models\Device;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DeviceImporter extends Importer
{
    protected static ?string $model = Device::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('model_id')
                ->label('ID del Modelo')
                ->numeric()
                ->requiredMapping()
                ->rules(['required', 'integer', 'exists:models,id']),

            ImportColumn::make('numero_serie')
                ->label('Número de Serie')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:100']),

            ImportColumn::make('imei')
                ->label('IMEI')
                ->rules(['nullable', 'string', 'max:20']),

            ImportColumn::make('condicion')
                ->label('Condición')
                ->rules(['nullable', 'string', 'max:20']),

            ImportColumn::make('disponibilidad')
                ->label('Disponibilidad')
                ->rules(['nullable', 'string', 'max:20']),

            ImportColumn::make('notas')
                ->label('Notas')
                ->rules(['nullable', 'string']),
        ];
    }

    // Nota importante: Este método NO debe llevar la palabra "static"
    public function resolveRecord(): ?Device
    {
        // Validar que el CSV contenga el número de serie antes de buscar
        if (empty($this->data['numero_serie'])) {
            return new Device();
        }

        // Esto actualizará el equipo si ya existe el número de serie, 
        // o creará uno nuevo si no existe.
        return Device::firstOrNew([
            'numero_serie' => $this->data['numero_serie'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'La importación de equipos ha completado y se han procesado ' . number_format($import->successful_rows) . ' filas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' filas fallaron al importar. Revisa el archivo de reporte para más detalles.';
        }

        return $body;
    }
}