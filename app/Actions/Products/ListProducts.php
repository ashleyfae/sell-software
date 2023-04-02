<?php
/**
 * ListProducts.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Products;

use App\Exceptions\Stores\MissingCurrentStoreException;
use App\Repositories\StoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ListProducts
{
    public function __construct(protected StoreRepository $storeRepository)
    {

    }

    /**
     * @throws MissingCurrentStoreException
     */
    public function fromRequest(Request $request): LengthAwarePaginator
    {
        if (! $currentStore = $this->storeRepository->getStoreForRequest($request)) {
            throw new MissingCurrentStoreException();
        }

        return $currentStore->products()->paginate(20);
    }
}
