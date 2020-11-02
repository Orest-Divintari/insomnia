<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Auth::routes(['verify' => true]);
Route::get('/', function () {
    return view('home');
})->name('home');

//search

Route::get('/search', 'SearchController@show')
    ->name('search.show');

Route::get('/search/advanced', 'SearchController@create')
    ->name('search.advanced');

// categories
Route::get('/forum', 'CategoryController@index')
    ->name('forum');

Route::get('/forum/categories/{category}', 'CategoryController@show')
    ->name('categories.show');

//threads

Route::get('/threads', 'ThreadController@index')
    ->name('filtered-threads.index');

Route::get('/categories/{category}/threads/', 'ThreadController@index')
    ->name('threads.index');

Route::get('/threads/{thread}', 'ThreadController@show')
    ->name('threads.show');

Route::delete('/threads/thread', 'ThreadController@destroy')
    ->name('threads.destroy');

//profile

Route::get('/profiles/{user}', 'ProfileController@show')
    ->name('profiles.show');

// ------- WEB AUTH -------

Route::group(['middleware' => 'auth'], function () {

    Route::get('/threads/create/{categorySlug}', 'ThreadController@create')
        ->name('threads.create');

    Route::post('/threads', 'ThreadController@store')
        ->middleware('verified')
        ->name('threads.store');

    // conversation

    Route::post('/conversations', 'ConversationController@store')
        ->middleware('verified')
        ->name('conversations.store');

    Route::get('/conversations/create', 'ConversationController@create')
        ->middleware('verified')
        ->name('conversations.create');

    Route::get('/conversations/index', 'ConversationController@index')
        ->name('conversations.index')
        ->middleware('verified');

});

// ------ API -------
Route::group([
    'prefix' => 'api',
    'namespace' => 'Api',
    'name' => 'api',
], function () {

    // following
    Route::get('/users/{user}/follows', 'FollowsController@index')
        ->name('api.follows.index');

    // followers
    Route::get('/users/{user}/followedBy', 'FollowedByController@index')
        ->name('api.followedBy.index');

    // latest activity
    Route::get('/profiles/{user}/latestActivity/{postings?}', 'LatestActivityController@index')
        ->name('api.latest-activity.index');

    // about
    Route::get('/profiles/{user}/about', 'AboutController@show')
        ->name('api.about.show');

    // profile posts
    Route::get('/profiles/{user}/posts', 'ProfilePostController@index')
        ->name('api.profile-posts.index');

    Route::get('/categories/{category}/threads', 'ThreadController@index')
        ->name('api.threads.index');

    Route::group(['middleware' => 'auth'], function () {

        // follows
        Route::post('/users/follow/', 'FollowController@store')
            ->name('api.follow.store');

        Route::post('/users/unfollow/', 'FollowController@destroy')
            ->name('api.follow.destroy');

        // comments
        Route::post('/posts/{post}/comments', 'CommentController@store')
            ->name('api.comments.store')
            ->middleware('verified');

        Route::patch('/comments/{comment}', 'CommentController@update')
            ->name('api.comments.update');

        Route::delete('/comments/{comment}', 'CommentController@destroy')
            ->name('api.comments.destroy');

        Route::get('/posts/{post}/comments', 'CommentController@index')
            ->name('api.comments.index');

        // profile posts
        Route::post('/profiles/{user}/posts', 'ProfilePostController@store')
            ->middleware('verified')
            ->name('api.profile-posts.store');

        Route::patch('/profile/posts/{post}', 'ProfilePostController@update')
            ->name('api.profile-posts.update');

        Route::delete('/profile/posts/{post}', 'ProfilePostController@destroy')
            ->name('api.profile-posts.destroy');

        // thread replies
        Route::get('/replies/{reply}', 'ReplyController@show')
            ->name('api.replies.show');

        Route::post('/threads/{thread}/replies', 'ReplyController@store')
            ->name('api.replies.store');

        Route::patch('/replies/{reply}', 'ReplyController@update')
            ->name('api.replies.update');

        Route::delete('/replies/{reply}', 'ReplyController@destroy')
            ->name('api.replies.destroy');

        // threads
        Route::patch('/threads/{thread}', 'ThreadController@update')
            ->name('api.threads.update');

        Route::delete('/threads/{thread}', 'ThreadController@delete')
            ->name('api.threads.update');

        // likes
        Route::post('/replies/{reply}/likes', 'LikeController@store')
            ->name('api.likes.store');

        Route::delete('/replies/{reply}/likes', 'LikeController@destroy')
            ->name('api.likes.destroy');

        // thread subscription
        Route::put('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@update')
            ->name('api.thread-subscriptions.update');

        Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')
            ->name('api.thread-subscriptions.destroy');

        // user notifications
        Route::get('/notifications', 'UserNotificationController@index')
            ->name('api.user-notifications.index');

        Route::delete('/notifications/{notificationId}', 'UserNotificationController@destroy')
            ->name('api.user-notifications.destroy');
    });

});