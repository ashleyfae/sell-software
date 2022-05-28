<?php

namespace App\Models;

use App\Enums\PeriodUnit;
use App\Traits\HasActivationLimit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property Product $product
 * @property string $name
 * @property int $price
 * @property int $renewal_price
 * @property int|null $license_period
 * @property PeriodUnit $license_period_unit
 * @property int|null $activation_limit
 * @property string $stripe_id
 *
 * @mixin Builder
 */
class ProductPrice extends Model
{
    use HasFactory, HasActivationLimit;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'renewal_price',
        'license_period',
        'license_period_unit',
        'activation_limit',
        'stripe_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                  => 'int',
        'price'               => 'int',
        'renewal_price'       => 'int',
        'license_period'      => 'int',
        'license_period_unit' => PeriodUnit::class,
        'activation_limit'    => 'int',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stripe_id',
        'created_at',
        'updated_at',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
