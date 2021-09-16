<?php

namespace App\Search;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Filters\SearchFilterFactoryInterface;
use App\Models\User;
use App\Search\SearchData;
use App\Search\SearchIndexFactory;
use Illuminate\Http\Request;

abstract class Search
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
     * @var SearchFilterFactoryInterface
     */
    protected $filtersFactory;

    /**
     * The search index factory that is used
     * to get requested the search index
     *
     * @var SearchIndexFactoryInterface
     */
    protected $indexFactory;

    /**
     * Append attribute that determines whether there is ignored content in the search results
     *
     * @param AppendHasIgnoredContentAttributeAction $appendHasIgnoredContentAttributeAction
     */
    protected $appendHasIgnoredContentAttributeAction;

    /**
     * The authenticated user
     *
     * @var User|null
     */
    protected $authUser;

    /**
     * Create a new Search instance
     *
     * @param SearchIndexFactoryInterface $searchIndexFactory
     * @param SearchFilterFactoryInterface $filtersFactory
     */
    public function __construct(
        User | null $authUser,
        SearchIndexFactoryInterface $searchIndexFactory,
        SearchFilterFactoryInterface $filtersFactory,
        AppendHasIgnoredContentAttributeAction $appendHasIgnoredContentAttributeAction

    ) {
        $this->authUser = $authUser;
        $this->indexFactory = $searchIndexFactory;
        $this->filtersFactory = $filtersFactory;
        $this->appendHasIgnoredContentAttributeAction = $appendHasIgnoredContentAttributeAction;
    }

    /**
     * Get the search results
     * apply model filters on search results
     * and return paginated data or no results
     *
     *
     * @param User $user
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

        $searchRequest = $filters->apply($index->search($searchData->query));

        return $this->fetch($searchRequest);
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
     * Return message when no results are found
     *
     * @return string
     */
    public function noResults()
    {
        return 'No results found.';
    }

    /**
     * Get the results from the database if there are any
     * otherwise return no results message
     *
     * @param mixed $searchRequest
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    abstract public function fetch($searchRequest);
}