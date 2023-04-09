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
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $session_id
 * @property CartItem[]|Collection $cart
 * @property PaymentGateway $gateway
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        'id'         => 'int',
        'cart' => CartItemsCast::class,
        'gateway' => PaymentGateway::class,
    ];

    public function cart(): Attribute
    {
        return Attribute::make(
            get: fn($value) => collect(array_map(fn(array $item) => CartItem::fromArray($item), json_decode($value, true))),
            set: fn($value) => json_encode($value)
        );
    }
}
