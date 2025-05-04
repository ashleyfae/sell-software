<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Licenses\ListAllLicenses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LicensesController extends Controller
{
    public function index(Request $request, ListAllLicenses $listAllLicenses)
    {
        return view('admin.licenses.index', [
            'licenses' => $listAllLicenses->fromRequest($request),
        ]);
    }
}
