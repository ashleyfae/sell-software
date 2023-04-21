<?php
/**
 * RenewLicense.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Enums\LicenseStatus;
use App\Models\License;
use Illuminate\Support\Carbon;

class RenewLicense
{
    public function __construct(protected CalculateExpirationDate $expirationDateCalculator)
    {

    }

    public function renew(License $license): License
    {
        $license->status = LicenseStatus::Active;
        $license->expires_at = $this->expirationDateCalculator->calculate(
            baseDate: $license->expires_at ?? Carbon::now(),
            period: $license->productPrice->license_period,
            periodUnit: $license->productPrice->license_period_unit,
        );
        $license->save();

        return $license;
    }
}
