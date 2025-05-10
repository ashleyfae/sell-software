<?php

namespace App\Models;

use App\Observers\BundleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int[] $price_ids
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin Builder
 */
#[ObservedBy(BundleObserver::class)]
class Bundle extends Model
{
    protected $casts = [
        'price_ids' => 'array',
    ];
}
