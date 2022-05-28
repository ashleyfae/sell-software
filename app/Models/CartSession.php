<?php

namespace App\Models;

use App\DataTransferObjects\CartItem;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $session_id
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
        'is_renewal' => 'bool',
    ];

    public function cart(): Attribute
    {
        return Attribute::make(
            get: fn($value) => collect(array_map(fn(array $item) => CartItem::fromArray($item), json_decode($value, true))),
            set: fn($value) => json_encode($value)
        );
    }
}
