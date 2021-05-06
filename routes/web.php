<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "wWEeb" middleware group. Now create something great!
|
 */
Auth::routes(['verify' => true]);
Route::get('/', function () {
    return view('home');
})->name('home');

/* ************ TAGS ************ */

Route::get('/tags/{tag}', 'TagController@show')
    ->name('tags.show');

/* ************ ONLINE ************ */

Route::get('/online/user-activities', 'OnlineUserActivityController@index')
    ->name('online-user-activities.index');

/* ************ SEARCH ************ */

Route::get('/search', 'SearchController@index')
    ->name('search.index');

Route::get('/search/advanced', 'SearchController@create')
    ->name('search.advanced');

/* ************ CATEGORIES ************ */

Route::get('/forum', 'ForumController@index')
    ->name('forum');

Route::get('/forum/categories/{category}', 'CategoryController@show')
    ->name('categories.show');

/* ************ THREADS ************ */

Route::get('/threads', 'ThreadController@index')
    ->name('threads.index');

Route::get('/categories/{category}/threads/', 'ThreadController@index')
    ->name('category-threads.index');

Route::get('/threads/{thread}', 'ThreadController@show')
    ->name('threads.show');

/* ************ THREADS REPLIES ************ */

Route::get('/replies/{reply}', 'ReplyController@show')
    ->name('replies.show');

/* ************ PROFILE ************ */

Route::get('/profiles/{user}', 'ProfileController@show')
    ->name('profiles.show');

/* ************ PROFILE POSTS ************ */

Route::get('/profile-posts/{post}', 'ProfilePostController@show')
    ->name('profile-posts.show');

/* ************ PROFILE POST COMMENTS ************ */

Route::get('/profile-posts/comments/{comment}', 'CommentController@show')
    ->name('comments.show');

Route::group(['middleware' => 'auth'], function () {

    /* ************ RECENTLY VIEWED THREADS ************ */

    Route::get('/history', 'RecentlyViewedThreadsController@index')
        ->name('recently-viewed-threads.index');

    Route::get('/messages/{messageId}', 'MessageController@show')
        ->name('messages.show');

    /* ************ ACCOUNT  ************ */
    Route::get('/account', 'AccountDetailsController@edit')
        ->name('account');

    /* ************ ACCOUNT FOLLOWS ************ */

    Route::get('/account/follows', 'AccountFollowsController@index')
        ->name('account.follows.index');

    /* ************ ACCOUNT DETAILS ************ */

    Route::patch('/account/details', 'AccountDetailsController@update')
        ->name('account.details.update');

    Route::get('account/details', 'AccountDetailsController@edit')
        ->name('account.details.edit');

    /* ************ THREADS ************ */

    Route::get('/threads/create/{categorySlug}', 'ThreadController@create')
        ->name('threads.create');

    Route::post('/threads', 'ThreadController@store')
        ->middleware('verified', 'throttle.posts')
        ->name('threads.store');

    /* ************ CONVERSATIONS ************ */

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