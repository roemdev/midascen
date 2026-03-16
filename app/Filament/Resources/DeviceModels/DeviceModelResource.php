<?php

namespace App\Filament\Resources\DeviceModels;

use App\Filament\Resources\DeviceModels\Pages;
use App\Models\DeviceModel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DeviceModelResource extends Resource
{
    protected static ?string $model = DeviceModel::class;
    protected static ?string $navigationLabel = "Modelos";
    protected static ?string $modelLabel = "Modelo";
    protected static ?string $pluralModelLabel = "Modelos";
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return "Catálogos";
    }

    public static function getNavigationIcon(): string
    {
        return "heroicon-o-cpu-chip";
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("brand_id")
                ->label("Marca")
                ->relationship("brand", "nombre")
                ->searchable()
                ->preload()
                ->required(),

            Select::make("category_id")
                ->label("Categoría")
                ->relationship("category", "nombre")
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make("nombre")
                ->label("Nombre del modelo")
                ->required()
                ->maxLength(150)
                ->placeholder("ej: T300, V2s, ThinkPad X1"),

            Textarea::make("descripcion")
                ->label("Descripción")
                ->rows(3)
                ->maxLength(500)
                ->placeholder("Especificaciones o notas del modelo"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")->label("ID")->sortable()->searchable(),

                TextColumn::make("brand.nombre")
                    ->label("Marca")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("category.nombre")
                    ->label("Categoría")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("nombre")
                    ->label("Modelo")
                    ->searchable()
                    ->sortable(),

                TextColumn::make("devices_count")
                    ->label("Equipos")
                    ->counts("devices")
                    ->sortable(),

                TextColumn::make("descripcion")
                    ->label("Descripción")
                    ->limit(40)
                    ->placeholder("Sin descripción")
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make("created_at")
                    ->label("Creado")
                    ->dateTime("d/m/Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make()->label("Editar"),
                DeleteAction::make()->label("Eliminar"),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label("Eliminar seleccionados"),
                ]),
            ])
            ->emptyStateHeading("No hay modelos")
            ->emptyStateDescription(
                "Primero crea marcas y categorías, luego agrega modelos.",
            )
            ->defaultSort("brand_id");
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListDeviceModels::route("/"),
            "create" => Pages\CreateDeviceModel::route("/create"),
            "edit" => Pages\EditDeviceModel::route("/{record}/edit"),
        ];
    }
}
