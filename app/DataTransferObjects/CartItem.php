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
use InvalidArgumentException;

class CartItem implements DataTransferObject
{
    /**
     * @param  int  $priceId Chosen item price
     * @param  OrderItemType  $type Type of order (new vs renewal)
     * @param  string|null  $licenseKey License key -- will be set if this is a renewal.
     */
    public function __construct(
        public int $priceId,
        public OrderItemType $type = OrderItemType::New,
        public ?string $licenseKey = null
    ) {

    }

    public function toArray(): array
    {
        $data = get_object_vars($this);

        if (array_key_exists('type', $data) && $data['type'] instanceof OrderItemType) {
            $data['type'] = $data['type']->value;
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $type = OrderItemType::New;
        if (array_key_exists('type', $array)) {
            $type = ($array['type'] instanceof OrderItemType) ? $array['type'] : OrderItemType::from($array['type']);
        }

        return new static(
            priceId: $array['priceId'] ?? throw new InvalidArgumentException('Missing required priceId.'),
            type: $type,
            licenseKey: $array['licenseKey'] ?? null
        );
    }
}
