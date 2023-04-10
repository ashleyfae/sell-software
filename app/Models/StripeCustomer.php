<?php

namespace App\Models;

use App\Enums\Currency;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $stripe_id
 * @property Currency $currency
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class StripeCustomer extends Model
{
    use HasFactory, HasUser;

    protected $casts = [
        'currency' => Currency::class,
    ];

    protected $fillable = [
        'stripe_id',
        'currency',
    ];
}
