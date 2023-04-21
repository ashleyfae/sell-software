<?php

namespace App\Http\Controllers\Customers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request): View
    {
        return view('customers.account.orders-list', [
            'orders' => $request->user()->orders()->orderBy('id', 'desc')->paginate(20),
        ]);
    }
}
