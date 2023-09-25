<?php

namespace App\Filament\Resources\WarehouseItemCategoryResource\Pages;

use App\Filament\Resources\WarehouseItemCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseItemCategories extends ListRecords
{
    protected static string $resource = WarehouseItemCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
