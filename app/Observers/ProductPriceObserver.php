<?php

namespace App\Observers;

use App\Actions\ProductPrices\GetProductPriceCurrencyFromStripe;
use App\Models\ProductPrice;

class ProductPriceObserver
{
    public function saving(ProductPrice $productPrice): void
    {
        if (
            $productPrice->stripe_id &&
            (! $productPrice->currency || $productPrice->isDirty('stripe_id'))
        ) {
            $productPrice->currency = app(GetProductPriceCurrencyFromStripe::class)->execute($productPrice->stripe_id);
        }
    }
}
