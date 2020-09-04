<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_the_posts_of_a_profile()
    {
        $profileOwner = create(User::class);

        $poster = create(User::class);

        $totalNumberOfPosts = 100;

        createMany(
            ProfilePost::class,
            $totalNumberOfPosts,
            [
                'profile_owner_id' => $profileOwner->id,
                'poster_id' => $poster->id,
            ]
        );

        $response = $this->get(route('api.profile-posts.index', $profileOwner))->json();

        $this->assertCount(ProfilePost::PER_PAGE, $response['data']);

    }
}
