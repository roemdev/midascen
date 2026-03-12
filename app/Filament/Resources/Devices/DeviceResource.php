<?php

namespace App\Filament\Resources\Devices;

use App\Filament\Resources\Devices\Pages;
use App\Models\Device;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;
    protected static ?string $navigationLabel = 'Equipos';
    protected static ?string $modelLabel = 'Equipo';
    protected static ?string $pluralModelLabel = 'Equipos';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return 'Inventario';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-device-tablet';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('model_id')
                ->label('Modelo')
                ->relationship('deviceModel', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => "{$record->brand->nombre} {$record->nombre}"
                ),

            TextInput::make('numero_serie')
                ->label('Número de serie')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->placeholder('ej: SN-00123'),

            TextInput::make('imei')
                ->label('IMEI')
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->placeholder('Solo para equipos con SIM')
                ->nullable(),

            Select::make('condicion')
                ->label('Condición')
                ->options([
                    'nuevo'  => 'Nuevo',
                    'usado'  => 'Usado',
                    'dañado' => 'Dañado',
                ])
                ->required()
                ->default('nuevo'),

            Select::make('disponibilidad')
                ->label('Disponibilidad')
                ->options([
                    'disponible'    => 'Disponible',
                    'asignado'      => 'Asignado',
                    'en_reparacion' => 'En reparación',
                    'dado_de_baja'  => 'Dado de baja',
                ])
                ->required()
                ->default('disponible'),

            Textarea::make('notas')
                ->label('Notas')
                ->rows(3)
                ->maxLength(500)
                ->placeholder('Observaciones sobre el equipo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deviceModel.brand.nombre')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deviceModel.nombre')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('numero_serie')
                    ->label('Serie')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Serie copiada'),

                TextColumn::make('imei')
                    ->label('IMEI')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('condicion')
                    ->label('Condición')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuevo'  => 'success',
                        'usado'  => 'warning',
                        'dañado' => 'danger',
                        default  => 'gray',
                    }),

                TextColumn::make('disponibilidad')
                    ->label('Disponibilidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disponible'    => 'success',
                        'asignado'      => 'info',
                        'en_reparacion' => 'warning',
                        'dado_de_baja'  => 'danger',
                        default         => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('condicion')
                    ->label('Condición')
                    ->options([
                        'nuevo'  => 'Nuevo',
                        'usado'  => 'Usado',
                        'dañado' => 'Dañado',
                    ]),

                SelectFilter::make('disponibilidad')
                    ->label('Disponibilidad')
                    ->options([
                        'disponible'    => 'Disponible',
                        'asignado'      => 'Asignado',
                        'en_reparacion' => 'En reparación',
                        'dado_de_baja'  => 'Dado de baja',
                    ]),

                SelectFilter::make('model_id')
                    ->label('Modelo')
                    ->relationship('deviceModel', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('No hay equipos registrados')
            ->emptyStateDescription('Registra el primer equipo para comenzar.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit'   => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}