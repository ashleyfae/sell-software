<?php

namespace App\View\Components;

use App\Models\Store;
use App\Repositories\StoreRepository;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class App extends Component
{
    public Collection $stores;
    public ?Store $currentStore = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        protected Request $request,
        protected StoreRepository $storeRepository
    )
    {
        $this->stores = $this->storeRepository->listForUser($this->request->user());
        $this->currentStore = $this->storeRepository->getStoreForRequest($request);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app');
    }
}
