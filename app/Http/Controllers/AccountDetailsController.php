<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountDetailsRequest;

class AccountDetailsController extends Controller
{
    /**
     * Update the account details of the user
     *
     * @param UpdateAccountDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAccountDetailsRequest $request)
    {
        $request->persist();

        return redirect()->back();
    }

    /**
     * Display the form for editing the account details of the user
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('account.details.edit', ['user' => auth()->user()]);
    }
}