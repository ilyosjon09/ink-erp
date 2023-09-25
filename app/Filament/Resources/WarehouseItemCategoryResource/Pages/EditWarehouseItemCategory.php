<?php

namespace App\Filament\Resources\WarehouseItemCategoryResource\Pages;

use App\Filament\Resources\WarehouseItemCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseItemCategory extends EditRecord
{
    protected static string $resource = WarehouseItemCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
