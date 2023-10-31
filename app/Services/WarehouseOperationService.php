<?php

namespace App\Services;

use App\Enums\OperationType;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemBatch;
use App\Models\WarehouseOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WarehouseOperationService
{
    public function stockIn(int $itemId, int $quantity, int $price, $createdAt)
    {
        /** @var Builder $batchesQuery */
        $batchesQuery = WarehouseItemBatch::query()->whereRaw('warehouse_item_id = ? and (in_quantity - out_quantity) > 0', [$itemId]);
        /** @var WarehouseItemBatch $currentBatch */
        $currentBatch = $batchesQuery->whereRaw('(in_quantity - out_quantity) > 0 and in_price = ?', [$price])->orderBy('created_at', 'asc')->first();

        if ($batchesQuery->getQuery()->count() < 1 || is_null($currentBatch)) {
            $batch = WarehouseItemBatch::query()->create([
                'warehouse_item_id' => $itemId,
                'in_quantity' => $quantity,
                'in_price' => $price,
                'out_quantity' => 0,
            ]);

            WarehouseOperation::query()
                ->create([
                    'warehouse_item_batch_id' => $batch->id,
                    'oper_day_id' => operday()->id,
                    'operation' => OperationType::ADD,
                    'amount' => $quantity,
                    'price' => $price,
                    'created_at' => $createdAt,
                    'created_by' => auth()->id()
                ]);

            return;
        }

        $currentBatch->in_quantity += $quantity;
        $currentBatch->save();


        WarehouseOperation::query()
            ->create([
                'warehouse_item_batch_id' => $currentBatch->id,
                'oper_day_id' => operday()->id,
                'operation' => OperationType::ADD,
                'amount' => $quantity,
                'price' => $price,
                'created_at' => $createdAt,
                'created_by' => auth()->id()
            ]);

        return;
    }

    public function stockOut($itemId, $quantity, $price, $createdAt)
    {
        /** @var Builder $batchesQuery */
        $batchesQuery = WarehouseItemBatch::query()->whereRaw('warehouse_item_id = ? and (in_quantity - out_quantity) > 0', [$itemId]);
        /** @var Collection $batches */
        $batches = $batchesQuery->orderBy('created_at', 'asc')->get();
        $qty = $quantity;
        $batches->each(function ($batch) use (&$qty) {
            if ($qty === 0) {
                return;
            }
            if ($batch->in_quantity > ($batch->out_quantity + $qty)) {
                $batch->out_quantity += $qty;
                $batch->save();
                $qty = 0;

                return;
            }
        });
        WarehouseOperation::query()
            ->create([
                'warehouse_item_batch_id' => $currentBatch->id,
                'oper_day_id' => operday()->id,
                'operation' => OperationType::ADD,
                'amount' => $quantity,
                'price' => $price,
                'created_at' => $createdAt,
                'created_by' => auth()->id()
            ]);
    }
}
