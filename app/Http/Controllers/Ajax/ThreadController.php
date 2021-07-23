<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateThreadRequest;
use App\Models\Category;
use App\Models\Thread;

class ThreadController extends Controller
{

    /**
     * Display the list of threads
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        return $category->threads()->paginate(Thread::PER_PAGE);
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
