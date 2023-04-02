<?php
/**
 * StoreRepository.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StoreRepository
{
    public function listForUser(User $user) : Collection
    {
        return Cache::remember("stores-{$user->id}", 600, function() use($user) {
            return $user->stores()->orderBy('name')->get();
        });
    }

    public function clearStoreCacheForUser(User $user): void
    {
        Cache::forget("stores-{$user->id}");
    }
}
