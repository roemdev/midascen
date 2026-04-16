<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use Filament\Widgets\ChartWidget;

class DispositivosPorEstado extends ChartWidget
{
    protected ?string $heading = 'Disponibilidad General';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [
                        Device::where('disponibilidad', 'disponible')->count(),
                        Device::where('disponibilidad', 'asignado')->count(),
                        Device::where('disponibilidad', 'en_reparacion')->count(),
                        Device::where('disponibilidad', 'dado_de_baja')->count(),
                    ],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['Disponible', 'Asignado', 'Reparación', 'Baja'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}