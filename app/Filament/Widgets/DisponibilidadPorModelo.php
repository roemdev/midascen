<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use App\Models\DeviceModel;
use Filament\Widgets\ChartWidget;

class DisponibilidadPorModelo extends ChartWidget
{
    protected ?string $heading = 'Disponibilidad por Modelo';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $modelos = DeviceModel::has('devices')->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Disponibles',
                    'data' => $modelos->map(fn($m) => $m->devices()->where('disponibilidad', 'disponible')->count()),
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Asignados',
                    'data' => $modelos->map(fn($m) => $m->devices()->where('disponibilidad', 'asignado')->count()),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $modelos->pluck('nombre'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}