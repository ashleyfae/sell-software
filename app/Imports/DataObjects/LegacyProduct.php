<?php
/**
 * LegacyProduct.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

readonly class LegacyProduct implements Arrayable
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
}
