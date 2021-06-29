<?php

namespace App\Providers;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\SearchRequestFactory;
use App\Search;
use App\Search\ModelFilterFactory;
use App\Search\SearchIndexFactory;
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
        $this->app->bind(Search::class, function ($app) {

            $searchIndexFactory = app(SearchIndexFactory::class);

            $filtersFactory = app(ModelFilterFactory::class);

            $appendHasIgnoredContentAttribute = app(AppendHasIgnoredContentAttributeAction::class);

            return new Search($searchIndexFactory, $filtersFactory, $appendHasIgnoredContentAttribute);
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