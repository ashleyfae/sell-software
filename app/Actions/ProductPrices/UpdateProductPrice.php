<?php
/**
 * UpdateProductPrice.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\ProductPrices;

use App\Http\Requests\Admin\UpdateProductPriceRequest;
use App\Models\ProductPrice;

class UpdateProductPrice
{
    public function createFromRequest(UpdateProductPriceRequest $request, ProductPrice $price): ProductPrice
    {
        $data = $request->validated();
        $data['is_active'] = ! empty($data['is_active']); // forces a missing boolean to update

        $price->update($data);

        return $price;
    }
}
