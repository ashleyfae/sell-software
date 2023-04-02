<?php
/**
 * HasStore.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Models\Scopes\StoreScope;
use App\Models\Store;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $store_id
 * @property Store|null $store
 */
trait HasStore
{
    public static function bootHasStore(): void
    {
        static::addGlobalScope(app(StoreScope::class));
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
