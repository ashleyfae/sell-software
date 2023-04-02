<?php

namespace App\Observers;

use Illuminate\Support\Str;

class SlugObserver
{
    public function creating($model)
    {
        if (! $model->slug && ! empty($model->name)) {
            $model->slug = Str::slug($model->name);
        }
    }
}
