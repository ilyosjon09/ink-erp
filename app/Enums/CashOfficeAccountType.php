<?php

namespace App\Enums;

enum CashOfficeAccountType: int
{
    case CASH = 0;
    case CARD = 1;
    case BANK_ACCOUNT = 2;
    case CLIENT_ACCOUNT = 3;

    public function label(): string
    {
        return match ($this) {
            self::CASH => __('💵 Наличные'),
            self::CARD => __('💳 Пластиковая карта'),
            self::BANK_ACCOUNT => __('🏦 Банковский счет'),
            self::CLIENT_ACCOUNT => __('✨ Счет клиента'),
        };
    }
}
