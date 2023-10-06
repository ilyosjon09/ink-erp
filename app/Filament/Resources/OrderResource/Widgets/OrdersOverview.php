<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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
                __('В процессе'),
                function () {
                    $printing = Order::query()
                        ->where('status', OrderStatus::IN_PRINTING_SHOP)
                        ->count();
                    $assembling = Order::query()
                        ->where('status', OrderStatus::IN_ASSEMPLY_SHOP)
                        ->count();
                    return new HtmlString("<span class=\"text-xl flex space-x-4 items-center\"><span>Печ.: {$printing}</span><span>Цех: {$assembling}</span></span>");
                }
            ),
            Card::make(
                __('Готовые заказы'),
                Order::query()
                    ->where('status',  OrderStatus::COMPLETED)
                    ->count()
            )->color('success'),
        ];
    }
}
