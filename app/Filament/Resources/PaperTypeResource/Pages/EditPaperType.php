<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaperType extends EditRecord
{
    protected static string $resource = PaperTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
