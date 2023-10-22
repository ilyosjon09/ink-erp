<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\PaperProp;
use App\Models\PrintingForm;
use App\Models\ServicePrice;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $today = today();
        $lastOrder = Order::whereYear('created_at', $today->year)->whereMonth('created_at', $today->month)->latest('code')->first();
        $data['code'] = $lastOrder ? (int)$lastOrder->code + 1 : 1;
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data = [
            'code' => $data['code'],
            'item_name' => $data['item_name'],
            'client_id' => $data['client_id'],
            'paper_prop_id' => $data['size'],
            'amount_per_paper' => $data['amount_per_paper'],
            'amount' => $data['order_amount'],
            'print_type' => $data['print_type'],
            'agreed_price' => $data['agreed_price'],
            'paper_price' => PaperProp::find($data['size'])->price,
            'tirage' => $data['tirage'],
            'profit_percentage_id' => $data['profit_percentage'],
            'additional_tirage' => $data['additional_tirage'],
            'created_by' => auth()->user()->id,
            'status' => OrderStatus::NEW->value,
            'item_image' => $data['item_image'],
        ];
        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
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
