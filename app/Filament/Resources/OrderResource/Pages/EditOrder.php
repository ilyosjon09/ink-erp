<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\PaperProp;
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
        $paperProp = PaperProp::find($this->record->paper_prop_id);
        $data['reg_number'] = $this->record->code . $this->record->created_at->format('-m-Y');
        $data['services'] = $this->record->servicePrices->pluck('service_id');
        $data['print_type'] = $this->record->print_type;
        $data['paper_type'] = $paperProp->paper_type_id;
        $data['grammage'] = $paperProp->grammage;
        $data['size'] = $paperProp->id;
        $data['order_amount'] = $this->record->amount;
        $data['profit_percentage'] = $this->record->profit_percentage_id;

        $data['total_amount'] = $this->record->tirage * $this->record->amount_per_paper;
        $data['tirage_forecast'] = floor((float)$this->record->amount / (float)$this->record->amount_per_paper);
        $data['total_tirage'] = $this->record->tirage + $this->record->additional_tirage;

        $printingForms = $this->record->printingForms()
            ->whereNot('name', 'like', '%Пичок%')
            ->get();
        $data['printing_forms'] = $printingForms->count() > 0 ? $printingForms->pluck('pivot.printing_form_id') : null;
        $cutterId = $this->record->printingForms()->where('name', 'like', '%Пичок%')->get();
        $data['cutter'] =  $cutterId?->first()?->id;
        return $data;
    }
}
