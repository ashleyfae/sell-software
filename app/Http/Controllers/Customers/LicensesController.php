<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LicensesController extends Controller
{
    public function show(Request $request, License $license) : View
    {
        $license->load('siteActivations');

        return view('customers.licenses.show', [
            'license' => $license,
        ]);
    }
}
