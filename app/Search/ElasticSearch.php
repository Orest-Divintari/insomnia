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
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function fetch($searchRequest)
    {
        $searchResults = $searchRequest->paginate(self::RESULTS_PER_PAGE);

        $mappedIds = [];

        foreach ($searchResults->matches() as $match) {
            $mappedIds[$match->indexName()][] = $match->document()->getContent()['id'];
        }

        $eloquentModels = collect();

        foreach ($mappedIds as $modelIndexName => $modelKeys) {
            $modelCollection = app(ModelsResolver::class)
                ->fromIndexName($modelIndexName)
                ->withSearchInfo()
                ->whereIn('id', $modelKeys)
                ->get();

            $eloquentModels = $eloquentModels->merge($modelCollection);
        }

        $paginatedResults = $this->paginate($searchResults, $eloquentModels);

        $results = $this->appendHasIgnoredContentAttributeAction->execute($paginatedResults);

        if (empty($results['data'])) {
            return $this->noResults();
        }

        return $results;
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