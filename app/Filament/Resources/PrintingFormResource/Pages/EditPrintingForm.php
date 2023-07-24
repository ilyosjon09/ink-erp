<?php

namespace App\Filament\Resources\PrintingFormResource\Pages;

use App\Filament\Resources\PrintingFormResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintingForm extends EditRecord
{
    protected static string $resource = PrintingFormResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
