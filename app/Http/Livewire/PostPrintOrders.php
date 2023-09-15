<?php

namespace App\Http\Livewire;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePrice;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class PostPrintOrders extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;


    public function render()
    {
        return view('livewire.post-print-orders');
    }


    protected function getTableQuery(): Builder|Relation
    {
        $selectStatement = "`id`, CONCAT('#',code, DATE_FORMAT(created_at, '-%m-%Y')) as reg_number, `item_name`, tirage, (select m.name from users m where m.id = created_by) manager, ( select concat( ( select pt.name from paper_types pt where pt.id = p.paper_type_id ),' ', p.grammage,' ', p.`size`) from paper_props p where p.id = paper_prop_id ) paper, print_type, (tirage * amount_per_paper) amount, (SELECT json_arrayagg(s.name) FROM services s where s.id in ( select sp.service_id from order_service_price op left JOIN service_prices sp on op.service_price_id = sp.id where op.order_id = orders.id)) services, item_image";
        return Order::query()
            ->selectRaw($selectStatement)
            ->where('status', OrderStatus::IN_ASSEMPLY_SHOP)
            ->withCasts(['services' => 'array']);
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
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('details')
                ->label(__('Детали'))
                ->mountUsing(fn (ComponentContainer $form, Order $record) => $form->fill([
                    'services' => Service::query()->get()->pluck('name', 'id'),
                ]))
                ->action(function (Order $record, array $data): void {
                    dd($data);
                })
                ->form([
                    CheckboxList::make('services')
                ])->button()->icon('heroicon-o-eye'),
        ];
    }
}
