<?php
/**
 * Currency.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum Currency: string
{
    case GBP = 'gbp';
    case USD = 'usd';

    public function getSymbol(): string
    {
        return match($this) {
            Currency::GBP => '£',
            Currency::USD => '$',
        };
    }

    public function getDecimals(): int
    {
        return 2;
    }
}
