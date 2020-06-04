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

Route::get('/', function () {
    return view('home');
})->name('home');

// categories
Route::get('/forum/', 'CategoryController@index')->name('forum');
Route::get('/forum/categories/{category}', 'CategoryController@show')->name('forum.categories.show');

//threads
Route::get('/categories/{category}/threads', 'ThreadController@index')->name('threads.index');
Route::group([
    'prefix' => 'api',
    'namespace' => 'Api',
    'name' => 'api',
], function () {

    //threads
    Route::get('/threads/{thread}', 'ThreadController@show')->name('thread.show');
    Route::post('/threads', 'ThreadController@store');
    //replies
    Route::get('/threads/{thread}/replies', 'ReplyController@index')->name('replies.index');

});

Auth::routes();