<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unathorized_users_cannot_edit_a_comment()
    {
        $user = create(User::class);

        $post = create(ProfilePost::class);

        $comment = $post->addComment([
            'body' => 'some body',
            'user_id' => $user->id,
        ]);

        $this->signIn();

        $this->patch(route('api.comments.update', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function the_user_who_posted_the_comment_may_edit_the_comment()
    {
        $postOwner = create(User::class);

        $commentOwner = create(User::class);

        $post = create(ProfilePost::class, [
            'profile_user_id' => $postOwner->id,
        ]);

        $comment = $post->addComment([
            'body' => 'some body',
            'user_id' => $commentOwner->id,
        ]);

        $this->signIn($commentOwner);

        $updatedComment = [
            'body' => 'updated body',
        ];

        $this->patch(route('api.comments.update', $comment), $updatedComment);

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentOwner->id,
            'body' => $comment->body,
        ]);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentOwner->id,
            'body' => $updatedComment['body'],
        ]);

    }

    /** @test */
    public function unathorized_users_cannot_delete_a_comment()
    {
        $user = create(User::class);

        $post = create(ProfilePost::class);

        $comment = $post->addComment([
            'body' => 'some body',
            'user_id' => $user->id,
        ]);

        $this->signIn();

        $this->delete(route('api.comments.destroy', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_comment_can_delete_it()
    {
        $postOwner = create(User::class);

        $commentOwner = create(User::class);

        $post = create(ProfilePost::class, [
            'profile_user_id' => $postOwner->id,
        ]);

        $comment = $post->addComment([
            'body' => 'some body',
            'user_id' => $commentOwner->id,
        ]);

        $this->signIn($commentOwner);

        $this->delete(route('api.comments.destroy', $comment));

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentOwner->id,
            'body' => $comment->body,
        ]);

    }

    /** @test */
    public function the_owner_of_the_profile_can_delete_any_comment()
    {
        $profileUser = create(User::class);

        $commentOwner = create(User::class);

        $post = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
        ]);

        $comment = $post->addComment([
            'body' => 'some body',
            'user_id' => $commentOwner->id,
        ]);

        $this->signIn($profileUser);

        $this->delete(route('api.comments.destroy', $comment));

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentOwner->id,
            'body' => $comment->body,
        ]);
    }
}