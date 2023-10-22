<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOfficeAccount extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function balances()
    {
        return $this->hasMany(CashOfficeAccountBalance::class);
    }

    public function operations()
    {
        return $this->hasMany(CashOfficeOperation::class);
    }
}
