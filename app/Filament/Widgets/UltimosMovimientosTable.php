<?php

namespace App\Filament\Widgets;

use App\Models\DeviceMovement;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosMovimientosTable extends BaseWidget
{
    protected static ?string $heading = 'Últimos 10 Movimientos';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(DeviceMovement::query()->latest()->limit(10))
            ->paginated(false) // Desactivamos paginación para ver solo 10
            ->columns([
                TextColumn::make('fecha_entrega')->date('d/m/Y')->label('Fecha'),
                TextColumn::make('tipo')->badge()->color(fn ($state) => $state === 'salida' ? 'warning' : 'success'),
                TextColumn::make('device.numero_serie')->label('Equipo'),
                TextColumn::make('recipient.nombre')->label('Involucrado'),
                TextColumn::make('comentario')->limit(30),
            ]);
    }
}