<?php

namespace App\Observers;

use App\Traits\HasUuid;
use Illuminate\Support\Str;

class UuidObserver
{
    /**
     * @param HasUuid $model
     */
    public function saving($model)
    {
        $property = $model::getUuidPropertyName();
        $model->{$property} = Str::uuid()->toString();
    }

}
