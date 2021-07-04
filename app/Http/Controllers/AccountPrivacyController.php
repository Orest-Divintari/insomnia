<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPrivacyRequest;

class AccountPrivacyController extends Controller
{
    /**
     * Update the privacy settings of the user
     *
     * @param UpdateUserPrivacyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserPrivacyRequest $request)
    {
        $request->persist();
        return redirect()->back();
    }

    /**
     * Display the form for editing the account privacy settings
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('account.privacy.edit', ['user' => auth()->user()]);
    }
}