<?php

namespace App\Providers;

use App\Models\Bundle;
use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'bundle'        => Bundle::class,
            'license'       => License::class,
            'order'         => Order::class,
            'order_item'    => OrderItem::class,
            'product'       => Product::class,
            'product_price' => ProductPrice::class,
            'refund'        => Refund::class,
            'user'          => User::class,
        ]);
    }
}
