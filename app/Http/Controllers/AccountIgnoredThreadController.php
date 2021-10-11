<?php

namespace App\Http\Controllers;

use App\Models\Thread;

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
            ->get()
            ->each
            ->append('permissions');

        return view('account.ignored.threads', compact('ignoredThreads'));
    }
}