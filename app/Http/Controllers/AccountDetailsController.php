<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountDetailsRequest;

class AccountDetailsController extends Controller
{
    public function update(UpdateAccountDetailsRequest $request)
    {
        $request->persist();
        return redirect()->back();
    }

    public function edit()
    {
        return view('account.details.edit', ['user' => auth()->user()]);
    }
}