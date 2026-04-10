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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

    /**
     * Formulario de CREACIÓN — permite selección múltiple de equipos.
     * Este schema lo usa CreateDeviceMovement.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::createFormComponents());
    }

    /**
     * Componentes del formulario de creación (con device_id múltiple).
     * Se expone como método público para que CreateDeviceMovement lo reutilice.
     */
    public static function createFormComponents(): array
    {
        return [
            Select::make('tipo')
                ->label('Tipo de movimiento')
                ->options([
                    'entrada' => 'Entrada',
                    'salida'  => 'Salida',
                ])
                ->required()
                ->live(),

            Select::make('device_id')
                ->label('Equipos')
                ->multiple()
                ->options(function (Get $get) {
                    $tipo = $get('tipo');
                    if (!$tipo) return [];

                    return Device::with('deviceModel.brand')
                        ->when(
                            $tipo === 'salida',
                            fn ($q) => $q->where('disponibilidad', 'disponible')
                        )
                        ->when(
                        // Entradas: solo equipos asignados
                        // (excluye disponible, en_reparacion y dado_de_baja)
                            $tipo === 'entrada',
                            fn ($q) => $q->where('disponibilidad', 'asignado')
                        )
                        ->get()
                        ->mapWithKeys(fn ($d) => [
                            $d->id => "{$d->deviceModel->brand->nombre} {$d->deviceModel->nombre} — {$d->numero_serie}"
                        ]);
                })
                ->searchable()
                ->required()
                ->live()
                ->helperText('Puedes seleccionar uno o varios equipos.'),

            Select::make('recipient_id')
                ->label('Ejecutivo destinatario')
                ->relationship('recipient', 'nombre')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida')
                ->required(fn (Get $get): bool => $get('tipo') === 'salida'),

            DatePicker::make('fecha_entrega')
                ->label('Fecha de entrega')
                ->required()
                ->default(now())
                ->displayFormat('d/m/Y'),

            DatePicker::make('fecha_devolucion')
                ->label('Fecha de devolución')
                ->displayFormat('d/m/Y')
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida')
                ->after('fecha_entrega'),

            TextInput::make('motivo')
                ->label('Motivo')
                ->maxLength(255),

            TextInput::make('referencia')
                ->label('Referencia')
                ->maxLength(100),
        ];
    }

    /**
     * Componentes del formulario de EDICIÓN — device_id es un Select simple,
     * no múltiple, porque cada registro tiene exactamente un equipo.
     */
    public static function editFormComponents(): array
    {
        return [
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
                ->options(function () {
                    return Device::with('deviceModel.brand')
                        ->get()
                        ->mapWithKeys(fn ($d) => [
                            $d->id => "{$d->deviceModel->brand->nombre} {$d->deviceModel->nombre} — {$d->numero_serie}"
                        ]);
                })
                ->searchable()
                ->required()
                ->disabled() // No tiene sentido cambiar el equipo al editar
                ->helperText('El equipo no puede cambiarse una vez registrado el movimiento.'),

            Select::make('recipient_id')
                ->label('Ejecutivo destinatario')
                ->relationship('recipient', 'nombre')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida'),

            DatePicker::make('fecha_entrega')
                ->label('Fecha de entrega')
                ->required()
                ->displayFormat('d/m/Y'),

            DatePicker::make('fecha_devolucion')
                ->label('Fecha de devolución')
                ->displayFormat('d/m/Y')
                ->visible(fn (Get $get): bool => $get('tipo') === 'salida')
                ->after('fecha_entrega'),

            TextInput::make('motivo')
                ->label('Motivo')
                ->maxLength(255),

            TextInput::make('referencia')
                ->label('Referencia')
                ->maxLength(100),
        ];
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
                    ->label('Marca'),

                TextColumn::make('recipient.nombre')
                    ->label('Ejecutivo')
                    ->placeholder('—'),

                TextColumn::make('fecha_entrega')
                    ->label('Entrega')
                    ->date('d/m/Y'),

                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida'  => 'Salida',
                    ]),
            ])
            ->actions([
                ViewAction::make()->label('Ver'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
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
