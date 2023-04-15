<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\Currency;
use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Models\Traits\HasOrderAmounts;
use App\Models\Traits\HasOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property int|null $product_id
 * @property int|null $product_price_id
 * @property string $product_name
 * @property OrderItemType $type
 * @property Carbon|null $provisioned_at
 * @property int|null $license_id
 * @property Currency $currency
 *
 * @property Order|Refund $object
 * @property Product $product
 * @property ProductPrice $productPrice
 * @property License|null $license
 *
 * @mixin Builder
 */
class OrderItem extends Model
{
    use HasFactory, HasOrderStatus, HasOrderAmounts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'             => 'int',
        'object_id'      => 'int',
        'status'         => OrderStatus::class,
        'type'           => OrderItemType::class,
        'subtotal'       => Money::class,
        'discount'       => Money::class,
        'tax'            => Money::class,
        'total'          => Money::class,
        'currency'       => Currency::class,
        'provisioned_at' => 'datetime',
        'license_id'     => 'int',
    ];

    public function object(): MorphTo
    {
        return $this->morphTo('object');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productPrice(): BelongsTo
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
