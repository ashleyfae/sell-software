<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Models\Traits\HasUuid;
use Ashleyfae\LaravelGitReleases\Traits\Releasable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $stripe_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property ProductPrice[]|Collection $productPrices
 *
 * @mixin Builder
 */
class Product extends Model
{
    use HasFactory, HasSlug, HasUuid, Releasable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'stripe_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stripe_id',
    ];

    protected function stripeUrl(): Attribute
    {
        $configKey = App::isProduction() ? 'prod' : 'test';

        return Attribute::make(
            get: fn() => Config::get("services.stripe.dashboardUrl.{$configKey}").'products/'.urlencode($this->stripe_id)
        );
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
