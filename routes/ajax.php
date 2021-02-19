<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    /* ************ THREADS ************ */

    Route::patch('/threads/{thread}', 'ThreadController@update')
        ->name('threads.update');

    Route::patch('/threads/{thread}/lock', 'LockThreadController@update')
        ->name('lock-threads.update');

    Route::delete('/threads/{thread}/lock', 'LockThreadController@destroy')
        ->name('lock-threads.destroy');

    Route::patch('/threads/{thread}/pin', 'PinThreadController@update')
        ->name('pin-threads.update');

    Route::delete('/threads/{thread}/pin', 'PinThreadController@destroy')
        ->name('pin-threads.destroy');

    Route::patch('/threads/{thread}/read', 'ReadThreadController@update')
        ->name('read-threads.update');

    // ************ THREAD REPLIES ************

    Route::get('/replies/{reply}', 'ReplyController@show')
        ->name('replies.show');

    Route::post('/threads/{thread}/replies', 'ReplyController@store')
        ->name('replies.store')
        ->middleware('throttle.posts');

    Route::patch('/replies/{reply}', 'ReplyController@update')
        ->name('replies.update');

    Route::delete('/replies/{reply}', 'ReplyController@destroy')
        ->name('replies.destroy');

    /* ************ CONVERSATIONS ************ */

    Route::patch('/conversations/{conversation}', 'ConversationController@update')
        ->name('conversations.update');

    Route::get('/conversations', 'ConversationController@index')
        ->name('conversations.index');

    Route::post('/conversations/{conversation}/participants', 'ConversationParticipantController@store')
        ->name('conversation-participants.store');

    Route::delete('/conversations/{conversation}/participants/{participantId}', 'ConversationParticipantController@destroy')
        ->name('conversation-participants.destroy');

    Route::patch('/conversations/{conversation}/star', 'StarConversationController@update')
        ->name('star-conversations.update');

    Route::delete('/conversations/{conversation}/star', 'StarConversationController@destroy')
        ->name('star-conversations.destroy');

    Route::patch('/conversations/{conversation}/participants/{participantId}/admin', 'ConversationAdminController@update')
        ->name('conversation-admins.update');

    Route::delete('/conversations/{conversation}/participants/{participantId}/admin', 'ConversationAdminController@destroy')
        ->name('conversation-admins.destroy');

    Route::patch('/conversations/{conversation}/read', 'ReadConversationController@update')
        ->name('read-conversations.update');

    Route::delete('/conversations/{conversation}/read', 'ReadConversationController@destroy')
        ->name('read-conversations.destroy');

    Route::patch('/conversations/{conversation}/hide', 'HideConversationController@update')
        ->name('hide-conversations.update');

    Route::patch('/conversations/{conversation}/leave', 'LeaveConversationController@update')
        ->name('leave-conversations.update');

    /* ************ CONVERSATION MESSAGES ************ */

    Route::get('/messages/{message}', 'MessageController@show')
        ->name('messages.show');

    Route::post('/conversations/{conversation}/messages', 'MessageController@store')
        ->name('messages.store');

    Route::patch('/messages/{message}', 'MessageController@update')
        ->name('messages.update');

    /* ************ FOLLOWS ************ */

    Route::post('/users/{user}/follow', 'FollowController@store')
        ->name('follow.store');

    Route::delete('/users/{user}/follow', 'FollowController@destroy')
        ->name('follow.destroy');

    // ************ PROFILE POSTS ************

    Route::post('/profiles/{user}/posts', 'ProfilePostController@store')
        ->middleware('verified', 'throttle.posts')
        ->name('profile-posts.store');

    Route::patch('/profile/posts/{post}', 'ProfilePostController@update')
        ->name('profile-posts.update');

    Route::delete('/profile/posts/{post}', 'ProfilePostController@destroy')
        ->name('profile-posts.destroy');

    /* ************ PROFILE POST COMMENTS ************ */

    Route::post('/posts/{post}/comments', 'CommentController@store')
        ->name('comments.store')
        ->middleware('verified', 'throttle.posts');

    Route::patch('/comments/{comment}', 'CommentController@update')
        ->name('comments.update');

    Route::delete('/comments/{comment}', 'CommentController@destroy')
        ->name('comments.destroy');

    Route::get('/posts/{post}/comments', 'CommentController@index')
        ->name('comments.index');

    /* ************ LIKES ************ */

    Route::post('/replies/{reply}/likes', 'LikeController@store')
        ->name('likes.store');

    Route::delete('/replies/{reply}/likes', 'LikeController@destroy')
        ->name('likes.destroy');

    /* ************ THREAD SUBSCRIPTION ************ */

    Route::put('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@update')
        ->name('thread-subscriptions.update');

    Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')
        ->name('thread-subscriptions.destroy');

    /* ************ USER NOTIFICATIONS ************ */

    Route::get('/notifications', 'UserNotificationController@index')
        ->name('user-notifications.index');

    Route::delete('/notifications/{notificationId}', 'UserNotificationController@destroy')
        ->name('user-notifications.destroy');

});

/* ************ PROFILE ************ */

Route::get('/profiles/{user}', 'ProfileController@show')
    ->name('profiles.show');

Route::get('/profiles/{user}/about', 'AboutController@show')
    ->name('about.show');

/* ************ FOLLOWS ************ */

Route::get('/users/{user}/follows', 'FollowsController@index')
    ->name('follows.index');

Route::get('/users/{user}/followed-by', 'FollowedByController@index')
    ->name('followed-by.index');

/* ************ LATEST ACTIVITIES ************ */

Route::get('/profiles/{user}/latest-activity/', 'LatestActivityController@index')
    ->name('latest-activity.index');

/* ************ POSTING ACTIVITIES ************ */

Route::get('/profiles/{user}/postings/', 'PostingActivityController@index')
    ->name('posting-activity.index');

/* ************ PROFILE POSTS ************ */

Route::get('/profiles/{user}/posts', 'ProfilePostController@index')
    ->name('profile-posts.index');

Route::get('/categories/{category}/threads', 'ThreadController@index')
    ->name('threads.index');