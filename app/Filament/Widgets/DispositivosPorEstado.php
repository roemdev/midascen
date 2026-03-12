<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DispositivosPorEstado extends ChartWidget
{
    protected ?string $heading = 'Dispositivos Por Estado';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
