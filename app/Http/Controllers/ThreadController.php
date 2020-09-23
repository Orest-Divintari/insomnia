<?php

namespace App\Http\Controllers;

use App\Category;
use App\Filters\ReplyFilters;
use App\Http\Requests\CreateThreadRequest;
use App\Reply;
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
        $filters = app('ThreadFilters');

        $threads = Thread::with('poster')
            ->filter($filters)
            ->withRecentReply()
            ->latest();

        if ($category->exists) {
            $threads->where('category_id', $category->id);
        }
        $threads = $threads->paginate(Thread::PER_PAGE);

        $threadFilters = $filters->getRequestedFilters();

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', compact('category', 'threads', 'threadFilters'));
    }

    /**
     * Show the form for posting a new thread
     *
     * @param string $category
     * @return Illuminate\View\View
     */
    public function create($categorySlug)
    {
        $category = Category::whereSlug($categorySlug)->firstOrFail();
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
     * Display a specific thread
     *
     * @param Thread $thread
     * @return Illuminate\View\View
     */
    public function show(Thread $thread, ReplyFilters $filters)
    {
        $thread->load('poster');

        $replies = Reply::forThread($thread, $filters);

        if (request()->wantsJson()) {
            return $replies;
        };

        $thread->recordVisit();

        return view('threads.show', compact('thread', 'replies'));

    }

}
