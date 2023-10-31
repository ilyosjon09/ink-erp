<?php

namespace App\Models;

use App\Enums\OperationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOperation extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'operation' => OperationType::class,
    ];

    public function batch()
    {
        return $this->belongsTo(WarehouseItemBatch::class, 'warehouse_item_batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
