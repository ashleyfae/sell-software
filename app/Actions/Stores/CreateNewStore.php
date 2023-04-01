<?php
/**
 * CreateNewStore.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Stores;

use App\Http\Requests\Stores\StoreStoreRequest;
use App\Models\Store;

class CreateNewStore
{
    public function createFromRequest(StoreStoreRequest $request): Store
    {
        return $request->user()->stores()->create($request->validated());
    }
}
