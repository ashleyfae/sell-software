<?php

namespace App\Http\Controllers\Customers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request): View
    {
        return view('customers.account.orders.index', [
            'orders' => $request->user()->orders()->orderBy('id', 'desc')->paginate(20),
        ]);
    }

    public function show(Order $order) : View
    {
        return view('customers.account.orders.show', [
            'order' => $order,
        ]);
    }
}
