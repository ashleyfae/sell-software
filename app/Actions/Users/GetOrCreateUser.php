<?php
/**
 * GetOrCreateUserFromStripeCustomer.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Users;

use App\DataTransferObjects\Customer;
use App\Models\StripeCustomer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Gets an existing user or creates a new one, given the information in the supplied {@see Customer} object.
 */
class GetOrCreateUser
{
    public function execute(Customer $customer): User
    {
        if ($user = $this->getUserByStripeId($customer)) {
            return $user;
        } elseif($user = $this->getUserByEmail($customer)) {
            $this->createStripeCustomerRecord($user, $customer);

            return $user;
        } else {
            return $this->createNewUser($customer);
        }
    }

    protected function getUserByStripeId(Customer $customer): ?User
    {
        $stripeCustomer = StripeCustomer::query()
            ->where('stripe_id', $customer->stripeCustomerId)
            ->first();

        return $stripeCustomer?->user;
    }

    protected function getUserByEmail(Customer $customer): ?User
    {
        return User::query()
            ->where('email', $customer->email)
            ->first();
    }

    protected function createStripeCustomerRecord(User $user, Customer $customer): void
    {
        $user->stripeCustomers()->create([
            'stripe_id' => $customer->stripeCustomerId,
            'currency' => $customer->currency,
        ]);
    }

    protected function createNewUser(Customer $customer): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $customer->name,
            'email' => $customer->email,
            'password' => Hash::make(Str::random(24)),
        ]);

        $this->createStripeCustomerRecord($user, $customer);

        Auth::login($user);

        return $user;
    }
}
