<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\PaperProp;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('accept-order')
                ->label(__('Отправить на печать'))
                ->hidden($this->record->status != OrderStatus::NEW)
                ->action(function () {
                    $this->record->status = OrderStatus::IN_PRINTING_SHOP;
                    $this->record->save();
                    Notification::make()
                        ->title(__('Заказ успешно отправлен в печать'))
                        ->success()
                        ->send();
                })->requiresConfirmation()
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
