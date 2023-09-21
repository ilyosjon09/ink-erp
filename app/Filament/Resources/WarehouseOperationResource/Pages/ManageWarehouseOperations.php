<?php

namespace App\Filament\Resources\WarehouseOperationResource\Pages;

use App\Filament\Resources\WarehouseOperationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWarehouseOperations extends ManageRecords
{
    protected static string $resource = WarehouseOperationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
