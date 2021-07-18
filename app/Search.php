<?php

namespace App;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Search\ModelFilterFactory;
use App\Search\SearchData;
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
     * Append attribute that determines whether there is ignored content in the search results
     *
     * @param AppendHasIgnoredContentAttributeAction $appendHasIgnoredContentAttributeAction
     */
    protected $appendHasIgnoredContentAttributeAction;

    /**
     * Create a new Search instance
     *
     * @param SearchIndexFactory $searchIndexFactory
     * @param ModelFilterFactory $filtersFactory
     */
    public function __construct(
        SearchIndexFactory $searchIndexFactory,
        ModelFilterFactory $filtersFactory,
        AppendHasIgnoredContentAttributeAction $appendHasIgnoredContentAttributeAction
    ) {
        $this->indexFactory = $searchIndexFactory;
        $this->filtersFactory = $filtersFactory;
        $this->appendHasIgnoredContentAttributeAction = $appendHasIgnoredContentAttributeAction;
    }

    /**
     * Get the search results
     * apply model filters on search results
     * and return paginated data or no results
     *
     * @param Request $request
     * @return Collection|string
     */
    public function handle(SearchData $searchData)
    {
        $index = $this->indexFor(
            $searchData->query,
            $searchData->type,
            $searchData->onlyTitle
        );

        $filters = $this->filtersFor($searchData->type);

        $builder = $index->search($searchData->query);

        $results = $filters->apply($builder);

        return $this->fetch($results);
    }

    /**
     * Get the search index instance
     *
     * @param mixed $searchQuery
     * @param string $type
     * @param bool $onlyTitle
     * @return SearchIndexInterface
     */
    public function indexFor($searchQuery, string $type, bool $onlyTitle = false)
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
     * Get the results from the database if there are any
     * otherwise return no results message
     *
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function fetch($results)
    {
        $results = $results->paginate(static::RESULTS_PER_PAGE);

        $results = $this->appendHasIgnoredContentAttributeAction->execute($results);

        if (empty($results['data'])) {
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