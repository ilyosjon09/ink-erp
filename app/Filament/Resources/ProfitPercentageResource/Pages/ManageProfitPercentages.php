<?php

namespace App\Filament\Resources\ProfitPercentageResource\Pages;

use App\Filament\Resources\ProfitPercentageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProfitPercentages extends ManageRecords
{
    protected static string $resource = ProfitPercentageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('xl'),
        ];
    }
}
