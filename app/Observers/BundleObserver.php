<?php

namespace App\Observers;

use App\Models\Bundle;

class BundleObserver
{
    public function saving(Bundle $bundle): void
    {
        if (! empty($bundle->price_ids) && is_array($bundle->price_ids)) {
            // this new variable is necessary because Laravel won't allow indirect modification of a property
            $priceIds = $bundle->price_ids;
            sort($priceIds);
            $bundle->price_ids = $priceIds;
        }
    }
}
