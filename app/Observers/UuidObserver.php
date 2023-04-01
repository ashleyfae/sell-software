<?php

namespace App\Observers;

use App\Models\Traits\HasUuid;
use Illuminate\Support\Str;

class UuidObserver
{
    /**
     * @param HasUuid $model
     */
    public function creating($model)
    {
        $property = $model::getUuidPropertyName();

        if (! $model->{$property}) {
            $model->{$property} = Str::uuid()->toString();
        }
    }

}
