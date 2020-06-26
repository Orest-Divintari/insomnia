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
    });

    //replies
    Route::get('/threads/{thread}/replies', 'ReplyController@index')
        ->name('api.replies.index');

    Route::get('/categories/{category}/threads', 'ThreadController@index')
        ->name('api.threads.index');
});