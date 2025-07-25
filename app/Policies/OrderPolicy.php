<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Order $order): bool
    {
        return $user?->isAdmin()
            || ($user && $order->user_id === $user->id)
            || session('guest_order_access') === $order->purchase_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
