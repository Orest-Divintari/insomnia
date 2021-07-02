<?php

namespace App\Http\Controllers;

class AccountIgnoredUserController extends Controller
{
    /**
     * Display a listing of ignored users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $ignoredUsers = auth()->user()
            ->ignoredUsers()
            ->withProfileInfo(auth()->user())
            ->get();

        return view('account.ignored.users', compact('ignoredUsers'));
    }
}