<?php

namespace App\Search;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use Illuminate\Pagination\LengthAwarePaginator;

class ElasticSearch extends Search
{

    /**
     * Get the results from the database if there are any
     * otherwise return no results message
     *
     * @param \ElasticScoutDriverPlus\Builders\SearchRequestBuilder $searchRequest
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function fetch($searchRequest)
    {
        $searchResults = $searchRequest->paginate(self::RESULTS_PER_PAGE);

        $modelsCollection = $this->map($searchResults);

        $paginatedResults = $this->paginate($searchResults, $modelsCollection);

        $results = $this->appendHasIgnoredContentAttributeAction->execute($paginatedResults);

        if (empty($results['data'])) {
            return $this->noResults();
        }

        return $results;
    }

    /**
     * Get a collection of models from the provided results
     *
     * @param  LengthAwarePaginator $searchResults
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function map($searchResults)
    {
        $mappedIds = [];

        foreach ($searchResults->matches() as $match) {
            $mappedIds[$match->indexName()][] = $match->document()->getContent()['id'];
        }

        $modelsCollection = collect();

        foreach ($mappedIds as $modelIndexName => $modelKeys) {

            $modelCollection = app(ModelsResolver::class)
                ->fromIndexName($modelIndexName)
                ->withSearchInfo()
                ->whereIn('id', $modelKeys)
                ->get();

            $modelsCollection = $modelsCollection->merge($modelCollection);
        }

        return $modelsCollection;
    }

    /**
     * Undocumented function
     *
     * @param collection $results
     * @return LengthAwarePaginator
     */
    private function paginate($searchResults, $eloquentModels)
    {
        return new LengthAwarePaginator(
            $eloquentModels,
            $searchResults->total(),
            $searchResults->perPage(),
            $searchResults->currentPage(),
            ['path' => $searchResults->path()]
        );
    }
}