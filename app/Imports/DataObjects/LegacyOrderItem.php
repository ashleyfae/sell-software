<?php
/**
 * LegacyOrderItem.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;

class LegacyOrderItem extends AbstractLegacyObject
{
    public function __construct(
        public int $id,
        public string $productName,
        public int $productId, // local
        public int $priceId, // local
        public OrderStatus $status,
        public OrderItemType $orderItemType,
        public int $subtotal,
        public int $discount,
        public int $tax,
        public int $total,
        public string $dateCreated
    )
    {
    }
}
