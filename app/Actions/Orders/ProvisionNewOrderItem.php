<?php
/**
 * ProvisionOrderItem.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Orders;

use App\Actions\Licenses\CalculateExpirationDate;
use App\Actions\Orders\Contracts\OrderItemProvision;
use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class ProvisionNewOrderItem implements OrderItemProvision
{
    public function __construct(protected CalculateExpirationDate $expirationDateCalculator)
    {

    }

    public function execute(OrderItem $orderItem): void
    {
        $license = $this->createLicense($orderItem);

        $orderItem->license()->associate($license);
        $orderItem->provisioned_at = Carbon::now();
        $orderItem->save();
    }

    protected function createLicense(OrderItem $orderItem): License
    {
        $license = new License();
        $license->order()->associate($orderItem->object);
        $license->user()->associate($orderItem->object->user);
        $license->status = LicenseStatus::Active;
        $license->product()->associate($orderItem->product);
        $license->productPrice()->associate($orderItem->productPrice);
        $license->activation_limit = $orderItem->productPrice->activation_limit;
        $license->expires_at = $this->expirationDateCalculator->calculate(
            baseDate: Carbon::now(),
            period: $orderItem->productPrice->license_period,
            periodUnit: $orderItem->productPrice->license_period_unit
        );

        $license->save();

        return $license;
    }
}
