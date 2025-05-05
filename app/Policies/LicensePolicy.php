<?php

namespace App\Policies;

use App\Models\License;
use App\Models\User;

class LicensePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, License $license): bool
    {
        return $user->id === $license->user_id;
    }

    /**
     * Determine whether the user can renew the model.
     */
    public function renew(User $user, License $license): bool
    {
        return $user->id === $license->user_id;
    }
}
