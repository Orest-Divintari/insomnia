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
    public function only_authenticated_users_can_jump_to_a_specific_profile_post()
    {
        $orestis = $this->signIn();
        $numberOfPages = 5;
        $posts = ProfilePostFactory::toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);
        $lastPost = $posts->last();

        $response = $this->get(route('profile-posts.show', $lastPost));

        $response->assertRedirect($lastPost->path);
    }

    /** @test */
    public function guests_may_not_see_a_profile_post()
    {
        $profileOwner = create(User::class);
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();

        $response = $this->get(route('profile-posts.show', $post));

        $response->assertRedirect('login');
    }

}