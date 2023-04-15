<?php
/**
 * RequestToCartItemsAdapter.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Adapters\Checkout;

use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Exceptions\Checkout\InvalidProductsToPurchaseException;
use App\Exceptions\Checkout\MissingProductsToPurchaseException;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RequestToCartItemsAdapter
{
    /**
     * Makes an array of {@see CartItem} objects based on the current request.
     *
     * @param  Request  $request
     *
     * @return CartItem[]
     * @throws InvalidProductsToPurchaseException|MissingProductsToPurchaseException
     */
    public function execute(Request $request): array
    {
        $priceUuids = $this->getPriceUuidsFromRequest($request);
        if (empty($priceUuids)) {
            throw new MissingProductsToPurchaseException();
        }

        return $this->makeCartItems(
            $this->getPricesFromIds($priceUuids)
        );
    }

    protected function getPriceUuidsFromRequest(Request $request): array
    {
        return array_filter(
            Arr::wrap($request->input('products')),
            function($id) {
                return is_string($id) && Str::isUuid($id);
            }
        );
    }

    /**
     * Fetches active {@see ProductPrice} objects from the supplied price UUIDs.
     *
     * @param  string[]  $priceUuids
     *
     * @return Collection
     * @throws InvalidProductsToPurchaseException
     */
    protected function getPricesFromIds(array $priceUuids): Collection
    {
        $prices = ProductPrice::query()
            ->whereIn('uuid', $priceUuids)
            ->where('is_active', true)
            ->get();

        if ($prices->isEmpty()) {
            throw new InvalidProductsToPurchaseException();
        }

        return $prices;
    }

    /**
     * @param  Collection  $prices
     *
     * @return CartItem[]
     */
    protected function makeCartItems(Collection $prices): array
    {
        $cartItems = [];
        foreach($prices as $price) {
            $cartItems[] = new CartItem(price: $price, type: OrderItemType::New);
        }

        return $cartItems;
    }
}
