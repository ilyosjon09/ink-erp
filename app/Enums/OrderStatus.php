<?php

namespace App\Enums;

enum OrderStatus: int
{
    case NEW = 0;
    case IN_PRINTING_SHOP = 1;
    case IN_ASSEMPLY_SHOP = 2;
    case COMPLETED = 3;
    case CANCELED = 4;

    public function label(): string
    {
        return match ($this) {
            OrderStatus::NEW => __('Новый'),
            OrderStatus::IN_PRINTING_SHOP => __('В печать'),
            OrderStatus::IN_ASSEMPLY_SHOP => __('В цех'),
            OrderStatus::COMPLETED => __('Готово'),
            OrderStatus::CANCELED => __('Отменен'),
        };
    }

    public static function colors(): array
    {
        return [
            'secondary',
            'primary' => OrderStatus::NEW->value,
            'success' => OrderStatus::COMPLETED->value,
        ];
    }
}
