<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['services'] = $this->record->servicePrices->pluck('service_id');
        $data['print_type'] = $this->record->printing_method;
        $data['printing_forms'] =  $this->record->printingForms()
            ->whereNot('name', 'like', '%Пичок%')
            ->get()
            ->pluck('pivot.printing_form_id');
        // $data['cutter'] =  $this->record->printingForms()->where('name', 'like', '%Пичок%')->get()->pluck('pivot.printing_form_id', 'pivot.price');
        return $data;
    }
}
