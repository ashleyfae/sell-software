<?php

namespace App\Models;

use App\Helpers\DomainSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $license_id
 * @property string $domain
 * @property bool $is_local
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property License $license
 *
 * @mixin Builder
 */
class SiteActivation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain',
        'is_local',
    ];

    protected $hidden = [
        'id',
        'license_id',
        'is_local',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'         => 'int',
        'license_id' => 'int',
        'is_local'   => 'bool',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function scopeWhereDomain($query, string $domain)
    {
        return $query->where('domain', DomainSanitizer::normalize($domain));
    }

}
