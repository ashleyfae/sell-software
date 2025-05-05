<?php
/**
 * CanPerformUserEmailSubqueryTrait.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Traits;

trait CanPerformUserEmailSubqueryTrait
{
    protected function whereUserEmailMatches(\Illuminate\Contracts\Database\Eloquent\Builder $query, string $email): void
    {
        $query->whereIn('user_id', function(\Illuminate\Database\Query\Builder $query) use($email) {
            $query->select('id')
                ->from('users')
                ->where('email', $email);
        });
    }
}
