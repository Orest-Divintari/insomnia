<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'visitor.append'], function () {

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

        Route::get('/profiles/{user}/posts', 'ProfilePostController@index')
            ->name('profile-posts.index');

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

        /* ************ POSTING ACTIVITIES ************ */

        Route::get('/profiles/{user}/postings/', 'PostingActivityController@index')
            ->name('posting-activity.index');

        /* ************ LATEST ACTIVITIES ************ */

        Route::get('/profiles/{user}/latest-activity/', 'LatestActivityController@index')
            ->name('latest-activity.index');

        /* ************ FOLLOWS ************ */

        Route::get('/users/{user}/followings', 'FollowingsController@index')
            ->name('followings.index');

        Route::get('/users/{user}/followers', 'FollowerController@index')
            ->name('followers.index');

        /* ************ PROFILE ABOUT ************ */

        Route::get('/profiles/{user}/about', 'AboutController@show')
            ->name('about.show');

        /* ************ LIKES ************ */

        Route::post('/replies/{reply}/likes', 'ReplyLikeController@store')
            ->name('reply-likes.store');

        Route::delete('/replies/{reply}/likes', 'ReplyLikeController@destroy')
            ->name('reply-likes.destroy');

        Route::post('/profile-posts/{profilePost}/likes', 'ProfilePostLikeController@store')
            ->name('profile-post-likes.store');

        Route::delete('/profile-posts/{profilePost}/likes', 'ProfilePostLikeController@destroy')
            ->name('profile-post-likes.destroy');

        /* ************ THREAD SUBSCRIPTION ************ */

        Route::put('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@update')
            ->name('thread-subscriptions.update');

        Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')
            ->name('thread-subscriptions.destroy');

        /* ************ USER NOTIFICATIONS ************ */

        Route::get('/notifications', 'UserNotificationController@index')
            ->name('user-notifications.index');

        Route::patch('/notifications/{notification}/read', 'ReadNotificationController@update')
            ->name('read-notifications.update');

        Route::delete('/notifications/{notification}/read', 'ReadNotificationController@destroy')
            ->name('read-notifications.destroy');

        Route::delete('/notifications/read', 'ReadAllNotificationsController@destroy')
            ->name('read-all-notifications.destroy');

        /* ************ USER AVATAR ************ */

        Route::patch('/users/{user}/avatar', 'UserAvatarController@update')
            ->name('user-avatar.update');

        Route::delete('/users/{user}/avatar', 'UserAvatarController@destroy')
            ->name('user-avatar.destroy');

        /* ************ USER EMAIL ************ */

        Route::patch('/users/{user}/email', 'UserEmailController@update')
            ->name('user-email.update');

        /* ************ IGNORED ************ */

        Route::post('/users/{user}/ignoration', 'UserIgnorationController@store')
            ->name('user-ignorations.store');

        Route::delete('/users/{user}/ignoration', 'UserIgnorationController@destroy')
            ->name('user-ignorations.destroy');

        Route::post('/threads/{thread}/ignoration', 'ThreadIgnorationController@store')
            ->name('thread-ignorations.store');

        Route::delete('/threads/{thread}/ignoration', 'ThreadIgnorationController@destroy')
            ->name('thread-ignorations.destroy');

    });

/* ************ SEARCH NAMES ************ */

    Route::get('/search/names/{name}', 'SearchNamesController@index')
        ->name('search.names.index');

/* ************ PROFILE ************ */

    Route::get('/profiles/{user}', 'ProfileController@show')
        ->name('profiles.show');

/* ************ THREADS ************ */

    Route::get('/categories/{category}/threads', 'ThreadController@index')
        ->name('threads.index');

});