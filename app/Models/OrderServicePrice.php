<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderServicePrice extends Pivot
{
    protected $casts = [
        'completed' => 'boolean',
    ];
}
