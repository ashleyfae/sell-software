<?php

namespace App\View\Components\Elements;

use App\Models\License;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LicenseKeyInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public License $license)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.elements.license-key-input');
    }
}
