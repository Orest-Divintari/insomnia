<?php

namespace Tests\Feature\ProfilePosts;

use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function jump_to_a_specific_profile_post()
    {
        $orestis = create(User::class);
        $numberOfPages = 5;
        $posts = ProfilePostFactory::toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);
        $lastPost = $posts->last();

        $response = $this->get(route('profile-posts.show', $lastPost));

        $response->assertRedirect($lastPost->path);
    }
}