<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOfficeOperationTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cashOfficeAccount()
    {
        return $this->belongsTo(CashOfficeAccount::class);
    }
}
