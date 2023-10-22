<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOfficeOperation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cashOfficeOperationTemplate()
    {
        return $this->belongsTo(CashOfficeOperationTemplate::class);
    }

    public function cashOfficeAccount()
    {
        return $this->belongsTo(CashOfficeAccount::class);
    }
}
