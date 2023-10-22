<?php

namespace App\Filament\Resources\CashOfficeAccountResource\Pages;

use App\Filament\Resources\CashOfficeAccountResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashOfficeAccounts extends ListRecords
{
    protected static string $resource = CashOfficeAccountResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
