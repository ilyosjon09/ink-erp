<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class OrdersOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make(
                __('Заказы за месяц'),
                Order::query()
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->count()
            )
                ->color('success'),
            Card::make(
                __('Новые заказы'),
                Order::query()
                    ->where('status', OrderStatus::NEW)
                    ->count()
            ),
            Card::make(
                __('Печатаемые заказы'),
                Order::query()
                    ->where('status', OrderStatus::IN_PRINTING_SHOP)
                    ->count()
            ),
            Card::make(
                __('Заказы в цех'),
                Order::query()
                    ->where('status', OrderStatus::IN_ASSEMPLY_SHOP)
                    ->count()
            ),
            Card::make(
                __('Готовые заказы'),
                Order::query()
                    ->where('status',  OrderStatus::COMPLETED)
                    ->count()
            )
                ->color('success'),
        ];
    }
}
