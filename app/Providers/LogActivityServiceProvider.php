<?php

namespace App\Providers;

use App\Actions\ActivityLogger;
use App\Actions\LogOnlineUserActivityAction;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class LogActivityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LogOnlineUserActivityAction::class, function ($app) {
            return new LogOnlineUserActivityAction(
                new ActivityLogger,
                app(Request::class)
            );
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