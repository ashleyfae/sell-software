<?php
/**
 * Money.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Helpers;

use App\Enums\Currency;

class Money
{
    public function __construct(protected Currency $currency, protected int $amount)
    {

    }

    public function __toString(): string
    {
        return "{$this->currency->getSymbol()}{$this->getDisplayValue()}";
    }

    public function getDisplayValue(): string
    {
        $amount = round($this->amount / $this->currency->getDecimals(), $this->currency->getDecimals());

        return number_format($amount, $this->currency->getDecimals());
    }
}
