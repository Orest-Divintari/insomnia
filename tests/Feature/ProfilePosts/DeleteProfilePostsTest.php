<?php

namespace Tests\Feature\ProfilePosts;

use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
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
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();

        $this->delete(route('ajax.profile-posts.destroy', $profilePost->id));

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
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();

        $this->delete(route('ajax.profile-posts.destroy', $profilePost->id));

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
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();

        $response = $this->delete(
            route('ajax.profile-posts.destroy', $profilePost->id)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
