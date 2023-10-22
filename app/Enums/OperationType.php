<?php

namespace App\Enums;

enum OperationType: int
{
    case ADD = 0;
    case SUBTRACT = 1;

    public function label(): string
    {
        return match ($this) {
            self::ADD => __('Приход'),
            self::SUBTRACT => __('Расход'),
        };
    }
}
