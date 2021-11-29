<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\Events\Thread\ThreadWasCreated;
use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Http\Requests\CreateThreadRequest;
use App\Models\Category;
use App\Models\Thread;
use App\ViewModels\ThreadsIndexViewModel;
use App\ViewModels\ThreadsShowViewModel;
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

        $viewModel = new ThreadsIndexViewModel(
            $category,
            $excludeIgnoredFilter,
            $threadFilters
        );

        return view('threads.index', [
            'category' => $category,
            'threads' => $viewModel->threads(),
            'pinnedThreads' => $viewModel->pinnedThreads(),
            'threadFilters' => $threadFilters->getRequestedFilters(),
        ]);

    }

    /**
     * Display the form for posting a new thread to the given category
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
     *
     * @param CreateThreadRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateThreadRequest $request)
    {
        $thread = $request->persist();

        event(new ThreadWasCreated($thread, auth()->user()));

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

        $thread->append('permissions');

        $filters = $this->filterManager->withReplyFilters();

        $replies = $viewModel->replies($thread, $filters, auth()->user());

        $thread->recordVisit();

        event(new UserViewedPage(UserViewedPage::THREAD, $thread));

        return view('threads.show', compact('thread', 'replies'));
    }

}