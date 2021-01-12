<?php

namespace Tests\Feature\Activities;

use App\Http\Middleware\ThrottlePosts;
use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
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
        $thread = create(Thread::class);
        $user = $this->signIn();
        $threadReplyAttributes = raw(
            Reply::class,
            ['repliable_type' => Thread::class]
        );

        $this->post(
            route('api.replies.store', $thread),
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
            route('api.comments.store', $profilePost),
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
            route('api.profile-posts.store', $profileOwner),
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

        $this->post(route('api.likes.store', $reply));

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

        $this->post(route('api.likes.store', $comment));

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

        $this->delete(route('api.replies.destroy', $reply));

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

        $this->delete(route('api.comments.destroy', $comment));

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
        $like = $reply->likedBy($user);
        $this->assertCount(1, $like->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-reply-like',
            'user_id' => $user->id,
        ]);

        $this->delete(route('api.likes.destroy', $reply));

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
        $like = $comment->likedBy($user);
        $this->assertCount(1, $like->activities);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);

        $this->delete(route('api.likes.destroy', $comment));

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

        $this->delete(route('api.profile-posts.destroy', $profilePost));

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
            route('api.messages.store', $conversation),
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

        $this->post(route('api.likes.store', $message));

        $like = $message->likes()->first();
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'user_id' => $user->id,
        ]);
    }
}