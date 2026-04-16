<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DisponibilidadOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Equipos Nuevos', Device::where('condicion', 'nuevo')->count())
                ->icon('heroicon-m-sparkles')
                ->description('Equipos en inventario sin uso previo')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Equipos Usados', Device::where('condicion', 'usado')->count())
                ->icon('heroicon-m-arrow-path')
                ->description('Equipos actualmente en rotación o retorno')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('warning'),
                
            Stat::make('Equipos Dañados', Device::where('condicion', 'danado')->count())
                ->icon('heroicon-m-x-circle')
                ->description('Equipos pendientes de revisión técnica')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}