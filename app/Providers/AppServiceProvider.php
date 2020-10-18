<?php

namespace App\Providers;

use App\Notifications\ThreadHasNewReply;
use App\Reply;
use App\Search\AllPosts;
use App\Search\ProfilePosts;
use App\Search\Threads;
use App\Thread;
use App\User;
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

        View::composer('categories.index', function ($view) {
            $latestPosts = Thread::with('category')->withRecentReply()
                ->has('replies')
                ->latest('updated_at')
                ->take(10)
                ->get();

            $totalThreads = Thread::count();
            $totalMessages = Reply::count();
            $totalMembers = User::count();

            $view->with(compact('latestPosts', 'totalThreads', 'totalMessages', 'totalMembers'));
        });

        Threads::bootSearchable();
        ProfilePosts::bootSearchable();
        AllPosts::bootSearchable();
    }
}