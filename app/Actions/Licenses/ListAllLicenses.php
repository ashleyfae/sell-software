<?php
/**
 * ListAllLicenses.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Models\License;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAllLicenses
{
    public function fromRequest($request) : LengthAwarePaginator
    {
        return License::query()
            ->with([
                'product',
                'user',
            ])
            ->paginate(40);
    }
}
