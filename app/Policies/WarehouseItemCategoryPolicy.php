<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseItemCategory;
use Illuminate\Auth\Access\Response;

class WarehouseItemCategoryPolicy
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
    public function view(User $user, WarehouseItemCategory $warehouseItemCategory): bool
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
    public function update(User $user, WarehouseItemCategory $warehouseItemCategory): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WarehouseItemCategory $warehouseItemCategory): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WarehouseItemCategory $warehouseItemCategory): bool
    {
        return $user->can('warehouse.view');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WarehouseItemCategory $warehouseItemCategory): bool
    {
        return $user->can('warehouse.view');
    }
}
