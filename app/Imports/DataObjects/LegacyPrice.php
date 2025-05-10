<?php
/**
 * LegacyPrice.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use App\Enums\PeriodUnit;
use Illuminate\Support\Arr;

class LegacyPrice extends AbstractLegacyObject
{
    public function __construct(
        public ?int $newPriceId,
        public ?int $index,
        public string $name,
        public ?int $activationLimit,
        public ?int $licensePeriod,
        public PeriodUnit $licensePeriodUnit,
        public bool $isActive,
        public string $currency
    )
    {
    }

    public static function fromArray(array $data) : static
    {
        return new LegacyPrice(
            newPriceId: Arr::get($data, 'newPriceId'),
            index: Arr::get($data, 'index'),
            name: Arr::get($data, 'name'),
            activationLimit: Arr::get($data, 'activationLimit'),
            licensePeriod: Arr::get($data, 'licensePeriod'),
            licensePeriodUnit: PeriodUnit::from(Arr::get($data, 'licensePeriodUnit')),
            isActive: (bool) Arr::get($data, 'isActive', false),
            currency: Arr::get($data, 'currency', '')
        );
    }
}
