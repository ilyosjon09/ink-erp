<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrdersOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $selectStatement = "`id`, CONCAT('#',code, DATE_FORMAT(`created_at`, '-%m-%Y')) as reg_number, `item_name`, `client_id`, `amount_per_paper`, `paper_prop_id`, `print_type`, `tirage`, `item_image`, `additional_tirage`, `created_by`, `created_at`, `status`, `updated_at`";
        return parent::getTableQuery()->withoutGlobalScopes()
            ->selectRaw($selectStatement);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrdersOverview::class,
        ];
    }
}
