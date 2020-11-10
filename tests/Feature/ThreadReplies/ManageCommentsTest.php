<?php

namespace Tests\Feature\ThreadReplies;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;

class ManageCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unathorized_users_cannot_edit_a_comment()
    {
        $comment = CommentFactory::create();

        $poster = $this->signIn();

        $this->patch(route('api.comments.update', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function the_user_who_posted_the_comment_may_edit_the_comment()
    {
        $commentPoster = $this->signIn();

        $comment = CommentFactory::by($commentPoster)->create();

        $updatedComment = [
            'body' => 'updated body',
        ];

        $this->patch(route('api.comments.update', $comment), $updatedComment);

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $comment->body,
        ]);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $updatedComment['body'],
        ]);

    }

    /** @test */
    public function unathorized_users_cannot_delete_a_comment()
    {
        $comment = CommentFactory::create();

        $unauthorizedUser = $this->signIn();

        $this->delete(route('api.comments.destroy', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_comment_can_delete_it()
    {
        $commentPoster = $this->signIn();

        $comment = CommentFactory::by($commentPoster)->create();

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentPoster->id,
            'body' => $comment->body,
        ]);

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

}