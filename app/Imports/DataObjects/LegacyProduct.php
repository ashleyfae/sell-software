<?php
/**
 * LegacyProduct.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use Illuminate\Support\Arr;

class LegacyProduct extends AbstractLegacyObject
{
    /**
     * @param  LegacyPrice[]  $prices
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $dateCreated,
        public array $prices,
        public bool $isBundle
    )
    {
    }

    public function toArray(): array
    {
        $data = get_object_vars($this);

        $data['prices'] = array_map(fn(LegacyPrice $legacyPrice) => $legacyPrice->toArray(), $this->prices);

        return $data;
    }

    public static function fromArray(array $data) : static
    {
        return new static(
            id: Arr::get($data, 'id', 0),
            name: Arr::get($data, 'name', ''),
            dateCreated: Arr::get($data, 'dateCreated', ''),
            prices: array_map(fn($priceData) => LegacyPrice::fromArray($priceData), Arr::get($data, 'prices', [])),
            isBundle: (bool) Arr::get($data, 'isBundle', false)
        );
    }
}
