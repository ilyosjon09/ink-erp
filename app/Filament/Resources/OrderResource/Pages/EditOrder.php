<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\PaperProp;
use App\Models\PrintingForm;
use App\Models\ServicePrice;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $paperProp = PaperProp::query()->find($data['size']);

        $data = [
            'item_name' => $data['item_name'],
            'client_id' => $data['client_id'],
            'paper_prop_id' => $paperProp->id,
            'paper_price' => $paperProp->price,
            'amount' => $data['order_amount'],
            'amount_per_paper' => $data['amount_per_paper'],
            'print_type' => $data['print_type'],
            'tirage' => $data['tirage'],
            'item_image' => $data['item_image'],
            'additional_tirage' => $data['additional_tirage'],
            'profit_percentage_id' => $data['profit_percentage'],
        ];

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var \App\Models\Order $order */
        $order = $this->record;
        $servicePrices = ServicePrice::query()
            ->whereIn('service_id', $this->data['services'])
            ->where('print_type', $this->data['print_type'])
            ->when(
                $this->data['tirage'] >= 1000,
                fn ($query) => $query->select('id', 'price_after_1k as price'),
                fn ($query) => $query->select('id', 'price_before_1k as price')
            )->get();

        $sd = [];

        $servicePrices->each(function ($service) use (&$sd) {
            $sd[$service->id] = ['price' => $service->price];
        });

        $order->servicePrices()->sync($sd);

        $printingForms = $this->data['printing_forms'];
        $printingForms[] = $this->data['cutter'];

        $printingForms = PrintingForm::query()
            ->whereIn('id', $printingForms)
            ->when(
                $this->data['print_type'] == '4+0',
                fn ($query) => $query->select('id', 'four_zero_price as price'),
                fn ($query) => $query->select('id', 'double_four_price as price')
            )->get();

        $pd = [];
        $printingForms = $printingForms->each(function ($printingForm) use (&$pd) {
            $pd[$printingForm->id] = [
                'price' => $printingForm->price,
            ];
        });

        $order->printingForms()->sync($pd);
    }
}
