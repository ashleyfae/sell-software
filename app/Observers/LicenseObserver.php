<?php

namespace App\Observers;

use App\Models\License;
use Illuminate\Support\Str;

class LicenseObserver
{
    /**
     * Handle the License "created" event.
     */
    public function creating(License $license): void
    {
        if (! $license->license_key) {
            $license->license_key = Str::uuid()->toString();
        }
    }
}
