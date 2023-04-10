<?php
/**
 * CreateOrderFromStripeSession.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Checkout;

use App\Actions\Users\GetOrCreateUser;
use App\DataTransferObjects\Customer;
use App\Enums\Currency;
use App\Models\Order;
use Stripe\Checkout\Session;

class CreateOrderFromStripeSession
{
    public function __construct(protected GetOrCreateUser $userCreator)
    {

    }

    public function execute(Session $session): Order
    {
        $user = $this->userCreator->execute($this->getCustomerFromSession($session->customer, $session->currency));
    }

    protected function getCustomerFromSession(\Stripe\Customer $stripeCustomer, string $currency): Customer
    {
        return new Customer(
            email: $stripeCustomer->email,
            stripeCustomerId: $stripeCustomer->id,
            name: $stripeCustomer->name,
            currency: Currency::from($stripeCustomer->currency ?: $currency)
        );
    }
}
