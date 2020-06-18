<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CreateThreadRequest;
use App\Http\Resources\Thread;

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
    public function create($categoryId)
    {
        return view('threads.create', compact('categoryId'));
    }

    /**
     * Store a newly created thread in storage
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateThreadRequest $request)
    {
        $thread = $request->persist();
        return redirect(route('threads.show', $thread));
    }

    /**
     * Display the specified resource
     *
     * @param Thread $thread
     * @return Illuminate\View\View
     */
    public function show(Thread $thread)
    {
        auth()->user()->read($thread);
        return view('threads.show', compact('thread'));
    }

}