<?php

namespace Tests\Feature\Activities;

use App\Activity;
use App\Like;
use App\ProfilePost;
use App\Reply;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteActivitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_profile_post_is_deleted_the_associated_activities_are_deleted()
    {
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)->create();
        $this->assertCount(1, $profilePost->activities);

        $this->delete(route('ajax.profile-posts.destroy', $profilePost->id));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => ProfilePost::class,
                'subject_id' => $profilePost->id]
            )->count()
        );
    }

    /** @test */
    public function when_a_profile_post_comment_is_deleted_the_associated_activities_are_deleted()
    {
        $poster = $this->signIn();
        $comment = CommentFactory::by($poster)->create();
        $this->assertCount(1, $comment->activities);

        $this->delete(route('ajax.comments.destroy', $comment));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => Reply::class,
                'subjecT_id' => $comment->id,
            ])->count()
        );
    }

    /** @test */
    public function when_a_profile_post_comment_is_unliked_the_associated_activities_are_deleted()
    {
        $comment = CommentFactory::create();
        $liker = $this->signIn();

        $like = $comment->like();

        $this->assertCount(1, $like->activities);

        $this->delete(route('ajax.reply-likes.destroy', $comment));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => Like::class,
                'subject_id' => $like->id,
            ])->count()
        );
    }

    /** @test */
    public function when_a_profile_post_is_unliked_the_associated_activity_is_deleted()
    {
        $profilePost = ProfilePostFactory::create();
        $liker = $this->signIn();
        $like = $profilePost->like($liker);
        $this->assertCount(1, $like->activities);

        $this->deleteJson(route('ajax.profile-post-likes.destroy', $profilePost));

        $this->assertCount(
            0,
            Activity::where([
                'subject_type' => Like::class,
                'subject_id' => $like->id,
            ])->get()
        );
    }

    /** @test */
    public function when_a_thread_reply_is_unliked_the_associated_activities_are_deleted()
    {
        $reply = ReplyFactory::create();
        $liker = $this->signIn();
        $like = $reply->like();
        $this->assertCount(1, $like->activities);

        $this->delete(route('ajax.reply-likes.destroy', $reply));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => Like::class,
                'subject_id' => $like->id,
            ])->count()
        );
    }

    /** @test */
    public function when_a_user_logs_out_the_activities_of_type_viewed_are_deleted()
    {
        $user = $this->signIn();
        $this->get(route('forum'));
        $this->assertCount(1, Activity::typeViewed()->get());

        Auth::logout();

        $this->assertCount(0, Activity::typeViewed()->get());
    }

    /** @test */
    public function activities_of_type_viewed_that_are_more_than_15_minutes_old_will_be_deleted_daily()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        $user = $this->signIn();
        $this->get(route('forum'));
        $this->assertCount(1, Activity::typeViewed()->get());
        Carbon::setTestNow();
        $this->get(route('forum'));
        $this->assertCount(2, Activity::typeViewed()->get());

        $this->artisan('schedule:run');

        $this->assertCount(1, Activity::typeViewed()->get());
    }
}
