<?php

namespace App\View\Components\Menus;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public ?string $url = null, public ?string $routeName = null)
    {
        if ($this->routeName && is_null($this->url)) {
            $this->url = route($this->routeName);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menus.item');
    }
}
