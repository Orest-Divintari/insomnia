<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CreateThreadRequest;

class ThreadController extends Controller
{

    /**
     * Display the list of threads
     *
     * @param Category $category
     * @return Illuminate\View\View
     */
    public function index(Category $category)
    {
        return view('threads.index', compact('category'));
    }

    /**
     * Show the form for posting a new thread
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created thread in storage
     *
     * @return \Illuminate\Http\Response`
     */
    public function store(CreateThreadRequest $request)
    {
        $thread = $request->persist();
        return redirect(route('threads.show', $thread));
    }

}