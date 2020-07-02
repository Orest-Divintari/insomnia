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

// categories
Route::get('/forum', 'CategoryController@index')
    ->name('forum');

Route::get('/forum/categories/{category}', 'CategoryController@show')
    ->name('categories.show');

//threads
Route::get('/categories/{category}/threads/', 'ThreadController@index')
    ->name('threads.index');

Route::get('/threads/{thread}', 'ThreadController@show')
    ->name('threads.show');

// ------- WEB AUTH -------

Route::group(['middleware' => 'auth'], function () {

    Route::get('/threads/create/{categoryId}', 'ThreadController@create')
        ->name('threads.create');

    Route::post('/threads', 'ThreadController@store')
        ->middleware('verified')
        ->name('threads.store');

});

// ------ API -------
Route::group([
    'prefix' => 'api',
    'namespace' => 'Api',
    'name' => 'api',
], function () {

    Route::group(['middleware' => 'auth'], function () {

        Route::get('/replies/{reply}', 'ReplyController@show')
            ->name('api.replies.show');

        Route::post('/threads/{thread}/replies', 'ReplyController@store')
            ->name('api.replies.store');

        Route::patch('/replies/{reply}', 'ReplyController@update')
            ->name('api.replies.update');

        Route::delete('/replies/{reply}', 'ReplyController@destroy')
            ->name('api.replies.destroy');

        Route::patch('/threads/{thread}', 'ThreadController@update')
            ->name('api.threads.update');

        Route::delete('/threads/{thread}', 'ThreadController@delete')
            ->name('api.threads.update');

        Route::post('/replies/{reply}/likes', 'LikeController@store')
            ->name('api.likes.store');

        Route::delete('/replies/{reply}/likes', 'LikeController@destroy')
            ->name('api.likes.destroy');

        Route::post('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@store')
            ->name('api.thread-subscriptions.store');

        Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')
            ->name('api.thread-subscriptions.destroy');

        Route::get('/notifications', 'UserNotificationController@index')
            ->name('api.user-notifications.index');

        Route::delete('/notifications/{notificationId}', 'UserNotificationController@destroy')
            ->name('api.user-notifications.destroy');
    });

    Route::get('/categories/{category}/threads', 'ThreadController@index')
        ->name('api.threads.index');
});