<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Licenses\ListAllLicenses;
use App\DataTransferObjects\Requests\AdminSearchLicensesInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchLicenseKeysRequest;
use Illuminate\Http\Request;

class LicensesController extends Controller
{
    public function index(SearchLicenseKeysRequest $request, ListAllLicenses $listAllLicenses)
    {
        $searchInput = AdminSearchLicensesInput::fromArray($request->validated());

        return view('admin.licenses.index', [
            'request'  => $searchInput,
            'licenses' => $listAllLicenses->fromRequest($searchInput),
        ]);
    }
}
