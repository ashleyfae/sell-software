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
use Illuminate\Contracts\Support\Arrayable;

readonly class LegacyPrice implements Arrayable
{
    public function __construct(
        public string $id,
        public string $name,
        public ?int $activationLimit,
        public ?int $licensePeriod,
        public PeriodUnit $licensePeriodUnit,
        public bool $isActive,
        public string $currency
    )
    {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
