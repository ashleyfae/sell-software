<?php
/**
 * CalculateExpirationDate.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Enums\PeriodUnit;
use Illuminate\Support\Carbon;

class CalculateExpirationDate
{
    public function calculate(Carbon $baseDate, ?int $period, PeriodUnit $periodUnit) : ?Carbon
    {
        if (is_null($period) || $periodUnit === PeriodUnit::Lifetime) {
            return null;
        }

        if ($baseDate->isPast()) {
            $baseDate = Carbon::now();
        }

        $baseDate->add(
            unit: $periodUnit->value,
            value: $period
        );

        return $baseDate;
    }
}
