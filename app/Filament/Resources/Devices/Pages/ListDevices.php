<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Imports\DeviceImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(DeviceImporter::class)
                ->label('Importar CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info'), // Le da un color distinto al botón de crear
            CreateAction::make(),
        ];
    }
}