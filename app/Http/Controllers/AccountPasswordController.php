<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPasswordRequest;

class AccountPasswordController extends Controller
{
    public function update(UpdateUserPasswordRequest $request)
    {
        $request->persist();
        return redirect()->back();
    }

    public function edit()
    {
        return view('account.password.edit', ['user' => auth()->user()]);
    }
}