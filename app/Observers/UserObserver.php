<?php

namespace App\Observers;

use App\Jobs\UpdateStripeCustomerEmailJob;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged(['email'])) {
            UpdateStripeCustomerEmailJob::dispatch($user);
        }
    }
}
