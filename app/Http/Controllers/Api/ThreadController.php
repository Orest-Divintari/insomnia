<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateThreadRequest;
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
        return $threads;
    }

    /**
     * Update an existing thread
     *
     * @param Thread $thread
     * @param UpdateThreadRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Thread $thread, UpdateThreadRequest $request)
    {
        $request->update($thread);
        return response('Thread has been updated', 200);
    }

}