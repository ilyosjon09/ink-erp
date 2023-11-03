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

    public function getRemainingStock($itemId): int
    {
        return WarehouseItemBatch::query()->selectRaw('IFNULL(SUM(in_quantity) - SUM(out_quantity), 0) remaining_stock')->where('warehouse_item_id', $itemId)->first()->remaining_stock;
    }

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

        /**
         *  Determine a batch to stock-out from
         *  - Check if batch exists for given item
         *  - if there is no batch notify that there is no item to stock out and exit
         *  - if batches exist then select the oldest batch that (in stock - out stock) not equal to 0
         *  - if there is no such notify that there is no item to stock out and exit
         *  - else check if (out stock + quantity) is greater than selected batch
         *  - if yes then add quantity value to out stock value then exit
         *  - if no then calculate how much needed to make selected batch in stock - out stock = 0 then add it to out stock of it
         *  - add remaining quantity to next batches until quantity becomes 0
         *  
         */
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
        // WarehouseOperation::query()
        //     ->create([
        //         'warehouse_item_batch_id' => $currentBatch->id,
        //         'oper_day_id' => operday()->id,
        //         'operation' => OperationType::ADD,
        //         'amount' => $quantity,
        //         'price' => $price,
        //         'created_at' => $createdAt,
        //         'created_by' => auth()->id()
        //     ]);
    }
}
