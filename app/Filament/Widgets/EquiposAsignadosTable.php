<?php

namespace App\Filament\Widgets;

use App\Models\DeviceMovement;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class EquiposAsignadosTable extends BaseWidget
{
    protected static ?string $heading = 'Equipos actualmente asignados';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeviceMovement::query()
                    ->where('tipo', 'salida')
                    ->whereNull('fecha_devolucion')
                    ->with(['device.deviceModel.brand', 'recipient'])
                    ->latest('fecha_entrega')
            )
            ->columns([
                TextColumn::make('device.deviceModel.brand.nombre')
                    ->label('Marca')
                    ->sortable(),

                TextColumn::make('device.deviceModel.nombre')
                    ->label('Modelo')
                    ->sortable(),

                TextColumn::make('device.numero_serie')
                    ->label('Serie')
                    ->searchable(),

                TextColumn::make('recipient.nombre')
                    ->label('Asignado a')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recipient.departamento')
                    ->label('Departamento')
                    ->sortable(),

                TextColumn::make('fecha_entrega')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_devolucion')
                    ->label('Devolución')
                    ->date('d/m/Y')
                    ->placeholder('Permanente'),
            ]);
    }
}