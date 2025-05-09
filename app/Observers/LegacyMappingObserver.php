<?php

namespace App\Observers;

use App\Imports\Repositories\DataSourceRepository;
use App\Models\LegacyMapping;
use Illuminate\Support\Facades\Config;

class LegacyMappingObserver
{
    public function saving(LegacyMapping $legacyMapping): void
    {
        $legacyMapping->source = DataSourceRepository::getCurrentSource();
    }
}
