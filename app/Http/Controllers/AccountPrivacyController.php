<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPrivacyRequest;

class AccountPrivacyController extends Controller
{
    public function update(UpdateUserPrivacyRequest $request)
    {
        $request->persist();
        return redirect()->back();
    }

    public function edit()
    {
        return view('account.privacy.edit', ['user' => auth()->user()]);
    }
}