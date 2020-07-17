<?php

namespace App\Http\Controllers;

use App\Category;
use App\Filters\ReplyFilters;
use App\Filters\ThreadFilters;
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
    public function index(Category $category, ThreadFilters $filters)
    {
        $threads = Thread::with(['recentReply.poster', 'poster'])->filter($filters)->latest();

        if ($category->exists) {
            $threads->where('category_id', $category->id);
        }

        $threads = $threads->paginate(Thread::PER_PAGE);

        $threadFilters = $filters->getThreadFilters();
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
    public function show($threadSlug, ReplyFilters $filters)
    {
        $thread = Thread::with('poster')->without('recentReply')
            ->whereSlug($threadSlug)->firstOrFail();

        $replies = Reply::forThread($thread, $filters);

        if (request()->wantsJson()) {
            return $replies;
        };

        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $thread->increment('views');

        return view('threads.show', compact('thread', 'replies'));

    }

}