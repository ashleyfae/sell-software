<?php
/**
 * HasOrderItems.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property OrderItem[]|Collection $orderItems
 */
trait HasOrderItems
{
    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'object');
    }
}
