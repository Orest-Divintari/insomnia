<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPreferencesRequest;

class AccountPreferenceController extends Controller
{
    /**
     * Get the form to update the user preferences
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('account.preferences.edit', ['user' => auth()->user()]);
    }

    /**
     * Update the user preferences settings
     *
     * @param UpdateUserPreferencesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserPreferencesRequest $request)
    {
        $request->persist();

        return redirect()->back();
    }
}