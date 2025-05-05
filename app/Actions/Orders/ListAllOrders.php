<?php
/**
 * ListAllOrders.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Orders;

use App\Actions\Traits\CanPerformUserEmailSubqueryTrait;
use App\DataTransferObjects\Requests\AdminSearchOrdersInput;
use App\Models\Order;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAllOrders
{
    use CanPerformUserEmailSubqueryTrait;

    public function execute(AdminSearchOrdersInput $input) : LengthAwarePaginator
    {
        return Order::query()
            ->with([
                'orderItems',
                'user',
            ])
            ->when($input->customerEmailSearchInput, fn(Builder $query) => $this->whereUserEmailMatches($query, $input->customerEmailSearchInput))
            ->orderBy('id', 'desc')
            ->paginate(40);
    }
}
