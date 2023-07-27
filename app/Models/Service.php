<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
