<?php

namespace App\Policies;

use App\Models\User;

class CheckoutPolicy
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
}
