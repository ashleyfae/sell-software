<?php
/**
 * Customer.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\DataTransferObjects;

use App\Enums\Currency;

class Customer
{
    public function __construct(
        public readonly string $email,
        public readonly string $stripeCustomerId,
        public readonly string $name,
        public readonly Currency $currency = Currency::USD
    ) {

    }
}
