<?php
/**
 * CartItem.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\DataTransferObjects;

use App\Contracts\DataTransferObject;
use App\Enums\OrderItemType;
use App\Models\License;
use App\Models\ProductPrice;
use InvalidArgumentException;

class CartItem implements DataTransferObject
{
    /**
     * @param  ProductPrice  $price  Chosen item price
     * @param  OrderItemType  $type  Type of order (new vs renewal)
     * @param  License|null  $license  The associated license -- will be set if this is a renewal.
     */
    public function __construct(
        public ProductPrice $price,
        public OrderItemType $type = OrderItemType::New,
        public ?License $license = null
    ) {

    }

    public function toArray(): array
    {
        $data = get_object_vars($this);

        if (array_key_exists('type', $data) && $data['type'] instanceof OrderItemType) {
            $data['type'] = $data['type']->value;
        }

        if (array_key_exists('price', $data) && $data['price'] instanceof ProductPrice) {
            $data['price'] = $data['price']->id;
        }

        if (array_key_exists('license', $data) && $data['license'] instanceof License) {
            $data['license'] = $data['license']->id;
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $type = OrderItemType::New;
        if (array_key_exists('type', $array)) {
            $type = ($array['type'] instanceof OrderItemType) ? $array['type'] : OrderItemType::from($array['type']);
        }

        if (empty($array['price']) || ! $array['price'] instanceof ProductPrice) {
            throw new InvalidArgumentException('Missing required price.');
        }

        return new static(
            price: $array['price'],
            type: $type,
            license: $array['license'] ?? null
        );
    }
}
