<?php

namespace App\Providers;

use App\Filters\ElasticSearchFilterFactory;
use App\Filters\FilterChain;
use App\Filters\FilterManager;
use App\Filters\ReplyFilters;
use App\Filters\SearchFilterFactoryInterface;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // $this->app->bind(ThreadFilters::class, function ($app) {
        //     $filterManager = new FilterManager();
        //     return $filterManager->addFilter(ThreadFilters::class);
        // });

        // $this->app->bind(ReplyFilters::class, function ($app) {
        //     $filterManager = new FilterManager();
        //     return $filterManager->addFilter(ReplyFilters::class);
        // });

        // $this->app->bind(ProfilePostFilters::class, function ($app) {
        //     $filterManager = new FilterManager();
        //     return $filterManager->addFilter(ProfilePostFilters::class);
        // });

        // $this->app->bind(AllPostsFilter::class, function ($app) {
        //     $filterManager = new FilterManager();
        //     return $filterManager->addFilter(ProfilePostFilters::class)
        //         ->addFilter(ThreadFilters::class);

        // });

        $this->app->bind(FilterManager::class, function ($app) {
            $filterChain = new FilterChain();
            return new FilterManager($filterChain);
        });

        $this->app->bind(SearchFilterFactoryInterface::class, function ($app) {
            $filterManager = app(FilterManager::class);
            return new ElasticSearchFilterFactory($filterManager);
        });

        $this->app->bind(ReplyFilters::class, function ($app) {
            return app(FilterManager::class)->withReplyFilters();
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