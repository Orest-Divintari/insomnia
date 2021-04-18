<?php

namespace App\Providers;

use App\Avatar\Avatar;
use App\Avatar\AvatarInterface;
use Illuminate\Support\ServiceProvider;

class AvatarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('avatar', function ($app) {
            return app(AvatarInterface::class);
        });

        $this->app->bind(AvatarInterface::class, function ($app) {
            return new Avatar(config('avatar.default'));
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