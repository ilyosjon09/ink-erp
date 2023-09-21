<?php

namespace App\Models;

use App\Enums\WarehouseOperationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOperation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'operation' => WarehouseOperationType::class,
    ];

    public function item()
    {
        return $this->belongsTo(WarehouseItem::class, 'item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
