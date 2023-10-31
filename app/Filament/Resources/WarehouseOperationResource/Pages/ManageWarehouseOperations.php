<?php

namespace App\Filament\Resources\WarehouseOperationResource\Pages;

use App\Enums\OperationType;
use App\Filament\Resources\WarehouseOperationResource;
use App\Services\WarehouseOperationService;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWarehouseOperations extends ManageRecords
{
    protected static string $resource = WarehouseOperationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data) {
                $operationService = new WarehouseOperationService;
                match (OperationType::from($data['operation'])) {
                    OperationType::ADD => $operationService->stockIn($data['item_id'], $data['amount'], $data['price'], $data['created_at']),
                    OperationType::SUBTRACT => $operationService->stockOut($data['item_id'], $data['amount'], $data['price'], $data['created_at']),
                };
            }),
        ];
    }
}
