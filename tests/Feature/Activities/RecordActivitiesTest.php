<?php

namespace Tests\Feature\Activities;

use App\Events\Activity\UserViewedPage;
use App\Http\Middleware\ThrottlePosts;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecordActivitiesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function when_an_authenticated_user_creates_a_thread_the_activity_is_recorded()
    {
        $user = $this->signIn();
        $threadAttributes = raw(Thread::class, ['user_id' => $user->id]);

        $this->post(route('threads.store'), $threadAttributes);

        $thread = Thread::first();
        $this->assertCount(1, $thread->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => 'created-thread',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_posts_a_thread_reply_the_activity_is_recorded()
    {
        $this->withoutExceptionHandling();
        $thread = create(Thread::class);
        $user = $this->signIn();
        $threadReplyAttributes = raw(
            Reply::class,
            ['repliable_type' => Thread::class]
        );

        $this->post(
            route('ajax.replies.store', $thread),
            $threadReplyAttributes
        );

        $reply = Reply::whereBody($threadReplyAttributes['body'])->first();
        $this->assertCount(1, $reply->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created-reply',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_posts_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();
        $profilePost = create(ProfilePost::class);
        $commentAttributes = raw(
            Reply::class,
            ['repliable_type' => ProfilePost::class]
        );

        $this->post(
            route('ajax.comments.store', $profilePost),
            $commentAttributes
        );

        $comment = Reply::whereBody($commentAttributes['body'])->first();
        $this->assertCount(1, $comment->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_creates_a_profile_post_the_activity_is_recorded()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $profilePostAttributes = raw(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
            ]
        );

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            $profilePostAttributes
        );

        $profilePost = ProfilePost::whereBody($profilePostAttributes['body'])->first();
        $this->assertCount(1, $profilePost->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_likes_a_thread_reply_the_activity_is_recorded()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::create();

        $this->post(route('ajax.reply-likes.store', $reply));

        $like = $reply->likes->first();
        $this->assertCount(1, $like->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-reply-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_likes_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();
        $comment = CommentFactory::create();

        $this->post(route('ajax.reply-likes.store', $comment));

        $like = $comment->likes()->first();
        $this->assertCount(1, $comment->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_likes_a_profile_post_the_activity_is_recorded()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $profilePost = ProfilePostFactory::create();

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        $like = $profilePost->likes()->first();

        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-profile-post-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_reply_is_deleted_the_associated_activity_is_deleted()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::by($user)->create();
        $this->assertCount(1, $reply->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created-reply',
            'user_id' => $user->id,
        ]);

        $this->delete(route('ajax.replies.destroy', $reply));

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created-reply',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_profile_post_comment_is_deleted_the_associated_activity_is_deleted()
    {
        $user = $this->signIn();
        $comment = CommentFactory::by($user)->create();
        $this->assertCount(1, $comment->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);

        $this->delete(route('ajax.comments.destroy', $comment));

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_unlikes_a_reply_the_activity_is_deleted()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::create();
        $like = $reply->like($user);
        $this->assertCount(1, $like->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-reply-like',
            'user_id' => $user->id,
        ]);

        $this->delete(route('ajax.reply-likes.destroy', $reply));

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-reply-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_unlikes_a_comment_the_activity_is_deleted()
    {
        $user = $this->signIn();
        $comment = CommentFactory::create();
        $like = $comment->like($user);
        $this->assertCount(1, $like->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);

        $this->delete(route('ajax.reply-likes.destroy', $comment));

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_profile_post_is_deleted_the_associated_activity_is_deleted()
    {
        $user = $this->signIn();
        $profilePost = ProfilePostFactory::by($user)->create();
        $this->assertCount(1, $profilePost->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);

        $this->delete(route('ajax.profile-posts.destroy', $profilePost));

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_conversation_message_is_created_the_activity_should_not_be_recorded()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $message = ['body' => $this->faker->sentence];

        $this->post(
            route('ajax.messages.store', $conversation),
            $message
        );

        $message = Reply::whereBody($message['body'])->first();
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $message->id,
            'subject_type' => Reply::class,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_conversation_message_is_liked_the_activity_should_not_be_recorded()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $message = $conversation->messages()->first();
        $liker = $this->signIn();

        $this->post(route('ajax.reply-likes.store', $message));

        $like = $message->likes()->first();
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_guest_visits_a_thread_the_activity_is_recorded()
    {
        $thread = create(Thread::class);

        $this->get(route('threads.show', $thread));

        $this->assertDatabaseHas('activities', [
            'user_id' => null,
            'guest_id' => csrf_token(),
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => 'viewed-thread',
            'description' => UserViewedPage::THREAD,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_visits_a_thread_the_activity_is_recorded()
    {
        $this->signIn();
        $thread = create(Thread::class);

        $this->get(route('threads.show', $thread));

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'guest_id' => null,
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => 'viewed-thread',
            'description' => UserViewedPage::THREAD,
        ]);
    }

    /** @test */
    public function when_a_guest_visits_a_category_the_activity_is_recorded()
    {
        $category = create(Category::class);

        $this->get(route('categories.show', $category));

        $this->assertDatabaseHas('activities', [
            'user_id' => null,
            'guest_id' => csrf_token(),
            'subject_id' => $category->id,
            'subject_type' => Category::class,
            'type' => 'viewed-category',
            'description' => UserViewedPage::CATEGORY,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_visits_a_category_the_activity_is_recorded()
    {
        $this->signIn();
        $category = create(Category::class);

        $this->get(route('categories.show', $category));

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'guest_id' => null,
            'subject_id' => $category->id,
            'subject_type' => Category::class,
            'type' => 'viewed-category',
            'description' => UserViewedPage::CATEGORY,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_opens_a_conversation_the_activity_is_recorded()
    {
        $john = $this->signIn();
        $peter = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$peter->name])
            ->create();

        $this->get(route('conversations.show', $conversation));

        $this->assertDatabaseHas('activities', [
            'user_id' => $john->id,
            'guest_id' => null,
            'subject_id' => null,
            'subject_type' => null,
            'type' => 'viewed-page',
            'description' => UserViewedPage::CONVERSATION,
        ]);
    }

    /** @test */
    public function when_a_guest_visits_the_forum_the_activity_is_recorded()
    {
        $this->get(route('forum'));

        $this->assertDatabaseHas('activities', [
            'user_id' => null,
            'guest_id' => csrf_token(),
            'subject_id' => null,
            'subject_type' => null,
            'type' => 'viewed-page',
            'description' => UserViewedPage::FORUM,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_visits_the_forum_the_activity_is_recorded()
    {
        $this->signIn();
        $this->get(route('forum'));

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'guest_id' => null,
            'subject_id' => null,
            'subject_type' => null,
            'type' => 'viewed-page',
            'description' => UserViewedPage::FORUM,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_visits_the_profile_of_another_member_the_activity_is_recorded()
    {
        $this->withoutExceptionHandling();
        $peter = $this->signIn();
        $john = create(User::class);

        $this->get(route('profiles.show', $john));

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'guest_id' => null,
            'subject_id' => $john->id,
            'subject_type' => User::class,
            'type' => 'viewed-user',
            'description' => UserViewedPage::PROFILE,
        ]);
    }

    /** @test */
    public function when_an_authenticated_user_views_the_list_of_conversations()
    {
        $user = $this->signIn();

        $this->get(route('conversations.index'));

        $this->assertDatabaseHas('activities', [
            'user_id' => $user->id,
            'guest_id' => null,
            'subject_id' => null,
            'subject_type' => null,
            'type' => 'viewed-page',
            'description' => UserViewedPage::CONVERSATION,
        ]);
    }
}