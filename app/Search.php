<?php

namespace App;

use App\Search\ModelFilterFactory;
use App\Search\SearchIndexFactory;
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
     * The search index factory that is used
     * to get requested the search index
     *
     * @var SearchIndexFactory
     */
    protected $indexFactory;

    /**
     * Create a new Search instance
     *
     * @param SearchIndexFactory $searchIndexFactory
     * @param ModelFilterFactory $filtersFactory
     */
    public function __construct(SearchIndexFactory $searchIndexFactory, ModelFilterFactory $filtersFactory)
    {
        $this->indexFactory = $searchIndexFactory;
        $this->filtersFactory = $filtersFactory;
    }

    /**
     * Get the search results
     * apply model filters on search results
     * and return paginated data
     *
     * @param Request $request
     * @return Collection|string
     */
    public function handle(Request $request)
    {
        $type = $request->input('type') ?: '';
        $onlyTitle = $request->boolean('onlyTitle') ?: false;
        $searchQuery = $request->input('q') ?: '';

        $index = $this->indexFor(
            $searchQuery,
            $type,
            $onlyTitle
        );
        $filters = $this->filtersFor($type);

        $builder = $index->search($searchQuery);
        $results = $filters->apply($builder);

        return $this->getPaginatedData($results);
    }

    /**
     * Get the search index instance
     *
     * @param string $searchQuery
     * @param string $type
     * @param bool $onlyTitle
     * @return SearchIndexInterface
     */
    public function indexFor(string $searchQuery, string $type, bool $onlyTitle = false)
    {
        return $this->indexFactory
            ->create($searchQuery, $type, $onlyTitle);
    }

    /**
     * Get a model filter instance
     *
     * @param string $type
     * @return FilterManager
     */
    public function filtersFor(string $type)
    {
        return $this->filtersFactory->create($type);
    }

    /**
     * Paginate the data
     *
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedData($results)
    {
        $results = $results->paginate(static::RESULTS_PER_PAGE);

        if (empty($results->items())) {
            return $this->noResults();
        }

        return $results;
    }

    /**
     * Return message when no results are found
     *
     * @return string
     */
    public function noResults()
    {
        return 'No results found.';
    }
}