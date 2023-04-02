<?php

namespace App\Models\Scopes;

use App\Repositories\StoreRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    public function __construct(protected StoreRepository $storeRepository)
    {

    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $currentStore = $this->storeRepository->getStoreForRequest(request());
        if ($currentStore) {
            $builder->where('store_id', $currentStore->id);
        }
    }
}
