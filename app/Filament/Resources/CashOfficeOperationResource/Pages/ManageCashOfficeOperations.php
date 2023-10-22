<?php

namespace App\Filament\Resources\CashOfficeOperationResource\Pages;

use App\Filament\Resources\CashOfficeOperationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCashOfficeOperations extends ManageRecords
{
    protected static string $resource = CashOfficeOperationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
