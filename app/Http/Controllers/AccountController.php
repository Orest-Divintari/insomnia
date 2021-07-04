<?php

namespace App\Http\Controllers;

class AccountController extends Controller
{
    /**
     * Display the account details of the authenticated user
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('account.details', ['user' => auth()->user()]);
    }
}