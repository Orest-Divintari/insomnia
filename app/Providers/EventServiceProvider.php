<?php

namespace App\Providers;

use App\Category;
use App\Conversation;
use App\GroupCategory;
use App\Observers\CategoryObserver;
use App\Observers\ConversationObserver;
use App\Observers\GroupCategoryObserver;
use App\Observers\MessageObserver;
use App\Observers\ProfilePostObserver;
use App\Observers\ReplyObserver;
use App\Observers\SubscribeToThreadObserver;
use App\Observers\ThreadReplyObserver;
use App\Observers\UserObserver;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Subscription\NewReplyWasPostedToThread' => [
            'App\Listeners\Subscription\SubscribeToThread',
        'App\Listeners\Subscription\NotifyThreadSubscribers',
        ],
        'App\Events\Profile\NewCommentWasAddedToProfilePost' => [
            'App\Listeners\Profile\NotifyPostParticipantsOfNewComment',
            'App\Listeners\Profile\NotifyProfileOwnerOfNewCommentOnAPost',
            'App\Listeners\Profile\NotifyProfileOwnerOfNewCommentOnTheirPost',
            'App\Listeners\Profile\NotifyProfilePostOwnerOfNewComment',
        ],
        'App\Events\Profile\NewPostWasAddedToProfile' => [
            'App\Listeners\Profile\NotifyProfileOwnerOfNewPost',
        ],

        'App\Events\Subscription\ReplyWasLiked' => [
            'App\Listeners\Subscription\NotifyReplyPoster',
        ],
        'App\Events\Profile\CommentWasLiked' => [
            'App\Listeners\Profile\NotifyCommentPoster',
        ],
        'App\Events\Conversation\MessageWasLiked' => [
            'App\Listeners\Conversation\NotifyMessagePoster',
        ],
        'App\Events\Conversation\NewMessageWasAddedToConversation' => [
            'App\Listeners\Conversation\NotifyConversationParticipants',
        ],
        'App\Events\Conversation\NewParticipantsWereAdded' => [
            'App\Listeners\Conversation\MarkConversationAsUnread',
        ],
        'App\Events\Conversation\ParticipantWasRemoved' => [
            'App\Listeners\Conversation\DeleteConversationReadRecord',
        ],
        'App\Events\Follow\AUserStartedFollowingYou' => [
            'App\Listeners\Follow\NotifyFollowingUser',
        ],
        'App\Events\Follow\AUserUnfollowedYou' => [
            'App\Listeners\Follow\DeleteFollowNotification',
        ],
        'App\Events\Like\ReplyWasUnliked' => [
            'App\Listeners\Like\DeleteReplyLikeNotification',
        ],
        'App\Events\Activity\UserViewedPage' => [
            'App\Listeners\Activity\LogUserActivity',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\Activity\DeleteUserViewedActivity',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        ProfilePost::observe(ProfilePostObserver::class);
        Conversation::observe(ConversationObserver::class);
        Category::observe(CategoryObserver::class);
        GroupCategory::observe(GroupCategoryObserver::class);
        User::observe(UserObserver::class);
        Reply::observe(ReplyObserver::class);
        Reply::observe(ThreadReplyObserver::class);
        Reply::observe(MessageObserver::class);
        Thread::observe(SubscribeToThreadObserver::class);

    }
}