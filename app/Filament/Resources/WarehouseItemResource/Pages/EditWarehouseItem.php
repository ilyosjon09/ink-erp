<?php

namespace App\Filament\Resources\WarehouseItemResource\Pages;

use App\Filament\Resources\OrderResource\Widgets\OrdersOverview;
use App\Filament\Resources\WarehouseItemResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWarehouseItem extends EditRecord
{
    protected static string $resource = WarehouseItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
