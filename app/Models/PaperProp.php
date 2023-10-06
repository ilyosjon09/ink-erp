<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperProp extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function paperType()
    {
        return $this->belongsTo(PaperType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'paper_prop_id');
    }

    public function warehouseItem()
    {
        return $this->morphOne(WarehouseItem::class, 'association');
    }
}
