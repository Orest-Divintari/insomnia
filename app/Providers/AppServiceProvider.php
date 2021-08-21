<?php

namespace App\Providers;

use App\helpers\Visitor;
use App\Models\User;
use App\Notifications\ThreadHasNewReply;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ThreadHasNewReply::class, function ($app, $params) {
            return new ThreadHasNewReply($params['thread'], $params['reply']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('components.layouts.master', function ($view) {
            $visitor = Visitor::get();
            $view->with(compact('visitor'));
        });

        // Threads::bootSearchable();
        // ProfilePosts::bootSearchable();
        // AllPosts::bootSearchable();

        Blade::if('verified', function () {
            if (auth()->check()) {
                return auth()->user()->email_verified_at;
            }
        });
    }
}