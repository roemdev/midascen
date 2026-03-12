<?php

namespace App\Filament\Resources\Recipients;

use App\Filament\Resources\Recipients\Pages;
use App\Models\Recipient;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class RecipientResource extends Resource
{
    protected static ?string $model = Recipient::class;
    protected static ?string $navigationLabel = 'Ejecutivos';
    protected static ?string $modelLabel = 'Ejecutivo';
    protected static ?string $pluralModelLabel = 'Ejecutivos';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return 'Personal';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre completo')
                ->required()
                ->maxLength(255)
                ->placeholder('ej: Carlos Pérez'),

            TextInput::make('departamento')
                ->label('Departamento')
                ->required()
                ->maxLength(150)
                ->placeholder('ej: Tecnología, Ventas, Operaciones'),

            TextInput::make('cargo')
                ->label('Cargo')
                ->required()
                ->maxLength(150)
                ->placeholder('ej: Gerente de TI'),

            TextInput::make('supervisor')
                ->label('Supervisor')
                ->required()
                ->maxLength(255)
                ->placeholder('Nombre del supervisor directo'),

            Toggle::make('activo')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('departamento')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cargo')
                    ->label('Cargo')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('supervisor')
                    ->label('Supervisor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->placeholder('Todos'),
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
            ->emptyStateHeading('No hay ejecutivos registrados')
            ->emptyStateDescription('Agrega el primer ejecutivo para comenzar.')
            ->defaultSort('nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecipients::route('/'),
            'create' => Pages\CreateRecipient::route('/create'),
            'edit'   => Pages\EditRecipient::route('/{record}/edit'),
        ];
    }
}