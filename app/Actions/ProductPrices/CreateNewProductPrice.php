<?php
/**
 * CreateNewProductPrice.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\ProductPrices;

use App\Http\Requests\Admin\StoreProductPriceRequest;
use App\Models\Product;
use App\Models\ProductPrice;

class CreateNewProductPrice
{
    public function createFromRequest(StoreProductPriceRequest $request, Product $product): ProductPrice
    {
        return $product->prices()->create($request->validated());
    }
}
