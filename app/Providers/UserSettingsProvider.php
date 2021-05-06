<?php

namespace App\Providers;

use App\User\Details;
use Illuminate\Support\ServiceProvider;

class UserSettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Details::class, function ($app) {
            if (!auth()->check()) {
                return;
            }
            $user = auth()->user();
            return new Details($user->details, $user);
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