<?php

namespace App\Models;

use App\Imports\Enums\DataSource;
use App\Observers\LegacyMappingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $mappable_id
 * @property string $mappable_type
 * @property DataSource $source
 * @property int $source_id
 * @property ?int $secondary_source_id
 * @property array $source_data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin Builder
 */
#[ObservedBy(LegacyMappingObserver::class)]
class LegacyMapping extends Model
{
    protected $casts = [
        'mappable_id'         => 'int',
        'source'              => DataSource::class,
        'source_id'           => 'int',
        'secondary_source_id' => 'int',
        'source_data'         => 'array',
    ];
}
