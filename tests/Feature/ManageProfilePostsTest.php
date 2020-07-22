<?php

namespace Tests\Feature;

use App\ProfilePost;
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
    public function authorized_users_can_delete_a_profile_post()
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
}