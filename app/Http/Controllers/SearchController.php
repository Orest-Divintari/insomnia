<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Search\Search;
use App\Search\SearchData;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    protected $request;

    /**
     * Instantiate a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Number of results per page
     * @var int
     */
    const RESULTS_PER_PAGE = 2;

    /**
     * Display the search results
     *
     * @return Illuminate\View\View;
     */
    public function index(Search $search, SearchRequest $searchRequest)
    {
        $validator = $searchRequest->handle();

        $query = $searchRequest->getSearchQuery();

        if ($validator->fails()) {
            return view('search.index', compact('query'))
                ->withErrors($validator);
        }

        $results = $search->handle(SearchData::fromRequest($this->request));

        if (request()->expectsJson()) {
            return $results;
        }
        return view('search.index', compact('results', 'query'));
    }

    /**
     * Display the advanced search form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $searchForm = [
            'thread' => 'search.advanced.threads',
            'profile_post' => 'search.advanced.posts.profile',
            'tag' => 'search.advanced.tags',
            '' => 'search.advanced.posts.all',
        ];
        $type = $this->request->input('type') ?? '';

        return view($searchForm[$type], compact('type'));
    }

}