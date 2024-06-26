<?php

namespace App\Http\Livewire\PrintingShop;

use App\Enums\OrderStatus;
use App\Enums\OperationType;
use App\Models\Order;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemCategory;
use App\Models\WarehouseOperation;
use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class Orders extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.printing-shop.orders');
    }

    protected function getTableQuery(): Builder|Relation
    {
        $selectStatement = "`id`, CONCAT('#',code, DATE_FORMAT(created_at, '-%m-%Y')) as reg_number, `item_name`, tirage, (select m.name from users m where m.id = created_by) manager, ( select concat( ( select pt.name from paper_types pt where pt.id = p.paper_type_id ),' ', p.grammage,' ', p.`size`) from paper_props p where p.id = paper_prop_id ) paper, print_type, (tirage * amount_per_paper) amount, (SELECT json_arrayagg(s.name) FROM services s where s.id in ( select sp.service_id from order_service_price op left JOIN service_prices sp on op.service_price_id = sp.id where op.order_id = orders.id)) services, item_image";
        return Order::query()
            ->selectRaw($selectStatement)
            ->where('status', OrderStatus::IN_PRINTING_SHOP)
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
            IconColumn::make('image_preview')->label(__('Фото'))
                ->options(['heroicon-o-photograph'])->action(function (Order $record): void {
                    $this->dispatchBrowserEvent('open-image-preview-modal', [
                        'url' => asset('storage/' . $record->item_image),
                    ]);
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('mark as done')
                ->label(__('Готово'))
                ->button()
                ->action(function (?Order $record) {

                    $record->load('paperProperties');
                    $record->refresh();
                    $category = WarehouseItemCategory::query()->with('items')->where('paper_type_id', $record->paperProperties->paperType->id)->first();

                    $item = $category->items->where('grammage', $record->paperProperties->grammage)->first();
                    // $warehouseItem =  WarehouseOperation::query()->where('item_id', $item->id)->latest()->first();
                    $price = $record->paperProperties->price;
                    WarehouseOperation::query()->create([
                        'item_id' => $item->id,
                        'operation' => OperationType::SUBTRACT,
                        'amount' => ceil(((int)$record->tirage + (int)$record->additional_tirage) / $record->paperProperties->divided_into),
                        'price' => $price,
                        'comment' => 'Из печатная',
                        'created_by' => auth()->user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $record->update([
                        'status' => OrderStatus::IN_ASSEMPLY_SHOP,
                        'printed_by' => Auth::user()->id,
                        'printed_at' => now(),
                    ]);
                    Notification::make()
                        ->title(__('Заказ был напечатан'))
                        ->success()
                        ->send();
                })->requiresConfirmation()
                ->icon('heroicon-o-check'),
        ];
    }
}
