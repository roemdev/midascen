<?php

namespace App\Filament\Resources\DeviceMovements;

use App\Filament\Resources\DeviceMovements\Pages;
use App\Models\DeviceMovement;
use App\Models\Device;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DeviceMovementResource extends Resource
{
    protected static ?string $model = DeviceMovement::class;
    protected static ?string $navigationLabel = 'Movimientos';
    protected static ?string $modelLabel = 'Movimiento';
    protected static ?string $pluralModelLabel = 'Movimientos';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'Inventario';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrows-right-left';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo')
                ->label('Tipo de movimiento')
                ->options([
                    'entrada' => 'Entrada',
                    'salida'  => 'Salida',
                ])
                ->required()
                ->live(),

            Select::make('device_id')
                ->label('Equipo')
                ->options(function (Get $get) {
                    $tipo = $get('tipo');
                    if (!$tipo) return [];

                    return Device::with('deviceModel.brand')
                        ->when(
                            $tipo === 'salida',
                            fn ($q) => $q->where('disponibilidad', 'disponible')
                        )
                        ->when(
                            $tipo === 'entrada',
                            fn ($q) => $q->whereIn('disponibilidad', ['asignado'])
                        )
                        ->get()
                        ->mapWithKeys(fn ($d) => [
                            $d->id => "{$d->deviceModel->brand->nombre} {$d->deviceModel->nombre} — {$d->numero_serie}"
                        ]);
                })
                ->searchable()
                ->required()
                ->live()
                ->helperText('Solo muestra equipos disponibles para el tipo de movimiento seleccionado'),

            Select::make('recipient_id')
                ->label('Ejecutivo destinatario')
                ->relationship('recipient', 'nombre')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida')
                ->required(fn (Get $get): bool => $get('tipo') === 'salida')
                ->helperText('Solo requerido en salidas'),

            DatePicker::make('fecha_entrega')
                ->label('Fecha de entrega')
                ->required()
                ->default(now())
                ->displayFormat('d/m/Y'),

            DatePicker::make('fecha_devolucion')
                ->label('Fecha de devolución')
                ->displayFormat('d/m/Y')
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida')
                ->helperText('Dejar vacío si la asignación es permanente')
                ->after('fecha_entrega'),

            TextInput::make('motivo')
                ->label('Motivo')
                ->maxLength(255)
                ->placeholder('ej: Asignación corporativa, Devolución por cambio de equipo'),

            TextInput::make('referencia')
                ->label('Referencia')
                ->maxLength(100)
                ->placeholder('ej: Factura F-2024-441, Orden OC-001'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success',
                        'salida'  => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('device.numero_serie')
                    ->label('Serie')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('device.deviceModel.brand.nombre')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('device.deviceModel.nombre')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recipient.nombre')
                    ->label('Ejecutivo')
                    ->searchable()
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('fecha_entrega')
                    ->label('Entrega')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_devolucion')
                    ->label('Devolución')
                    ->date('d/m/Y')
                    ->placeholder('Permanente')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida'  => 'Salida',
                    ]),

                SelectFilter::make('recipient_id')
                    ->label('Ejecutivo')
                    ->relationship('recipient', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make()->label('Ver'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('No hay movimientos registrados')
            ->emptyStateDescription('Los movimientos se generan al registrar entradas y salidas de equipos.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDeviceMovements::route('/'),
            'create' => Pages\CreateDeviceMovement::route('/create'),
            'edit'   => Pages\EditDeviceMovement::route('/{record}/edit'),
        ];
    }
}