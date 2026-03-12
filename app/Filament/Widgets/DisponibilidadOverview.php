<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DisponibilidadOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total        = Device::count();
        $disponibles  = Device::where('disponibilidad', 'disponible')->count();
        $asignados    = Device::where('disponibilidad', 'asignado')->count();
        $reparacion   = Device::where('disponibilidad', 'en_reparacion')->count();
        $baja         = Device::where('disponibilidad', 'dado_de_baja')->count();

        return [
            Stat::make('Total de equipos', $total)
                ->icon('heroicon-o-device-tablet')
                ->color('gray'),

            Stat::make('Disponibles', $disponibles)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description('Listos para asignar'),

            Stat::make('Asignados', $asignados)
                ->icon('heroicon-o-user-circle')
                ->color('info')
                ->description('En manos de ejecutivos'),

            Stat::make('En reparación', $reparacion)
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Dados de baja', $baja)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}