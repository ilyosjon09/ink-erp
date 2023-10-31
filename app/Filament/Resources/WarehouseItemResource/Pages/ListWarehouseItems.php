<?php

namespace App\Filament\Resources\WarehouseItemResource\Pages;

use App\Filament\Resources\WarehouseItemResource;
use App\Models\WarehouseOperation;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListWarehouseItems extends ListRecords
{
    protected static string $resource = WarehouseItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // $balanceQuery = <<<SQL
        //     coalesce(SUM( CASE WHEN o.operation = 0 THEN o.amount ELSE 0 END ) - SUM( CASE WHEN o.operation = 1 THEN o.amount ELSE 0 END ),0) AS rem_amount 
        // SQL;
        $fullNameQuery = <<<SQL
            case when warehouse_items.category_id is null then warehouse_items.name else CONCAT(c.name,' → ', warehouse_items.name) end full_name
        SQL;
        return parent::getTableQuery()
            ->addSelect([
                // 'balance' => DB::table('warehouse_operations as o'),
                    // ->selectRaw($balanceQuery)
                    // ->whereRaw('o.item_id = warehouse_items.id'),
                'full_name' => DB::table('warehouse_item_categories as c')
                    ->selectRaw($fullNameQuery)
                    ->whereRaw('c.id = warehouse_items.category_id')
            ]);
    }
}
