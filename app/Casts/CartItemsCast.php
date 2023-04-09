<?php

namespace App\Casts;

use App\DataTransferObjects\CartItem;
use App\Models\ProductPrice;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CartItemsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $cartItemsArray = json_decode($value, true);
        if (! is_array($cartItemsArray)) {
            return [];
        }

        $priceIds = Arr::pluck($cartItemsArray, 'price');
        if (empty($priceIds)) {
            return [];
        }

        $prices = ProductPrice::query()->whereIn('id', $priceIds)->get();

        $cartItemsArray = array_map(function(array $item) use($prices) {
            try {
                $item['price'] = Arr::first($prices, fn(ProductPrice $price) => $price->id === $item['price']);

                return CartItem::fromArray($item);
            } catch(\Exception $e) {
                return [];
            }
        }, $cartItemsArray);

        return array_filter($cartItemsArray);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! is_array($value)) {
            return json_encode([]);
        }

        return json_encode(array_filter(array_map(function($item) {
            if ($item instanceof CartItem) {
                return $item->toArray();
            } else {
                return [];
            }
        }, $value)));
    }
}
