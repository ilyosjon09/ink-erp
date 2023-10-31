<?php

namespace App\Services;

use App\Enums\OperationType;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemBatch;
use App\Models\WarehouseOperation;

class WarehouseOperationService
{
    public function stockIn(int $itemId, int $quantity, int $price)
    {
        $batchExists = WarehouseItemBatch::query()->where('warehouse_item_id', $itemId)->whereRaw('(in_quantity - out_quantity) > 0')->exists();

        if (!$batchExists) {
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
                ]);

            return;
        }
    }

    public function stockOut()
    {
    }
}
