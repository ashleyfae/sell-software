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
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StoreDeterminer
{
    public Collection $stores;
    public ?Store $currentStore = null;

    public function __construct()
    {
        $this->stores = collect([]);
    }

    public function determineForCurrentUser() : void
    {
        $this->stores = Auth()->check() ? Auth::user()->stores : collect([]);

        if ($this->stores->isEmpty() || ! Auth::check()) {
            return;
        }

        $this->currentStore = $this->getCurrentStore();
    }

    protected function getCurrentStore(): ?Store
    {
        $currentStoreId = Session::get('currentStore');
        if (! $currentStoreId && $this->stores->isNotEmpty()) {
            $currentStoreId = $this->stores->first()->id;
            Session::put('currentStore', $currentStoreId);
        }

        if ($currentStoreId) {
            return Arr::first($this->stores, fn(Store $store) => $store->id === $currentStoreId);
        }

        return null;
    }
}
