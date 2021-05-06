<?php

namespace App\Http\Controllers;

class AccountController extends Controller
{
    public function show()
    {
        return view('account.details', ['user' => auth()->user()]);
    }
}