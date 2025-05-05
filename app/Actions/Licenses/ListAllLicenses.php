<?php
/**
 * ListAllLicenses.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\DataTransferObjects\Requests\AdminSearchLicensesInput;
use App\Models\License;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAllLicenses
{
    public function fromRequest(AdminSearchLicensesInput $input) : LengthAwarePaginator
    {
        return License::query()
            ->with([
                'product',
                'user',
            ])
            ->when($input->licenseKeySearchInput, fn(Builder $query) => $query->where('license_key', $input->licenseKeySearchInput))
            ->when($input->customerEmailSearchInput, function(Builder $query) use($input) {
                $query->whereIn('user_id', function(\Illuminate\Database\Query\Builder $query) use($input) {
                    $query->select('id')
                        ->from('users')
                        ->where('email', $input->customerEmailSearchInput);
                });
            })
            ->paginate(40);
    }
}
