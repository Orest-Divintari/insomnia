<?php

namespace App\Http\Controllers;

use App\Category;
use App\Events\Activity\UserViewedPage;
use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Http\Requests\CreateThreadRequest;
use App\Thread;
use App\ViewModels\ThreadsShowViewModel;
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
    public function index(Category $category, ExcludeIgnoredFilter $excludeIgnoredFilter)
    {
        $threadFilters = $this->filterManager->withThreadFilters();

        $threadsQuery = Thread::query()
            ->excludeIgnored(auth()->user(), $excludeIgnoredFilter)
            ->with('poster')
            ->withHasBeenUpdated()
            ->withRecentReply()
            ->forCategory($category)
            ->filter($threadFilters)
            ->latest('updated_at');

        $normalThreads = $threadsQuery->paginate(Thread::PER_PAGE);
        $pinnedThreads = $threadsQuery->pinned()->get();
        $threadFilters = $threadFilters->getRequestedFilters();

        return view(
            'threads.index',
            compact(
                'category',
                'normalThreads',
                'pinnedThreads',
                'threadFilters'
            )
        );
    }

    /**
     * Show the form for posting a new thread to the given category
     *
     * @param Category $category
     * @return Illuminate\View\View
     */
    public function create(Category $category)
    {
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
    public function show($threadSlug, ThreadsShowViewModel $viewModel)
    {
        $thread = $viewModel->thread($threadSlug, auth()->user());

        $filters = $this->filterManager->withReplyFilters();

        $replies = $viewModel->replies($thread, $filters, auth()->user());

        $thread->recordVisit();

        event(new UserViewedPage(UserViewedPage::THREAD, $thread));

        if (request()->wantsJson()) {
            return $replies;
        };

        return view('threads.show', compact('thread', 'replies'));
    }

}