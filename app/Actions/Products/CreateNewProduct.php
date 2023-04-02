<?php
/**
 * CreateNewProduct.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Products;

use App\Exceptions\Stores\MissingCurrentStoreException;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Repositories\StoreRepository;

class CreateNewProduct
{
    public function __construct(protected StoreRepository $storeRepository)
    {

    }

    public function createFromRequest(StoreProductRequest $request): Product
    {
        if (! $store = $this->storeRepository->getStoreForRequest($request)) {
            throw new MissingCurrentStoreException();
        }

        return $store->products()->create($request->validated());
    }
}
