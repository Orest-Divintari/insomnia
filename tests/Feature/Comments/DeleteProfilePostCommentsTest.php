<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
use App\Reply;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProfilePostCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_user_who_posted_the_comment_can_delete_it()
    {
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();

        $this->delete(route('api.comments.destroy', $comment));

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
        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);
        $commentPoster = $this->signIn();
        $comment = CommentFactory::by($commentPoster)->create();
        $this->signIn($profileOwner);

        $this->delete(route('api.comments.destroy', $comment));

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
        $profilePost = create(ProfilePost::class, ['user_id' => $poster->id]);
        $profilePost->addComment(
            raw(Reply::class, [
                'repliable_type' => ProfilePost::class,
            ]),
            $poster
        );
        $this->assertCount(1, $profilePost->comments);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

        $this->assertCount(0, Reply::where('repliable_type', '=', ProfilePost::class)->get());
    }
}