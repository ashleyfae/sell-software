<?php

namespace App\View\Components\Elements;

use App\Enums\LicenseStatus;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LicenseKeyStatus extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public LicenseStatus $status)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.elements.license-key-status');
    }
}
