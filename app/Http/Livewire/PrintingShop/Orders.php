<?php

namespace App\Http\Livewire\PrintingShop;

use App\Models\Order;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Orders extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.printing-shop.orders');
    }

    protected function getTableQuery(): Builder|Relation
    {
        return Order::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('item_name'),
        ];
    }
}
