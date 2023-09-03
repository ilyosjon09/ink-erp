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
                Order::query()->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count()
            )
                // ->chart(
                //     Order::query()->selectRaw('count(created_at) o')->whereRaw('year(created_at) = year(current_date()) and month(created_at) = month(CURRENT_DATE())')->groupByRaw('day(created_at)')->get()->pluck('o')->toArray()
                // )
                ->color('success'),
            Card::make(
                __('Новые заказы'),
                Order::query()
                    ->where('status', OrderStatus::NEW)
                    ->count()
            ),
            Card::make(
                __('Выполняемые заказы'),
                Order::query()->whereNot('status', value: OrderStatus::NEW)->count()
            ),
            Card::make(
                __('Готовые заказы'),
                Order::query()->where('status', value: OrderStatus::COMPLETED)->count()
            ),
        ];
    }
}
