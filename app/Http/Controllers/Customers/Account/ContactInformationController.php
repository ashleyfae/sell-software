<?php

namespace App\Http\Controllers\Customers\Account;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactInformationController extends Controller
{
    public function show(Request $request): View
    {
        return view('customers.account.contact', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, UpdateUserProfileInformation $updater): RedirectResponse
    {
        $updater->update(user: $request->user(), input: $request->input());

        $request->session()->flash('status', 'Contact information updated successfully.');

        return redirect()->route('customers.account.contact.show');
    }
}
