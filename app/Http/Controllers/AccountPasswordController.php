<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPasswordRequest;

class AccountPasswordController extends Controller
{
    /**
     * Update the password of the user
     *
     * @param UpdateUserPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserPasswordRequest $request)
    {
        $request->persist();

        return redirect()->back();
    }

    /**
     * Display the form for editing the password
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('account.password.edit', ['user' => auth()->user()]);
    }
}