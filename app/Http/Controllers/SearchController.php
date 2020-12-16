<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Search;
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
    public function show(Search $search, SearchRequest $searchRequest)
    {
        $validator = $searchRequest
            ->handle($this->request)
            ->getValidator();

        $query = $searchRequest->getSearchQuery();

        if ($validator->fails()) {
            return view('search.show', compact('query'))
                ->withErrors($validator);
        }

        $results = $search->handle($this->request);
        if (request()->expectsJson()) {
            return $results;
        }
        return view('search.show', compact('results', 'query'));
    }

    /**
     * Display the advanced search form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->viewSearchForm();
    }

    /**
     * Determine the search type
     *
     * @return View
     */
    public function viewSearchForm()
    {
        $type = $this->request->input('type');
        if ($type == 'thread') {
            return view('search.advanced.threads', ['type' => 'thread']);
        } elseif ($type == 'profile_post') {
            return view('search.advanced.profile_posts', ['type' => 'profile_post']);
        } elseif ($type == 'tag') {
            return view('search.advanced.tags', ['type' => 'tag']);
        }
        return view('search.advanced.all_posts', ['type' => '']);
    }
}