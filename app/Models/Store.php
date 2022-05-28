<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $stripe_public_key
 * @property string $stripe_private_key
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin Builder
 */
class Store extends Model
{
    use HasFactory, HasUser;

    protected $casts = [
        'id'                 => 'int',
        'stripe_public_key'  => 'encrypted',
        'stripe_private_key' => 'encrypted',
    ];
}
