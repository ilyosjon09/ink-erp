<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseItemBatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class);
    }

    public function operations()
    {
        return $this->hasMany(WarehouseOperation::class, 'warehouse_item_batch_id');
    }
}
