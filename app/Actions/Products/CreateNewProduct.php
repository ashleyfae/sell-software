<?php
/**
 * CreateNewProduct.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Products;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;

class CreateNewProduct
{

    public function createFromRequest(StoreProductRequest $request): Product
    {
        return Product::create($request->validated());
    }
}
