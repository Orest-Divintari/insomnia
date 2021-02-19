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

// tags
Route::get('/tags/{tag}', 'TagController@show')
    ->name('tags.show');

// online user activity
Route::get('/online/users-activity', 'OnlineUserActivityController@index')
    ->name('online-users-activity.index');

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

    // recently viewed threads
    Route::get('/history', 'RecentlyViewedThreadsController@index')
        ->name('recently-viewed-threads.index');

    Route::get('/messages/{messageId}', 'MessageController@show')
        ->name('messages.show');

    Route::get('/threads/create/{categorySlug}', 'ThreadController@create')
        ->name('threads.create');

    Route::post('/threads', 'ThreadController@store')
        ->middleware('verified', 'throttle.posts')
        ->name('threads.store');

    // conversation

    Route::post('/conversations', 'ConversationController@store')
        ->middleware('verified')
        ->name('conversations.store');

    Route::get('/conversations/create', 'ConversationController@create')
        ->middleware('verified')
        ->name('conversations.create');

    Route::get('/conversations', 'ConversationController@index')
        ->name('conversations.index');

    Route::get('/conversations/{conversation}', 'ConversationController@show')
        ->name('conversations.show');

});