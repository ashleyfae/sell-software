<?php
/**
 * HasUuid.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Observers\UuidObserver;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::observe(UuidObserver::class);
    }

    public static function getUuidPropertyName(): string
    {
        return 'uuid';
    }

    public function getRouteKeyName() : string
    {
        return 'uuid';
    }
}
