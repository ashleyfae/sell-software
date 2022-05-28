<?php
/**
 * HasOrderStatus.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Traits;

use App\Enums\OrderStatus;

/**
 * @property OrderStatus $status
 */
trait HasOrderStatus
{
    public function scopeComplete($query)
    {
        return $query->where($this->getTable().'.status', OrderStatus::Complete);
    }
}
