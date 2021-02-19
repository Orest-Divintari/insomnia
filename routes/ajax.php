<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    // lock threads
    Route::patch('/threads/{thread}/lock', 'LockThreadController@update')
        ->name('lock-threads.update');

    Route::delete('/threads/{thread}/lock', 'LockThreadController@destroy')
        ->name('lock-threads.destroy');

    // pin threads
    Route::patch('/threads/{thread}/pin', 'PinThreadController@update')
        ->name('pin-threads.update');

    Route::delete('/threads/{thread}/pin', 'PinThreadController@destroy')
        ->name('pin-threads.destroy');

    // conversation participants
    Route::post('/conversations/{conversation}/participants', 'ConversationParticipantController@store')
        ->name('conversation-participants.store');

    Route::delete('/conversations/{conversation}/participants/{participantId}', 'ConversationParticipantController@destroy')
        ->name('conversation-participants.destroy');

    // star conversation
    Route::patch('/conversations/{conversation}/star', 'StarConversationController@update')
        ->name('star-conversations.update');

    Route::delete('/conversations/{conversation}/star', 'StarConversationController@destroy')
        ->name('star-conversations.destroy');

    // conversation admin
    Route::patch('/conversations/{conversation}/participants/{participantId}/admin', 'ConversationAdminController@update')
        ->name('conversation-admins.update');

    Route::delete('/conversations/{conversation}/participants/{participantId}/admin', 'ConversationAdminController@destroy')
        ->name('conversation-admins.destroy');

    // read threads
    Route::patch('/threads/{thread}/read', 'ReadThreadController@update')
        ->name('read-threads.update');

    // read conversations
    Route::patch('/conversations/{conversation}/read', 'ReadConversationController@update')
        ->name('read-conversations.update');

    Route::delete('/conversations/{conversation}/read', 'ReadConversationController@destroy')
        ->name('read-conversations.destroy');

    // hide conversation
    Route::patch('/conversations/{conversation}/hide', 'HideConversationController@update')
        ->name('hide-conversations.update');

    // leave conversation
    Route::patch('/conversations/{conversation}/leave', 'LeaveConversationController@update')
        ->name('leave-conversations.update');

    // conversations
    Route::patch('/conversations/{conversation}', 'ConversationController@update')
        ->name('conversations.update');

    Route::get('/conversations', 'ConversationController@index')
        ->name('conversations.index');
    // messages

    Route::get('/messages/{message}', 'MessageController@show')
        ->name('messages.show');

    Route::post('/conversations/{conversation}/messages', 'MessageController@store')
        ->name('messages.store');

    Route::patch('/messages/{message}', 'MessageController@update')
        ->name('messages.update');

    // follows
    Route::post('/users/{user}/follow', 'FollowController@store')
        ->name('follow.store');

    Route::delete('/users/{user}/follow', 'FollowController@destroy')
        ->name('follow.destroy');

    // comments
    Route::post('/posts/{post}/comments', 'CommentController@store')
        ->name('comments.store')
        ->middleware('verified', 'throttle.posts');

    Route::patch('/comments/{comment}', 'CommentController@update')
        ->name('comments.update');

    Route::delete('/comments/{comment}', 'CommentController@destroy')
        ->name('comments.destroy');

    Route::get('/posts/{post}/comments', 'CommentController@index')
        ->name('comments.index');

    // profile posts
    Route::post('/profiles/{user}/posts', 'ProfilePostController@store')
        ->middleware('verified', 'throttle.posts')
        ->name('profile-posts.store');

    Route::patch('/profile/posts/{post}', 'ProfilePostController@update')
        ->name('profile-posts.update');

    Route::delete('/profile/posts/{post}', 'ProfilePostController@destroy')
        ->name('profile-posts.destroy');

    // thread replies
    Route::get('/replies/{reply}', 'ReplyController@show')
        ->name('replies.show');

    Route::post('/threads/{thread}/replies', 'ReplyController@store')
        ->name('replies.store')
        ->middleware('throttle.posts');

    Route::patch('/replies/{reply}', 'ReplyController@update')
        ->name('replies.update');

    Route::delete('/replies/{reply}', 'ReplyController@destroy')
        ->name('replies.destroy');

    // threads
    Route::patch('/threads/{thread}', 'ThreadController@update')
        ->name('threads.update');

    // likes
    Route::post('/replies/{reply}/likes', 'LikeController@store')
        ->name('likes.store');

    Route::delete('/replies/{reply}/likes', 'LikeController@destroy')
        ->name('likes.destroy');

    // thread subscription
    Route::put('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@update')
        ->name('thread-subscriptions.update');

    Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')
        ->name('thread-subscriptions.destroy');

    // user notifications
    Route::get('/notifications', 'UserNotificationController@index')
        ->name('user-notifications.index');

    Route::delete('/notifications/{notificationId}', 'UserNotificationController@destroy')
        ->name('user-notifications.destroy');

});

// profile
Route::get('/profiles/{user}', 'ProfileController@show')
    ->name('profiles.show');

// following
Route::get('/users/{user}/follows', 'FollowsController@index')
    ->name('follows.index');

// followers
Route::get('/users/{user}/followed-by', 'FollowedByController@index')
    ->name('followed-by.index');

// latest activity
Route::get('/profiles/{user}/latest-activity/', 'LatestActivityController@index')
    ->name('latest-activity.index');

// postings
Route::get('/profiles/{user}/postings/', 'PostingActivityController@index')
    ->name('posting-activity.index');

// about
Route::get('/profiles/{user}/about', 'AboutController@show')
    ->name('about.show');

// profile posts
Route::get('/profiles/{user}/posts', 'ProfilePostController@index')
    ->name('profile-posts.index');

Route::get('/categories/{category}/threads', 'ThreadController@index')
    ->name('threads.index');