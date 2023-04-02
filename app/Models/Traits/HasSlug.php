<?php
/**
 * HasSlug.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Observers\SlugObserver;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::observe(SlugObserver::class);
    }
}
