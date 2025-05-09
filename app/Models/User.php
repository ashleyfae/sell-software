<?php

namespace App\Models;

use App\Models\Traits\HasLegacyMapping;
use App\Models\Traits\HasOrders;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property bool $is_admin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property License[]|Collection $licenses
 * @property StripeCustomer[]|Collection $stripeCustomers
 *
 * @mixin Builder
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasOrders, HasLegacyMapping;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'stripe_customer_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                => 'int',
        'email_verified_at' => 'datetime',
        'is_admin'          => 'bool',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function cartSessions(): HasMany
    {
        return $this->hasMany(CartSession::class);
    }

    public function stripeCustomers(): HasMany
    {
        return $this->hasMany(StripeCustomer::class);
    }

    public function isAdmin(): bool
    {
        return ! empty($this->is_admin);
    }

    public function hasActiveLicenseForProduct(int $productId): bool
    {
        return $this->licenses()
                ->where('product_id', $productId)
                ->active()
                ->count() > 0;
    }
}
