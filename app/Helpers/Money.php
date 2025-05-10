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
    public function __construct(public Currency $currency, public int $amount)
    {

    }

    public function __toString(): string
    {
        return "{$this->currency->getSymbol()}{$this->getDisplayValue()}";
    }

    public function getDisplayValue(): string
    {
        $amount = round($this->amount / 100, $this->currency->getDecimals());

        return number_format($amount, $this->currency->getDecimals());
    }

    public static function makeFromFloat(Currency $currency, float $amount): static
    {
        return new static(
            currency: $currency,
            amount: round($amount * 100)
        );
    }
}
