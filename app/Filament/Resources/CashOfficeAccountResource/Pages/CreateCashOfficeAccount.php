<?php

namespace App\Filament\Resources\CashOfficeAccountResource\Pages;

use App\Filament\Resources\CashOfficeAccountResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCashOfficeAccount extends CreateRecord
{
    protected static string $resource = CashOfficeAccountResource::class;
}
