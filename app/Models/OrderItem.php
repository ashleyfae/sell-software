<?php

namespace App\Models;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Traits\HasOrderAmounts;
use App\Traits\HasOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $product_id
 * @property int|null $product_price_id
 * @property string $product_name
 * @property OrderItemType $type
 * @property Carbon|null $provisioned_at
 *
 * @property Order $object
 * @property Product $product
 * @property ProductPrice $productPrice
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
        'id' => 'int',
        'status' => OrderStatus::class,
        'type' => OrderItemType::class,
        'subtotal'     => 'int',
        'discount'     => 'int',
        'tax'          => 'int',
        'total'        => 'int',
        'provisioned_at' => 'datetime',
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

}
