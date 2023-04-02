<?php

namespace App\View\Components;

use App\Actions\Stores\StoreDeterminer;
use App\Models\Store;
use Closure;
use Illuminate\Contracts\View\View;
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
        protected StoreDeterminer $storeDeterminer
    )
    {
        $this->storeDeterminer->determineForCurrentUser();
        $this->stores = $this->storeDeterminer->stores;
        $this->currentStore = $this->storeDeterminer->currentStore;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app');
    }
}
