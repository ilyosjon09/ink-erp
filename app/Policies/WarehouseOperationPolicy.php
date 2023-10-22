<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseOperation;
use Illuminate\Auth\Access\Response;

class WarehouseOperationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WarehouseOperation $warehouseOperation): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WarehouseOperation $warehouseOperation): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WarehouseOperation $warehouseOperation): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WarehouseOperation $warehouseOperation): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WarehouseOperation $warehouseOperation): bool
    {
        return $user->can('warehouse.view');
    }
}
