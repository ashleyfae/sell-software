<?php
/**
 * UpdateProduct.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Products;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class UpdateProduct
{
    public function executeFromRequest(Product $product, UpdateProductRequest $request): Product
    {
        $product->update($request->validated());

        return $product;
    }
}
