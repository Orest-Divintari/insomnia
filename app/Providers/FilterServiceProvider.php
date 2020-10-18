<?php

namespace App\Providers;

use App\Filters\FilterManager;
use App\Filters\ModelFilterChain;
use App\Search\ModelFilterFactory;
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
            $modelFilterChain = new ModelFilterChain();
            return new FilterManager($modelFilterChain);
        });

        $this->app->bind(ModelFilterFactory::class, function ($app) {
            $filterManager = app(FilterManager::class);
            return new ModelFilterFactory($filterManager);
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