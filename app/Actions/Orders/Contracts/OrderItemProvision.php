<?php
/**
 * OrderItemProvision.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Orders\Contracts;

use App\Models\OrderItem;

interface OrderItemProvision
{
    public function execute(OrderItem $orderItem): void;
}
