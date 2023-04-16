<?php
/**
 * HasOrders.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Refund;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property Order[]|Collection $orders
 *
 * @mixin Builder
 */
trait HasOrders
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Order::class, 'user_id', 'object_id')
            ->where(
                'object_type',
                array_search(Order::class, Relation::morphMap()) ?: Order::class
            );
    }

    public function getPurchasedProductIds(): array
    {
        return $this->orderItems()
            ->select('product_id')
            ->complete()
            ->get()
            ->pluck('product_id')
            ->map('intval')
            ->toArray();
    }

    public function getPurchasedProducts(): \Illuminate\Support\Collection
    {
        $productIds = $this->getPurchasedProductIds();

        if (empty($productIds)) {
            return collect([]);
        }

        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }

    /**
     * @param  int|Product  $product
     *
     * @return bool
     */
    public function hasPurchasedProduct(Product|int $product): bool
    {
        $productId = $product instanceof Product ? $product->id : $product;

        $count = $this->orderItems()
            ->complete()
            ->where('product_id', $productId)
            ->count();

        return $count > 0;
    }

}
