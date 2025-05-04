<?php

namespace App\Models;

use App\Casts\CartItemsCast;
use App\DataTransferObjects\CartItem;
use App\Enums\PaymentGateway;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $session_id
 * @property CartItem[]|Collection $cart
 * @property PaymentGateway $gateway
 * @property string|null $ip
 * @property int|null $order_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Order|null $order
 *
 * @mixin Builder
 */
class CartSession extends Model
{
    use HasFactory, HasUser;

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
        'id'       => 'int',
        'cart'     => CartItemsCast::class,
        'gateway'  => PaymentGateway::class,
        'order_id' => 'int',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
