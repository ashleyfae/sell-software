<?php
/**
 * GetProductPriceCurrencyFromStripe.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\ProductPrices;

use Stripe\StripeClient;

class GetProductPriceCurrencyFromStripe
{
    public function __construct(
        protected StripeClient $stripeClient
    )
    {
    }

    public function execute(string $stripePriceId) : string
    {
        return $this->stripeClient->prices->retrieve($stripePriceId)?->currency;
    }
}
