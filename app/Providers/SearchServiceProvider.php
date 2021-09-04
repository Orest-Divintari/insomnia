<?php

namespace App\Providers;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\SearchRequestFactory;
use App\Search\ElasticSearch;
use App\Search\ElasticSearchIndexFactory;
use App\Search\ModelFilterFactory;
use App\Search\Search;
use App\Search\SearchIndexFactoryInterface;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SearchIndexFactoryInterface::class, ElasticSearchIndexFactory::class);

        $this->app->bind(Search::class, function ($app) {

            $searchIndexFactory = app(SearchIndexFactoryInterface::class);

            $filtersFactory = app(ModelFilterFactory::class);

            $appendHasIgnoredContentAttribute = new AppendHasIgnoredContentAttributeAction;

            return new ElasticSearch($searchIndexFactory, $filtersFactory, $appendHasIgnoredContentAttribute);
        });

        $this->app->bind(SearchRequest::class, function ($app) {

            return app(SearchRequestFactory::class)->create();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}