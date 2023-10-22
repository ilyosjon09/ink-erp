<?php

namespace App\Filament\Resources\CashOfficeAccountResource\Pages;

use App\Filament\Resources\CashOfficeAccountResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashOfficeAccount extends EditRecord
{
    protected static string $resource = CashOfficeAccountResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
