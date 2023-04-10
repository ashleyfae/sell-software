<?php
/**
 * ListProducts.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ListProducts
{
    public function fromRequest(Request $request): LengthAwarePaginator
    {
        return Product::query()->paginate(20);
    }
}
