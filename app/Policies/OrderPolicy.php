<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->getAllPermissions()->contains('name', 'order.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->getAllPermissions()->contains('name', 'order.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->getAllPermissions()->contains('name', 'order.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->getAllPermissions()->contains('name', 'order.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->getAllPermissions()->contains('name', 'order.delete');
    }

    public function markAsPrinted(User $user, Order $order)
    {
        return $user->getAllPermissions()->contains('name', 'order.mark-as-printed');
    }
}
