<?php
/**
 * HasUuid.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Traits;

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

}
