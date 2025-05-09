<?php
/**
 * UpdateStripeCustomerEmail.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Users;

use App\Models\User;
use Stripe\StripeClient;

class UpdateStripeCustomerEmail
{
    public function __construct(
        protected StripeClient $stripeClient
    )
    {
    }

    public function execute(User $user) : void
    {
        if ($user->stripeCustomers->isEmpty()) {
            return;
        }

        foreach($user->stripeCustomers as $stripeCustomer) {
            $this->stripeClient->customers->update(
                id: $stripeCustomer->stripe_id,
                params: ['email' => $user->email]
            );
        }
    }
}
