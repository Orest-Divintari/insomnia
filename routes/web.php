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

Route::get('/threads/{threadSlug}', 'ThreadController@show')
    ->name('threads.show');

/* ************ THREADS REPLIES ************ */

Route::get('/replies/{reply}', 'ReplyController@show')
    ->name('replies.show');

Route::group(['middleware' => 'auth'], function () {

    /* ************ ADMIN ************ */
    Route::group([
        'namespace' => 'Admin',
        'prefix' => 'admin',
        'middleware' => 'role:admin',
    ], function () {

        /* ************ DASHBOARD ************ */
        Route::get('/dashboard', 'DashboardController@index')
            ->name('admin.dashboard.index');

        /* ************ GROUP CATEGORIES ************ */

        Route::get('/group-categories/create', 'GroupCategoryController@create')
            ->name('admin.group-categories.create');

        Route::get('/group-categories', 'GroupCategoryController@index')
            ->name('admin.group-categories.index');

        Route::post('/group-categories', 'GroupCategoryController@store')
            ->name('admin.group-categories.store');

        Route::patch('/group-categories/{groupCategory}', 'GroupCategoryController@update')
            ->name('admin.group-categories.update');

        Route::get('/group-categories/{groupCategory}/edit', 'GroupCategoryController@edit')
            ->name('admin.group-categories.edit');

        /* ************ CATEGORIES ************ */

        Route::get('/categories/create', 'CategoryController@create')
            ->name('admin.categories.create');

        Route::get('/categories', 'CategoryController@index')
            ->name('admin.categories.index');

        Route::post('/categories', 'CategoryController@store')
            ->name('admin.categories.store');

        Route::patch('/categories/{category}', 'CategoryController@update')
            ->name('admin.categories.update');

        Route::get('/categories/{category}/edit', 'CategoryController@edit')
            ->name('admin.categories.edit');
    });

    /* ************ ONLINE ************ */

    Route::get('/online/user-activities', 'OnlineUserActivityController@index')
        ->middleware('must-be-verified')
        ->name('online-user-activities.index');

    /* ************ PROFILE ************ */

    Route::get('/profiles/{user}', 'ProfileController@show')
        ->name('profiles.show');

    /* ************ PROFILE POSTS ************ */

    Route::get('/profile-posts/{post}', 'ProfilePostController@show')
        ->name('profile-posts.show');

    Route::get('/profile-posts', 'ProfilePostController@index')
        ->middleware('must-be-verified')
        ->name('profile-posts.index');

    /* ************ PROFILE POST COMMENTS ************ */

    Route::get('/profile-posts/comments/{comment}', 'CommentController@show')
        ->name('comments.show');

    /* ************ RECENTLY VIEWED THREADS ************ */

    Route::get('/history', 'RecentlyViewedThreadsController@index')
        ->name('recently-viewed-threads.index');

    Route::get('/messages/{messageId}', 'MessageController@show')
        ->name('messages.show');

    /* ************ ACCOUNT  ************ */
    Route::get('/account', 'AccountDetailsController@edit')
        ->name('account');

    /* ************ ACCOUNT FOLLOWS ************ */

    Route::get('/account/followings', 'AccountFollowingsController@index')
        ->name('account.followings.index');

    /* ************ ACCOUNT LIKES ************ */

    Route::get('/account/likes', 'AccountLikeController@index')
        ->name('account.likes.index');

    /* ************ ACCOUNT IGNORED ************ */

    Route::get('/account/ignored-users', 'AccountIgnoredUserController@index')
        ->name('account.ignored-users.index');

    Route::get('/account/ignored-threads', 'AccountIgnoredThreadController@index')
        ->name('account.ignored-threads.index');

    /* ************ ACCOUNT DETAILS ************ */

    Route::patch('/account/details', 'AccountDetailsController@update')
        ->name('account.details.update');

    Route::get('/account/details', 'AccountDetailsController@edit')
        ->name('account.details.edit');

    /* ************ ACCOUNT PRIVACY ************ */

    Route::patch('/account/privacy', 'AccountPrivacyController@update')
        ->name('account.privacy.update');

    Route::get('/account/privacy', 'AccountPrivacyController@edit')
        ->name('account.privacy.edit');

    /* ************ ACCOUNT PREFERENCES ************ */

    Route::patch('/account/preferences', 'AccountPreferenceController@update')
        ->name('account.preferences.update');

    Route::get('/account/preferences', 'AccountPreferenceController@edit')
        ->name('account.preferences.edit');

    /* ************ ACCOUNT PASSWORD ************ */

    Route::patch('/account/password', 'AccountPasswordController@update')
        ->name('account.password.update');

    Route::get('/account/password', 'AccountPasswordController@edit')
        ->name('account.password.edit');

    /* ************ ACCOUNT PASSWORD ************ */

    Route::get('/account/notifications', 'AccountNotificationsController@index')
        ->name('account.notifications.index');

    /* ************ THREADS ************ */

    Route::get('/categories/{category}/threads/create', 'ThreadController@create')
        ->middleware('must-be-verified')
        ->name('threads.create');

    Route::post('/threads', 'ThreadController@store')
        ->middleware('throttle.posts')
        ->name('threads.store');

    /* ************ CONVERSATIONS ************ */

    Route::post('/conversations', 'ConversationController@store')
        ->name('conversations.store');

    Route::get('/conversations/create', 'ConversationController@create')
        ->middleware('must-be-verified')
        ->name('conversations.create');

    Route::get('/conversations', 'ConversationController@index')
        ->name('conversations.index');

    Route::get('/conversations/{conversation}', 'ConversationController@show')
        ->name('conversations.show');

});