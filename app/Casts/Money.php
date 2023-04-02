<?php

namespace App\Casts;

use App\Enums\Currency;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): \App\Helpers\Money
    {
        if (! array_key_exists('currency', $attributes)) {
            throw new \InvalidArgumentException('Currency is required to get money from string.');
        }

        return new \App\Helpers\Money(
            currency: Currency::from($attributes['currency']),
            amount: $value
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_string($value) || is_float($value)) {
            return $this->makeFromString((float) $value, $attributes);
        } elseif($value instanceof \App\Helpers\Money) {
            return [
                $key => $value->amount,
                'currency' => $value->currency->value,
            ];
        }

        return $value;
    }

    protected function makeFromString(float $value, array $attributes) : int
    {
        if (! array_key_exists('currency', $attributes)) {
            throw new \InvalidArgumentException('Currency is required to make money from string.');
        }

        return \App\Helpers\Money::makeFromFloat(
            currency: Currency::from($attributes['currency']),
            amount: $value
        )->amount;
    }
}
