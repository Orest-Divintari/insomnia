<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CreateThreadRequest;
use App\Thread;

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
        $threads = $category->threads()->paginate(Thread::PER_PAGE);
        return view('threads.index', compact('category', 'threads'));
    }

    /**
     * Show the form for posting a new thread
     *
     * @param string $category
     * @return Illuminate\View\View
     */
    public function create($category)
    {

        $category = Category::whereSlug($category)->firstOrFail();
        return view('threads.create', compact('category'));
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
    public function show($threadSlug)
    {
        $thread = Thread::without('recentReply')->whereSlug($threadSlug)->first();
        if (auth()->check()) {
            auth()->user()->read($thread);
        }
        return view('threads.show', compact('thread'));
    }

}