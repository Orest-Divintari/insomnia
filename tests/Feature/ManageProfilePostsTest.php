<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\Reply;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthorized_users_cannot_update_a_profile_post()
    {

        $profileUser = create(User::class);

        $poster = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
        ]);

        $this->patch(
            route('api.profile-posts.update', $profilePost->id),
            ['body' => 'new body']
        )->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function authorized_users_can_update_a_profile_post()
    {
        $profileUser = create(User::class);

        $poster = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $this->patch(
            route('api.profile-posts.update', $profilePost->id),
            ['body' => 'new body']
        );

        $this->assertDatabaseHas('profile_posts', [
            'body' => 'new body',
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);
    }

    /** @test */
    public function a_profile_post_requires_a_body()
    {
        $profileUser = create(User::class);

        $poster = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $this->patch(
            route('api.profile-posts.update', $profilePost->id),
            ['body' => '']
        )->assertSessionHasErrors('body');

    }

    /** @test */
    public function unathorized_users_cannot_delete_a_profile_post()
    {
        $profileUser = create(User::class);

        $poster = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
        ]);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_user_who_posted_the_post_can_delete_it()
    {
        $profileUser = create(User::class);

        $poster = $this->signIn();

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_user_id' => $profilePost->profile_user_id,
            'poster_id' => $profilePost->poster_id,
        ]);
    }

    /** @test */
    public function the_user_who_owns_the_profile_can_delete_any_post_on_his_profile()
    {
        $profileUser = create(User::class);

        $poster = create(User::class);

        $this->signIn($profileUser);

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_user_id' => $profilePost->profile_user_id,
            'poster_id' => $profilePost->poster_id,
        ]);
    }

    /** @test */
    public function when_a_post_is_deleted_then_all_the_associated_comments_are_deleted()
    {
        $profilePost = create(ProfilePost::class);

        $profilePost->addComment(raw(Reply::class, [
            'repliable_type' => ProfilePost::class,
        ]));

        $this->assertCount(1, $profilePost->comments);

        $profilePost->delete();

        $this->assertCount(Reply::all()->count(), $profilePost->comments);
    }
}