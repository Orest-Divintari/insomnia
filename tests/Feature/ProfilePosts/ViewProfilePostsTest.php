<?php

namespace Tests\Feature\ProfilePosts;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
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

    /** @test */
    public function it_returns_the_profile_post_comments_created_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($john)->create();
        $commentByDoe = CommentFactory::by($doe)
            ->toProfilePost($profilePost)
            ->create();
        $commentByBob = CommentFactory::by($bob)
            ->toProfilePost($profilePost)
            ->create();
        $john->ignore($doe);
        $this->signIn($john);

        $response = $this->get(route('profiles.show', $john));

        $comments = collect($response['profilePosts']->items()[0]['paginatedComments']->items());
        $this->assertCount(1, $comments);
        $this->assertFalse($comments->search(function ($comment) use ($commentByDoe) {
            return $comment->is($commentByDoe);
        }));
    }

    /** @test */
    public function it_returns_profile_posts_created_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $profilePostByDoe = ProfilePostFactory::by($doe)
            ->toProfile($john)
            ->create();
        $profilePostByBob = ProfilePostFactory::by($bob)
            ->toProfile($john)
            ->create();
        $john->ignore($doe);
        $this->signIn($john);

        $response = $this->get(route('profiles.show', $john));

        $profilePosts = collect($response['profilePosts']->items());
        $this->assertCount(1, $profilePosts);
        $this->assertFalse($profilePosts->search(function ($profilePost) use ($profilePostByDoe) {
            return $profilePost->id == $profilePostByDoe->id;
        }));
    }

    /** @test */
    public function guests_should_not_see_the_new_profile_posts()
    {
        $response = $this->get(route('profile-posts.index'), ['new' => 1]);

        $response->assertRedirect('login');
    }

    /** @test */
    public function it_returns_the_new_profile_posts()
    {
        $this->signIn();
        Carbon::setTestNow(Carbon::now()->subWeek());
        $oldProfilePost = ProfilePostFactory::create();
        Carbon::setTestNow();
        $newProfilePost = ProfilePostFactory::create();

        $response = $this->get(route('profile-posts.index', ['new_posts' => 1]));

        $profilePosts = $response['profilePosts']->items();

        $this->assertEquals($newProfilePost->id, $profilePosts[0]['id']);
        $this->assertEquals($oldProfilePost->id, $profilePosts[1]['id']);
    }

    /** @test */
    public function it_returns_the_profile_posts_of_users_you_follow()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = $this->signIn();
        $bob->follow($john);
        $profilePostByBob = ProfilePostFactory::by($bob)->create();
        $profilePostByJohn = ProfilePostFactory::by($john)->create();
        $profilePostByDoe = ProfilePostFactory::by($doe)->create();

        $response = $this->get(route('profile-posts.index', ['by_following' => 1]));

        $profilePosts = collect($response['profilePosts']->items());

        $this->assertCount(2, $profilePosts);
        $this->assertTrue($profilePosts->pluck('id')->contains($profilePostByJohn->id));
        $this->assertTrue($profilePosts->pluck('id')->contains($profilePostByBob->id));
    }

}