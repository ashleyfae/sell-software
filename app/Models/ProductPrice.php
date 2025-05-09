<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\PeriodUnit;
use App\Helpers\Money;
use App\Helpers\StripeHelper;
use App\Models\Traits\HasActivationLimit;
use App\Models\Traits\HasLegacyMapping;
use App\Models\Traits\HasUuid;
use App\Observers\ProductPriceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property string $uuid
 * @property int $product_id
 * @property Product $product
 * @property string $name
 * @property int|null $license_period
 * @property PeriodUnit $license_period_unit
 * @property int|null $activation_limit
 * @property ?string $stripe_id only nullable during imports
 * @property ?string $currency
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property string $stripeUrl
 *
 * @mixin Builder
 */
#[ObservedBy(ProductPriceObserver::class)]
class ProductPrice extends Model
{
    use HasFactory, HasActivationLimit, HasUuid, HasLegacyMapping;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'license_period',
        'license_period_unit',
        'activation_limit',
        'stripe_id',
        'currency',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                  => 'int',
        'license_period'      => 'int',
        'license_period_unit' => PeriodUnit::class,
        'activation_limit'    => 'int',
        'is_active'           => 'bool',
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

    public function getRouteKeyName() : string
    {
        return 'id';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function stripeUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => StripeHelper::dashboardUrl('prices/'.urlencode($this->stripe_id))
        );
    }
}
