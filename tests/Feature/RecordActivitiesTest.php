<?php

namespace Tests\Feature;

use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;
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

        $this->assertCount(1, $user->fresh()->activities);

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

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class, ['user_id' => $user->id]));

        $this->assertCount(2, $user->fresh()->activities);

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

        $profilePost = create(ProfilePost::class);

        $comment = $profilePost->addComment(
            raw(Reply::class, ['user_id' => $user->id]),
            $user
        );

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

        $this->assertCount(1, $user->activities);

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

        $thread = create(Thread::class);

        $reply = $thread->addReply(
            raw(Reply::class, ['user_id' => $user->id])
        );

        $like = $reply->likedBy($user);

        $this->assertCount(3, $user->activities);

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

        $profilePost = create(ProfilePost::class);

        $comment = $profilePost->addComment(
            raw(Reply::class, ['user_id' => $user->id]),
            $user
        );

        $like = $comment->likedBy($user);

        $this->assertCount(3, $user->activities);

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

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class, ['user_id' => $user->id]));

        $this->assertCount(2, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created-reply',
            'user_id' => $user->id,
        ]);

        $reply->delete();

        $this->assertCount(1, $user->fresh()->activities);
    }

    /** @test */
    public function when_the_user_deletes_a_comment_the_activity_is_deleted()
    {

        $user = $this->signIn();

        $thread = create(ProfilePost::class);

        $comment = $thread->addComment(raw(Reply::class, ['user_id' => $user->id]), $user);

        $this->assertCount(2, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created-comment',
            'user_id' => $user->id,
        ]);

        $comment->delete();

        $this->assertCount(1, $user->fresh()->activities);
    }

    /** @test */
    public function when_a_user_unlikes_a_reply_the_activity_is_deleted()
    {
        $user = $this->signIn();

        $profilePost = create(ProfilePost::class);

        $comment = $profilePost->addComment(
            raw(Reply::class, ['user_id' => $user->id]),
            $user
        );

        $like = $comment->likedBy($user);

        $this->assertCount(3, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);

        $comment->unlikedBy($user);

        $this->assertCount(2, $user->fresh()->activities);
    }

    /** @test */
    public function when_a_user_unlikes_a_comment_the_activity_is_deleted()
    {
        $user = $this->signIn();

        $profilePost = create(ProfilePost::class);

        $comment = $profilePost->addComment(
            raw(Reply::class, ['user_id' => $user->id]),
            $user
        );

        $like = $comment->likedBy($user);

        $this->assertCount(3, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => 'created-comment-like',
            'user_id' => $user->id,
        ]);

        $comment->unlikedBy($user);

        $this->assertCount(2, $user->fresh()->activities);
    }

    /** @test */
    public function when_a_user_deletes_a_profile_post_the_activity_is_deleted()
    {

        $user = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'poster_id' => $user->id,
        ]);

        $this->assertCount(1, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => 'created-profile-post',
            'user_id' => $user->id,
        ]);

        $profilePost->delete();

        $this->assertCount(0, $user->fresh()->activities);
    }

}