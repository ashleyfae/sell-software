<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $name
 * @property string $stripe_account_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product[]|Collection $products
 *
 * @mixin Builder
 */
class Store extends Model
{
    use HasFactory, HasUser, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                 => 'int',
        'stripe_account_id'  => 'encrypted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stripe_account_id',
        'uuid',
    ];

    public function getRouteKeyName() : string
    {
        return 'uuid';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
