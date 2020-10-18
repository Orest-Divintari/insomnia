<?php

namespace App;

use App\Search\ModelFilterFactory;
use App\Search\SearchStrategyFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class Search
{
    /**
     * Number of results per page
     * @var int
     */
    const RESULTS_PER_PAGE = 10;

    /**
     * The model factory that is used
     * to get the requested model filter
     *
     * @var mixed
     */
    protected $filtersFactory;

    /**
     * The strategy factory that is used
     * to get requested the search strategy
     *
     * @var [type]
     */
    protected $searchStrategyFactory;

    /**
     * Create a new search instance
     *
     * @param SearchStrategyFactory $strategyFactory
     * @param ModelFilterFactory $filtersFactory
     */
    public function __construct(SearchStrategyFactory $searchStrategyFactory, ModelFilterFactory $filtersFactory)
    {
        $this->searchStrategyFactory = $searchStrategyFactory;
        $this->filtersFactory = $filtersFactory;
    }

    /**
     * Get the search results
     * apply model filters on search results
     * and return paginated data
     *
     * @return Collection|string
     */
    public function handle(Request $request)
    {
        try {
            $strategy = $this->strategyFor($request);
            $filters = $this->filtersFor($request);

            $builder = $strategy->search($request);
            $results = $filters->apply($builder);
            return $this->getPaginatedData($results);
        } catch (Exception $e) {
            return $this->noResults();
        }
    }

    /**
     * Get a search strategy instance
     *
     * @param Request $request
     * @return SearchStrategyInterface
     */
    public function strategyFor(Request $request)
    {
        return $this->searchStrategyFactory->create($request);
    }

    /**
     * Get a model filter instance
     *
     * @param Request $request
     * @return App\Filters\Filter
     */
    public function filtersFor(Request $request)
    {
        return $this->filtersFactory->create($request);
    }
    /**
     * Paginates the data
     * Transforms the data if results
     * are returned from activities table
     *
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator|string
     */
    public function getPaginatedData($results)
    {
        $results = $results->paginate(static::RESULTS_PER_PAGE);
        if (empty($results->items())) {
            throw new Exception('No results');
        } elseif (array_key_exists('subject', $results->toArray()['data'][0])) {
            return $this->getActivitySubject($results);
        }
        return $results;
    }

    /**
     * When the activities table is used to get the results
     * then the results are enclosed in the subject attribute
     * This happens when there is no search query word
     *
     * @param Illuminate\Pagination\LengthAwarePaginator $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActivitySubject($results)
    {
        $data = collect(collect($results)['data']);

        $pagination = $results->toArray();
        $subjects = $data->pluck('subject');
        $results = new LengthAwarePaginator(
            $subjects,
            $pagination['total'],
            $pagination['per_page'],
            $pagination['current_page'],
            ['path' => $pagination['path']]
        );
        return $results;
    }

    /**
     * Return a message if no results are found
     *
     * @return string
     */
    public function noResults()
    {
        return 'No results';
    }
}