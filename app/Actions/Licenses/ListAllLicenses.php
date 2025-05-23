<?php
/**
 * ListAllLicenses.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Actions\Traits\CanPerformUserEmailSubqueryTrait;
use App\DataTransferObjects\Requests\AdminSearchLicensesInput;
use App\Models\License;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAllLicenses
{
    use CanPerformUserEmailSubqueryTrait;

    public function fromRequest(AdminSearchLicensesInput $input) : LengthAwarePaginator
    {
        return License::query()
            ->with([
                'product',
                'user',
            ])
            ->when($input->licenseKeySearchInput, fn(Builder $query) => $query->where('license_key', $input->licenseKeySearchInput))
            ->when($input->customerEmailSearchInput, fn(Builder $query) => $this->whereUserEmailMatches($query, $input->customerEmailSearchInput))
            ->orderBy('id', 'desc')
            ->paginate(40);
    }
}
