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
        $query = <<<SQL
            SUM( CASE WHEN o.operation = 0 THEN o.amount ELSE 0 END ) - SUM( CASE WHEN o.operation = 1 THEN o.amount ELSE 0 END ) AS rem_amount 
        SQL;
        return parent::getTableQuery()->addSelect(['balance' => DB::table('warehouse_operations as o')->selectRaw($query)->whereRaw('o.item_id = warehouse_items.id')]);
    }
}
