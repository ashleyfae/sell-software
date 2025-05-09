<?php
/**
 * HasImportMapping.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Models\LegacyMapping;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasLegacyMapping
{
    public function legacyMapping() : MorphOne
    {
        return $this->morphOne(LegacyMapping::class, 'mappable');
    }
}
