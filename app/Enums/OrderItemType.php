<?php
/**
 * OrderItemType.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

use App\Actions\Orders\Contracts\OrderItemProvision;
use App\Actions\Orders\ProvisionNewOrderItem;
use App\Actions\Orders\RenewOrderItem;

enum OrderItemType: string
{
    case New = 'new';
    case Renewal = 'renewal';

    public function getProvisioner():  OrderItemProvision
    {
        return match($this)  {
            OrderItemType::New => app(ProvisionNewOrderItem::class),
            OrderItemType::Renewal => app(RenewOrderItem::class),
        };
    }
}
