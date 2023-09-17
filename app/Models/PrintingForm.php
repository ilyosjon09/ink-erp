<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingForm extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot(['completed'])->withTimestamps();
    }
}
