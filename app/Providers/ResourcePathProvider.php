<?php

namespace App\Providers;

use App\Helpers\ResourcePath;
use Illuminate\Support\ServiceProvider;

class ResourcePathProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('resourcePath', function () {
            return new ResourcePath;
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