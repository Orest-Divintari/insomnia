<?php

namespace App\Http\Controllers;

class AccountIgnoredThreadController extends Controller
{
    /**
     * Display a listing of ignored threads
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $ignoredThreads = auth()->user()
            ->ignoredThreads()
            ->withIgnoredByVisitor(auth()->user())
            ->with('poster')
            ->withRecentReply()
            ->get();

        return view('account.ignored.threads', compact('ignoredThreads'));
    }
}