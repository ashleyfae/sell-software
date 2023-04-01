<?php
/**
 * HasUser.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property User $user
 */
trait HasUser
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
