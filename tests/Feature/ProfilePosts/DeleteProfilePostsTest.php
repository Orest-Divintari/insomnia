<?php

namespace Tests\Feature\ProfilePosts;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_user_who_owns_the_profile_can_delete_any_post_on_his_profile()
    {
        $profileOwner = create(User::class);
        $poster = create(User::class);
        $this->signIn($profileOwner);
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_owner_id' => $profilePost->profile_owner_id,
            'user_id' => $profilePost->user_id,
        ]);
    }

    /** @test */
    public function the_user_who_posted_the_post_can_delete_it()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);

        $this->delete(route('api.profile-posts.destroy', $profilePost->id));

        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_owner_id' => $profilePost->profile_owner_id,
            'user_id' => $profilePost->user_id,
        ]);
    }

    /** @test */
    public function unathorized_users_cannot_delete_a_profile_post()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);

        $response = $this->delete(
            route('api.profile-posts.destroy',
                $profilePost->id)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}