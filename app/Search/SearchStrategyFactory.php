<?php

namespace App\Search;

use App\Search\Algolia\AlgoliaSearchStrategyFactory;
use App\Search\Database\DatabaseSearchStrategyFactory;
use Illuminate\Http\Request;

class SearchStrategyFactory implements SearchStrategyFactoryInterface
{

    /**
     * Get the requested search strategy instance
     *
     * @param Request $request
     * @return SearchStrategyInterface
     */
    public function create(Request $request)
    {
        if ($request->missing('q')) {
            return app(DatabaseSearchStrategyFactory::class)
                ->create($request);
        } else {
            return app(AlgoliaSearchStrategyFactory::class)
                ->create($request);
        }

    }

}