<?php

namespace App\Http\Controllers;

use App\Category;
use App\Filters\FilterManager;
use App\Http\Requests\CreateThreadRequest;
use App\Reply;
use App\Thread;
use DeepCopy\Filter\Filter;
use Illuminate\Http\Request;

class ThreadController extends Controller
{

    protected $filterManager;
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * Display the list of threads
     *
     * @param Category $category
     * @return Illuminate\View\View
     */
    public function index(Category $category)
    {
        $filters = $this->filterManager->withThreadFilters();
        $threadsQuery = Thread::with('poster')
            ->withHasBeenUpdated()
            ->filter($filters)
            ->withRecentReply()
            ->latest('updated_at');

        if ($category->exists) {
            $threadsQuery->where('category_id', $category->id);
        }

        $normalThreads = $threadsQuery->paginate(Thread::PER_PAGE);
        $pinnedThreads = $threadsQuery->pinned()->get();
        $threadFilters = $filters->getRequestedFilters();

        if (request()->wantsJson()) {
            return $normalThreads;
        }

        return view('threads.index', compact('category', 'normalThreads', 'pinnedThreads', 'threadFilters'));
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
     * @param CreateThreadRequest $request
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
    public function show(Thread $thread)
    {
        $thread->load(['poster', 'tags']);
        $filters = $this->filterManager->withReplyFilters();

        $replies = Reply::forRepliable($thread, $filters);

        if (request()->wantsJson()) {
            return $replies;
        };

        $thread->recordVisit();

        return view('threads.show', compact('thread', 'replies'));

    }

}