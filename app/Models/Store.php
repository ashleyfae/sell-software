<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $stripe_public_key
 * @property string $stripe_private_key
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product[]|Collection $products
 *
 * @mixin Builder
 */
class Store extends Model
{
    use HasFactory, HasUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'stripe_public_key',
        'stripe_private_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                 => 'int',
        'stripe_public_key'  => 'encrypted',
        'stripe_private_key' => 'encrypted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stripe_public_key',
        'stripe_private_key',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
