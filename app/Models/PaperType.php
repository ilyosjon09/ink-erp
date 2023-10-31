<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function properties()
    {
        return $this->hasMany(PaperProp::class);
    }

    public function warehouseItemCategory()
    {
        return $this->belongsTo(WarehouseItemCategory::class, 'warehouse_item_category_id');
    }
}
