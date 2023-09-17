<?php

namespace App\Http\Livewire;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePrice;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Livewire\Component;
use Filament\Tables;
use Filament\Forms\Components;
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
        $selectStatement = <<<COLS
            `id`, 
            CONCAT('#',code, DATE_FORMAT(created_at, '-%m-%Y')) as reg_number, 
            `item_name`, 
            `tirage`, 
            (select m.name from users m where m.id = created_by) manager, 
            (select concat(( select pt.name from paper_types pt where pt.id = p.paper_type_id ),' ', p.grammage,' ', p.`size`) from paper_props p where p.id = paper_prop_id ) paper, 
            `print_type`, 
            (tirage * amount_per_paper) amount, 
            (SELECT json_arrayagg(s.name) FROM services s where s.id in ( select sp.service_id from order_service_price op left JOIN service_prices sp on op.service_price_id = sp.id where op.order_id = orders.id)) services, 
            item_image,
            `printed_at`, 
            `created_at`
        COLS;
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
                    'item_image' => $record->item_image,
                    'services' => $record->servicePrices()
                        ->get()
                        ->mapWithKeys(fn ($servicePrice, $key) => [$servicePrice->id => $servicePrice->pivot->completed])
                        ->toArray(),
                    'printing_forms' => $record->printingForms()
                        ->get()
                        ->mapWithKeys(fn ($printingForm, $key) => [$printingForm->id => $printingForm->pivot->completed])
                        ->toArray(),
                ]))
                ->action(function (): void {
                    return;
                })
                ->form([
                    Grid::make(2)->schema([
                        Card::make([
                            Placeholder::make('Рег. номер')->content(fn (Order $record) => $record->reg_number),
                            Placeholder::make('Создан')->content(fn (Order $record) => $record->created_at->format('d.m.Y')),
                            Placeholder::make('Название')->content(fn (Order $record) => $record->item_name)->columnSpanFull(),
                            Placeholder::make('Менеджер')->content(fn (Order $record) => $record->manager),
                            Placeholder::make('Напечатен')->content(fn (Order $record) => $record->printed_at->format('d.m.Y')),
                        ])
                            ->inlineLabel()
                            ->columnSpan(1),
                        Card::make([
                            ViewField::make('item_image')
                                ->label(__('Изображение'))
                                ->view('image')
                        ])->columnSpan(1),
                    ]),
                    Grid::make(2)->schema([
                        Fieldset::make('services')
                            ->label(__('Статус услуги'))
                            ->columnSpan(1)
                            ->columns(1)
                            ->childComponents(function (Order $record) {
                                return $record->servicePrices()
                                    ->with('service:id,name')
                                    ->get()
                                    ->map(
                                        fn ($servicePrice) => Toggle::make('services.' . $servicePrice->id)
                                            ->label(__($servicePrice->service->name))
                                            ->onIcon('heroicon-o-check')
                                            ->offIcon('heroicon-o-x')
                                            ->hint(fn ($state) => $state ? __('Готово') : __('Не готово'))
                                            ->hintColor(fn ($state) =>  $state ? 'success' : 'secondary')
                                            ->afterStateUpdated(function (?Order $record, Components\Component $component, $state) {
                                                $id = (int)collect(explode('.', $component->getId()))->last();
                                                $record->servicePrices()->updateExistingPivot($id, ['completed' => (bool) $state]);
                                            })
                                            ->reactive()
                                    )
                                    ->toArray();
                            }),
                        Fieldset::make('printing_forms')
                            ->hidden(fn (Order $record) => $record->printingForms()->get()->isEmpty())
                            ->label(__('Печатные формы'))
                            ->columnSpan(1)
                            ->columns(1)
                            ->childComponents(function (Order $record) {
                                return $record->printingForms()
                                    ->get()
                                    ->map(
                                        fn ($printingForm) => Toggle::make('printing_forms.' . $printingForm->id)
                                            ->label(__($printingForm->name))
                                            ->onIcon('heroicon-o-check')
                                            ->offIcon('heroicon-o-x')
                                            ->hint(fn ($state) => $state ? __('Готово') : __('Не готово'))
                                            ->hintColor(fn ($state) =>  $state ? 'success' : 'secondary')
                                            ->afterStateUpdated(function (?Order $record, Components\Component $component, $state) {
                                                $id = (int)collect(explode('.', $component->getId()))->last();
                                                $record->printingForms()->updateExistingPivot($id, ['completed' => (bool) $state]);
                                            })
                                            ->reactive()
                                    )
                                    ->toArray();
                            }),
                    ])
                ])
                ->modalButton(__('OK'))
                ->icon('heroicon-o-eye'),
            Action::make('done')
                ->label(__('Готово'))
                ->action(function (Order $record) {
                    $record->load(['servicePrices', 'printingForms']);
                    $printingForms = $record->printingForms->pluck('pivot.completed');
                    $servicePrices = $record->servicePrices->pluck('pivot.completed');

                    if (!$servicePrices->merge($printingForms)->every(fn ($item) => $item)) {
                        return;
                    }

                    $record->update(['status' => OrderStatus::COMPLETED, 'processed_at' => now()]);
                    $record->save();
                    Notification::make()
                        ->title(__('Заказ помечен как готовый'))
                        ->success()
                        ->send();
                })
                ->disabled(function (Order $record) {
                    $record->load(['servicePrices', 'printingForms']);
                    $printingForms = $record->printingForms->pluck('pivot.completed');
                    $servicePrices = $record->servicePrices->pluck('pivot.completed');

                    return !$servicePrices->merge($printingForms)->every(fn ($item) => $item);
                })
                ->button()
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
