<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use App\Models\Traits\HasActivationLimit;
use App\Models\Traits\HasUser;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $license_key
 * @property LicenseStatus $status
 * @property int $product_id
 * @property int $product_price_id
 * @property Carbon|null $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product $product
 * @property ProductPrice $productPrice
 * @property OrderItem[]|Collection $orderItems
 *
 * @mixin Builder
 */
class License extends Model
{
    use HasFactory, HasUser, HasActivationLimit, HasUuid;

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
        'id'               => 'int',
        'status'           => LicenseStatus::class,
        'product_id'       => 'int',
        'product_price_id' => 'int',
        'expires_at'       => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    public static function getUuidPropertyName(): string
    {
        return 'license_key';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productPrice(): BelongsTo
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', LicenseStatus::Active);
    }

    public function isExpired(): bool
    {
        return $this->status === LicenseStatus::Expired;
    }

    public function isLifetime(): bool
    {
        return is_null($this->expires_at);
    }
}
