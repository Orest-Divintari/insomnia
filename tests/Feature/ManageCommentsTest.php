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
        $poster = $this->signIn();

        $post = create(ProfilePost::class);

        $user = create(User::class);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $user->id,
            ],
            $poster
        );

        $this->patch(route('api.comments.update', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function the_user_who_posted_the_comment_may_edit_the_comment()
    {
        $postOwner = create(User::class);

        $commentOwner = create(User::class);

        $poster = $this->signIn($commentOwner);

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $postOwner->id,
        ]);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $commentOwner->id,
            ],
            $poster
        );

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
        $poster = $this->signIn();

        $post = create(ProfilePost::class);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $poster->id,
            ],
            $poster
        );

        $this->signIn();

        $this->delete(route('api.comments.destroy', $comment))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_comment_can_delete_it()
    {
        $postOwner = create(User::class);

        $commentOwner = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $postOwner->id,
        ]);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $commentOwner->id,
            ],
            $commentOwner
        );

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
        $profileOwner = create(User::class);

        $commentOwner = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $commentOwner->id,
            ],
            $commentOwner
        );

        $this->signIn($profileOwner);

        $this->delete(route('api.comments.destroy', $comment));

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $comment->id,
            'repliable_type' => ProfilePost::class,
            'user_id' => $commentOwner->id,
            'body' => $comment->body,
        ]);
    }

}