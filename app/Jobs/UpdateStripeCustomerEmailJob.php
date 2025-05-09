<?php

namespace App\Jobs;

use App\Actions\Users\UpdateStripeCustomerEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateStripeCustomerEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateStripeCustomerEmail $updater): void
    {
        $updater->execute($this->user);
    }
}
