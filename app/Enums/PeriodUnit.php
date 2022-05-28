<?php
/**
 * PeriodUnit.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum PeriodUnit: string
{
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Year = 'year';
    case Lifetime = 'lifetime';
}
