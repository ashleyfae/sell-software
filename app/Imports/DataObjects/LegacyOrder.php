<?php
/**
 * LegacyOrder.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;

class LegacyOrder extends AbstractLegacyObject
{
    /**
     * @param  LegacyOrderItem[]  $items
     */
    public function __construct(
        public int $id,
        public ?string $displayOrderNumber,
        public int $userId, // new DB
        public OrderStatus $orderStatus,
        public PaymentGateway $gateway,
        public ?string $ip,
        public string $currency,
        public int $subtotal,
        public int $discount,
        public int $tax,
        public int $total,
        public int $rate,
        public string $dateCreated,
        public string $dateCompleted,
        public ?string $stripeChargeId,
        public array $items
    ) {

    }

    public function toArray() : array
    {
        $data = get_object_vars($this);

        $data['items'] = array_map(fn(LegacyOrderItem $legacyOrderItem) => $legacyOrderItem->toArray(), $this->items);

        return $data;
    }
}
