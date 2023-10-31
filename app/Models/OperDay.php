<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperDay extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'operday' => 'date',
        'closed' => 'boolean'
    ];

    public function warehouseOperations(): HasMany
    {
        return $this->hasMany(WarehouseOperation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cashOfficeOperations(): HasMany
    {
        return $this->hasMany(CashOfficeOperation::class);
    }

    public function current()
    {
        return $this->where('is_current', true)->first();
    }
}
