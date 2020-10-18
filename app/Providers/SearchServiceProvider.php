<?php

namespace App\Providers;

use App\Search;
use App\Search\ModelFilterFactory;
use App\Search\SearchStrategyFactory;
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

            $searchStrategyFactory = app(SearchStrategyFactory::class);

            $filtersFactory = app(ModelFilterFactory::class);

            return new Search($searchStrategyFactory, $filtersFactory);
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