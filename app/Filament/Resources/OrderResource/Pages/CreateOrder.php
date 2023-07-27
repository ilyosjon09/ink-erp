<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        
        $today = today();
        $lastOrder = Order::whereYear('order_date', $today->year)->whereMonth('order_date', $today->month)->latest('code')->first();
        $data['code'] = $lastOrder ? $lastOrder->code++ : 1;
        $data['order_date'] = $today;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        dd($data);
        return static::getModel()::create($data);
    }
}
