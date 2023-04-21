<?php

namespace App\View\Components\Menus;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class SideItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $url = '#',
        public bool $active = false,
        public ?string $routeName = null
    )
    {
        if (!is_null($this->routeName)) {
            $this->url = route($this->routeName);
            $this->active = Route::currentRouteName() === $this->routeName;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menus.side-item');
    }
}
