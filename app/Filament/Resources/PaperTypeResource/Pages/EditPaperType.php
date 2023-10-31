<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use App\Models\PaperType;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemCategory;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPaperType extends EditRecord
{
    protected static string $resource = PaperTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('link-to-warehouse')
                ->label(__('Связать со складом'))
                ->requiresConfirmation(true)
                ->action(function () {
                    /** @var App\Models\PaperType $record */
                    $record = $this->record;
                    $record->refresh();
                    $record->load('properties');

                    $category = WarehouseItemCategory::query()->create([
                        'name' => $record->name,
                        'for_paper' => true,
                    ]);

                    $record->update(['warehouse_item_category_id' => $category->id]);
                    $code = $record->id * 100;
                    DB::beginTransaction();
                    $record->properties->groupBy('grammage')->each(function ($size, $grammage) use ($category, &$code) {
                        $item = WarehouseItem::query()
                            ->create([
                                'category_id' => $category->id,
                                'code' => $code++,
                                'name' => $grammage . 'гр.',
                                'measurement_unit' => 'шт.',
                                'for_paper' => true,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]);
                        $size->each(function ($paperSize) use ($item) {
                            $paperSize->update([
                                'warehouse_item_id' => $item->id
                            ]);
                        });
                    });
                    DB::commit();
                    Notification::make('paper-linked-to-warehouse-category')
                        ->success()
                        ->body(__('Тип бумаги успешно привязан к категории склада'))
                        ->send();
                })
                ->visible(is_null($this->record->warehouse_item_category_id)),
            Actions\DeleteAction::make(),
        ];
    }
}
