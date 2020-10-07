<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePostFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_filter_profile_posts_by_username()
    {
        $uric = create(User::class);

        $profilePostByUric = create(ProfilePost::class, ['user_id' => $uric->id]);
        createMany(ProfilePost::class, 5);

        do {
            $results = $this->getJson(
                route('search.show', ['type' => 'profile_post', 'q' => $profilePostByUric->body]
                ))->json()['data'];
        } while (empty($results));

        $this->assertCount(1, $results);
    }
}
