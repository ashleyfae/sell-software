<?php

namespace App\Policies;

use App\Models\User;
use Ashleyfae\LaravelGitReleases\Models\Release;

class ReleasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Release $release): bool
    {
        return $release->releasable_type === 'product' &&
            $user->hasActiveLicenseForProduct($release->releasable_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
