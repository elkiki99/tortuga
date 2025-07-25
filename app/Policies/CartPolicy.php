<?php

namespace App\Policies;

use App\Models\User;

class CartPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user): bool
    {
        if (!$user) {
            return true;
        }

        return !$user->isAdmin();
    }

    /**
     * Determine whether the user can add items to the wishlist.
     */
    public function add(?User $user): bool
    {
        if (!$user) {
            return true;
        }

        return !$user->isAdmin();
    }

    /**
     * Determine whether the user can remove items from the wishlist.
     */
    public function remove(?User $user): bool
    {
        if (!$user) {
            return true;
        }
        return !$user->isAdmin();
    }

    /**
     * Determine whether the user can clear the cart.
     */
    public function clear(?User $user): bool
    {
        if (!$user) {
            return true;
        }
        return !$user->isAdmin();
    }
}
