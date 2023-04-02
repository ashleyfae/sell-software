<?php
/**
 * StoreDeterminer.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Stores;

use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class StoreDeterminer
{
    protected const SESSION_KEY = 'currentStore';

    public Collection $stores;
    public ?Store $currentStore = null;

    public function __construct(protected StoreRepository $storeRepository)
    {
        $this->stores = collect([]);
    }

    public function determineForRequest(Request $request): void
    {
        $this->stores = $request->user() ? $this->storeRepository->listForUser($request->user()) : collect([]);

        if ($this->stores->isEmpty() || ! $request->user()) {
            return;
        }

        $this->currentStore = $this->getCurrentStore();
    }

    protected function getCurrentStore(): ?Store
    {
        $currentStoreId = Session::get(static::SESSION_KEY);
        if (! $currentStoreId && $this->stores->isNotEmpty()) {
            $currentStoreId = $this->stores->first()->id;
            $this->setCurrentStore($currentStoreId);
        }

        if ($currentStoreId) {
            return Arr::first($this->stores, fn(Store $store) => $store->id == $currentStoreId);
        }

        return null;
    }

    public function setCurrentStore(string $storeId): void
    {
        Session::put(static::SESSION_KEY, $storeId);
    }
}
