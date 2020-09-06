<?php

namespace Tests\Feature;

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
            'type' => 'created_thread_activity',
            'user_id' => $user->id,
        ]);

    }

    /** @test */
    public function when_a_user_posts_a_thread_reply_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $reply = create(Reply::class, ['user_id' => $user->id], $state = 'thread');

        $this->assertCount(1, $user->fresh()->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created_reply_activity',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_posts_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $comment = create(Reply::class, [
            'user_id' => $user->id,
            'repliable_id' => 1,
            'repliable_type' => 'App\ProfilePost',
        ]);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => 'created_comment_activity',
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
            'type' => 'created_profilepost_activity',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function when_a_user_likes_a_thread_reply_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $reply = create(Reply::class, [
            'repliable_id' => 1,
            'repliable_type' => Thread::class,
        ]);

        $reply->likedBy($user);
        $this->assertCount(2, $user->activities);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => 'created_like_activity',
            'user_id' => $user->id,
        ]);

    }

    /** @test */
    public function when_a_user_likes_a_comment_the_activity_is_recorded()
    {
        $user = $this->signIn();

        $reply = create(
            Reply::class,
            [],
            $state = 'profilePost'
        );

        $reply->likedBy($user);

        $this->assertCount(2, $user->activities);

    }
}