<?php

namespace App\Observers;

use App\Models\Store;
use App\Repositories\StoreRepository;

class StoreObserver
{
    public function __construct(protected StoreRepository $storeRepository)
    {

    }

    /**
     * Handle the Store "created" event.
     */
    public function created(Store $store): void
    {
        $this->storeRepository->clearStoreCacheForUser($store->user);
    }

    /**
     * Handle the Store "updated" event.
     */
    public function updated(Store $store): void
    {
        $this->storeRepository->clearStoreCacheForUser($store->user);
    }

    /**
     * Handle the Store "deleted" event.
     */
    public function deleted(Store $store): void
    {
        $this->storeRepository->clearStoreCacheForUser($store->user);
    }
}
