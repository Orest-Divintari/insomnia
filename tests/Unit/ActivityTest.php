<?php

namespace Tests\Unit;

use App\Activity;
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
    public function user_can_fetch_the_thread_activity()
    {
        $user = $this->signIn();

        $thread = create(Thread::class, [
            'user_id' => $user->id,
        ]);

        $reply = $thread->addReply(raw(Reply::class, ['user_id' => $user->id]));

        $reply->likedBy($user);

        $activity = Activity::feed($user)->paginate(Activity::NUMBER_OF_ACTIVITIES);

        $this->assertCount(3, $activity);
    }

    /** @test */
    public function a_user_can_get_the_activity_of_threads_replies_profile_posts_and_comments()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = ReplyFactory::create(['repliable_id' => $thread->id]);
        $profilePost = create(ProfilePost::class);
        $comment = CommentFactory::create(['repliable_id' => $profilePost->id]);
        $reply->likedBy($user);
        $comment->likedBy($user);
        $numberOfTotalActivities = 6;
        $numberOfPostingActivities = 4;
        $this->assertCount($numberOfTotalActivities, Activity::all());
        $this->assertCount($numberOfPostingActivities, Activity::ofAllPosts()->get());
    }

    /** @test */
    public function a_user_can_get_the_activity_of_threads()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = ReplyFactory::create(['repliable_id' => $thread->id]);
        $profilePost = create(ProfilePost::class);
        $comment = CommentFactory::create(['repliable_id' => $profilePost->id]);
        $reply->likedBy($user);
        $comment->likedBy($user);
        $numberOfTotalActivities = 6;
        $numberOfThreadActivities = 1;
        $this->assertCount($numberOfTotalActivities, Activity::all());
        $this->assertCount($numberOfThreadActivities, Activity::ofThreads()->get());
    }
}