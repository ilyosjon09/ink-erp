<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderPrintingForm extends Pivot
{
    protected $casts = [
        'completed' => 'boolean'
    ];
}
