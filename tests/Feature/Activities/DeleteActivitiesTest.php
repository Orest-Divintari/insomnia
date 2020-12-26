<?php

namespace Tests\Feature\Activities;

use App\Activity;
use App\Like;
use App\ProfilePost;
use App\Reply;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteActivitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_profile_post_is_deleted_the_associated_activities_are_deleted()
    {
        $poster = $this->signIn();
        $profilePost = create(ProfilePost::class, ['user_id' => $poster->id]);

        $this->assertCount(1, $profilePost->activities);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

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

        $this->delete(route('api.comments.destroy', $comment));

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
        $like = $comment->likedBy();
        $this->assertCount(1, $like->activities);

        $this->delete(route('api.likes.destroy', $comment));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => Like::class,
                'subject_id' => $like->id,
            ])->count()
        );
    }

    /** @test */
    public function when_a_thread_reply_is_unliked_the_associated_activities_are_deleted()
    {
        $reply = ReplyFactory::create();
        $liker = $this->signIn();
        $like = $reply->likedBy();
        $this->assertCount(1, $like->activities);

        $this->delete(route('api.likes.destroy', $reply));

        $this->assertEquals(
            0,
            Activity::where([
                'subject_type' => Like::class,
                'subject_id' => $like->id,
            ])->count()
        );
    }
}