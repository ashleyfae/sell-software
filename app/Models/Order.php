<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\Currency;
use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Events\OrderCreated;
use App\Models\Traits\HasLegacyMapping;
use App\Models\Traits\HasOrderAmounts;
use App\Models\Traits\HasUser;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $custom_id -- to support IDs from imported DBs where they may have been different
 * @property string $display_id -- order ID for display purposes
 * @property OrderStatus $status
 * @property PaymentGateway $gateway
 * @property string|null $ip
 * @property Currency $currency
 * @property float $rate
 * @property Carbon|null $completed_at
 * @property string|null $stripe_session_id
 * @property string|null $stripe_payment_intent_id
 * @property string|null $gateway_transaction_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property OrderItem[]|Collection $orderItems
 * @property Refund[]|Collection $refunds
 *
 * @mixin Builder
 */
class Order extends Model
{
    use HasFactory, HasUser, HasOrderAmounts, HasUuid, HasLegacyMapping;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'           => 'int',
        'status'       => OrderStatus::class,
        'gateway'      => PaymentGateway::class,
        'subtotal'     => Money::class,
        'discount'     => Money::class,
        'tax'          => Money::class,
        'total'        => Money::class,
        'currency'     => Currency::class,
        'rate'         => 'float',
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'custom_id',
        'stripe_session_id',
        'rate',
    ];

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'object');
    }

    public function isRenewal(): bool
    {
        return in_array(OrderItemType::Renewal, Arr::pluck($this->orderItems, 'type'));
    }
}
