<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\Currency;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Events\OrderCreated;
use App\Models\Traits\HasOrderAmounts;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property OrderStatus $status
 * @property PaymentGateway $gateway
 * @property string|null $ip
 * @property Currency $currency
 * @property float $rate
 * @property Carbon|null $completed_at
 * @property string|null $stripe_session_id
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
    use HasFactory, HasUser, HasOrderAmounts;

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
        'stripe_session_id',
        'rate',
    ];

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
