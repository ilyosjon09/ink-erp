<?php

namespace App\Http\Livewire\PrintingShop;

use App\Models\Order;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
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
        $selectStatement = "`id`, CONCAT('#',code, DATE_FORMAT(created_at, '-%m-%Y')) as reg_number, `item_name`, tirage, (select m.name from users m where m.id = created_by) manager, ( select concat( ( select pt.name from paper_types pt where pt.id = p.paper_type_id ),' ', p.grammage,' ', p.`size`) from paper_props p where p.id = paper_prop_id ) paper, print_type, (tirage * amount_per_paper) amount, (SELECT json_arrayagg(s.name) FROM services s where s.id in ( select sp.service_id from order_service_price op left JOIN service_prices sp on op.service_price_id = sp.id where op.order_id = id)) services, item_image";
        return Order::query()->selectRaw($selectStatement)->withCasts(['services' => 'array']);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('reg_number')->label(__('Номер заказа')),
            TextColumn::make('manager')->label(__('Менеджер')),
            TextColumn::make('item_name')->label(__('Наименование')),
            TextColumn::make('paper')->label(__('Вид бумаги')),
            TextColumn::make('print_type')->label(__('Краска')),
            TextColumn::make('tirage')->label(__('Тираж')),
            TextColumn::make('amount')->label(__('Штук')),
            TagsColumn::make('services')->label(__('Услуги')),
            ImageColumn::make('item_image')->label(__('Фото'))->square()
        ];
    }
}
