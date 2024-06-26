<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function operations(): HasMany
    {
        return $this->hasMany(WarehouseOperation::class, 'item_id');
    }

    public function category()
    {
        return $this->belongsTo(WarehouseItemCategory::class, 'category_id');
    }

    public function warehouseItemBatches()
    {
        return $this->hasMany(WarehouseItemBatch::class);
    }
}
