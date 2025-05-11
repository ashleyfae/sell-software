<?php
/**
 * LegacyRefund.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;

class LegacyRefund extends AbstractLegacyObject
{
    /**
     * @param  LegacyOrderItem[]  $items
     */
    public function __construct(
        public int $id,
        public string $displayOrderNumber,
        public int $orderId, // new DB
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
        public ?string $gatewayTransactionId,
        public array $items
    )
    {
    }
}
