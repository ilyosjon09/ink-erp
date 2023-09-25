<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseItemCategory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'for_paper' => 'boolean',
    ];

    public function paperType()
    {
        return $this->belongsTo(PaperType::class);
    }

    public function items()
    {
        return $this->hasMany(WarehouseItem::class, 'category_id');
    }
}
