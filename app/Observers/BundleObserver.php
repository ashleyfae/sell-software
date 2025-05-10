<?php

namespace App\Observers;

use App\Models\Bundle;

class BundleObserver
{
    public function saving(Bundle $bundle): void
    {
        if (! empty($bundle->price_ids) && is_array($bundle->price_ids)) {
            sort($bundle->price_ids);
        }
    }
}
