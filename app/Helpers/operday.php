<?php

use App\Models\OperDay;

if (!function_exists('operday')) {
    function operday(): OperDay
    {
        return app('App\Models\OperDay')->current();
    }
}
