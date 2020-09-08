<?php

namespace Tests\Feature;

use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_creates_a_thread_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $thread = create(Thread::class, [
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $thread->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => 'created-thread',
            'user_id' => $user->id,
        ]);

    }

    /** @test */
    public function when_a_user_posts_a_thread_reply_the_activity_is_recorded()
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
    }

    /** @test */
    public function when_a_user_posts_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $comment = CommentFactory::by($user)->create();

        $this->assertCount(2, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_creates_a_profile_post_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'poster_id' => $user->id,
        ]);

        $this->assertCount(1, $profilePost->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_likes_a_thread_reply_the_activity_is_recorded()
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

    }

    /** @test */
    public function when_a_user_likes_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $comment = CommentFactory::create();

        $like = $comment->likedBy($user);

        $this->assertCount(1, $comment->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_the_user_deletes_a_reply_the_activity_is_deleted()
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

        $reply->delete();

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created-reply',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_the_user_deletes_a_comment_the_activity_is_deleted()
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

        $comment->delete();

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_unlikes_a_reply_the_activity_is_deleted()
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

        $reply->unlikedBy($user);

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-reply-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_unlikes_a_comment_the_activity_is_deleted()
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

        $comment->unlikedBy($user);

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_deletes_a_profile_post_the_activity_is_deleted()
    {
        $user = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'poster_id' => $user->id,
        ]);

        $this->assertCount(1, $profilePost->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);

        $profilePost->delete();

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);

    }

}