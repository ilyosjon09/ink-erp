<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPaperTypes extends ListRecords
{
    protected static string $resource = PaperTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->withoutGlobalScopes()->withCount('properties');
    }
}
