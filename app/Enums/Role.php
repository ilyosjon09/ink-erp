<?php

namespace App\Enums;

enum Role: string
{
    case MANAGER = 'Менеджер';
    case PRINT_SHOP = 'Печать';
    case POST_PRINT_SHOP = 'Цех';
    case ACCOUNTANT = 'Бухгалтер';
    case SUPERADMIN = 'Суперадмин';
}
