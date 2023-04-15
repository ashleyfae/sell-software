<?php
/**
 * RenewOrderItem.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Orders;

use App\Actions\Licenses\RenewLicense;
use App\Actions\Orders\Contracts\OrderItemProvision;
use App\Exceptions\Orders\OrderItemMissingLicenseException;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class RenewOrderItem implements OrderItemProvision
{
    public function __construct(protected RenewLicense $renewLicense)
    {

    }

    /**
     * @throws OrderItemMissingLicenseException
     */
    public function execute(OrderItem $orderItem): void
    {
        if (! $orderItem->license) {
            throw new OrderItemMissingLicenseException();
        }

        $this->renewLicense->renew($orderItem->license);

        $orderItem->provisioned_at = Carbon::now();
    }
}
