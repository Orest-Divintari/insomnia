<?php

namespace App\Providers;

use App\Filters\ExcludeIgnoredFilter;
use App\helpers\Visitor;
use App\Notifications\ThreadHasNewReply;
use App\Reply;
use App\Search\AllPosts;
use App\Search\ProfilePosts;
use App\Search\Threads;
use App\Thread;
use App\User;
use App\ViewModels\LatestPostsViewModel;
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

        View::composer('forum.index', function ($view) {

            $excludeIgnored = app(ExcludeIgnoredFilter::class);
            $latestPosts = app(LatestPostsViewModel::class)->recentlyActiveThreads($excludeIgnored);

            $totalThreads = Thread::count();
            $totalMessages = Reply::count();
            $totalMembers = User::count();

            $view->with(compact('latestPosts', 'totalThreads', 'totalMessages', 'totalMembers'));
        });

        View::composer('components.layouts.master', function ($view) {
            $visitor = Visitor::get();
            $view->with(compact('visitor'));
        });

        Threads::bootSearchable();
        ProfilePosts::bootSearchable();
        AllPosts::bootSearchable();

        Blade::if('verified', function () {
            if (auth()->check()) {
                return auth()->user()->email_verified_at;
            }
        });
    }
}