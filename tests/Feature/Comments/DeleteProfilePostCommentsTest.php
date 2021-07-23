<?php

namespace Tests\Feature\Comments;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteProfilePostCommentsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function the_user_who_posted_the_comment_can_delete_it()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();

        $this->delete(route('ajax.comments.destroy', $comment));

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $comment->body,
        ]);
    }

    /** @test */
    public function the_owner_of_the_profile_can_delete_any_comment()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)
            ->toProfilePost($profilePost)
            ->create();
        $this->signIn($profileOwner);

        $this->delete(route('ajax.comments.destroy', $comment));

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $comment->body,
        ]);
    }

    /** @test */
    public function when_a_profile_post_is_deleted_then_all_the_associated_comments_are_deleted()
    {
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profilePost->addComment(['body' => $this->faker->sentence], $poster);
        $this->assertCount(1, $profilePost->comments);

        $this->delete(route('ajax.profile-posts.destroy', $profilePost->id));

        $this->assertCount(0, Reply::where('repliable_type', '=', ProfilePost::class)->get());
    }
}
