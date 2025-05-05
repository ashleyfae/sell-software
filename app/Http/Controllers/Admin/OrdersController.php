<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Orders\ListAllOrders;
use App\DataTransferObjects\Requests\AdminSearchOrdersInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchOrdersRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(SearchOrdersRequest $request, ListAllOrders $listAllOrders)
    {
        return view('admin.orders.index', [
            'orders' => $listAllOrders->execute(AdminSearchOrdersInput::fromArray($request->validated())),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
