<?php

namespace Tests\Browser\ProfilePosts;

use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use \Facades\Tests\Setup\CommentFactory;

class ViewProfilePostsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function members_may_view_profile_posts()
    {
        $profilePost = ProfilePostFactory::create();
        $comment = CommentFactory::toProfilePost($profilePost)->create();
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user, $profilePost, $comment) {

            $response = $browser
                ->loginAs($user)
                ->visit(route('profile-posts.index'));

            $response
                ->assertSee('Profile posts')
                ->assertSee('Filters')
                ->assertSee($profilePost->body)
                ->assertSee($profilePost->profileOwner->name)
                ->assertSee($profilePost->poster->name)
                ->assertSee($comment->body)
                ->assertSee($comment->poster->name)
                ->assertVisible('@like-button')
                ->assertVisible('@new-comment');
        });
    }

    /** @test */
    public function members_may_view_the_new_profile_posts()
    {
        Carbon::setTestNow(Carbon::now()->subWeek());
        $oldProfilePost = ProfilePostFactory::create();
        Carbon::setTestNow();
        $newProfilePost = ProfilePostFactory::create();
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user, $oldProfilePost, $newProfilePost) {

            $response = $browser
                ->loginAs($user)
                ->visit(route('profile-posts.index'));

            $response
                ->assertSee('Profile posts')
                ->assertSee('Filters')
                ->click('@profile-post-filters-dropdown-button')
                ->check('@new-posts-filter-checkbox')
                ->click('@apply-filters-button')
                ->assertSee('Show only: New posts')
                ->assertQueryStringHas('new_posts', "true")
                ->assertSee($oldProfilePost->body)
                ->assertSee($newProfilePost->body);
        });
    }

    /** @test */
    public function members_may_view_only_the_profile_posts_by_members_they_follow()
    {
        $followingMember = create(User::class);
        $stranger = create(User::class);
        $john = create(User::class);
        $john->follow($followingMember);
        $profilePostByFollowingMember = ProfilePostFactory::by($followingMember)->create();
        $profilePostByStranger = ProfilePostFactory::by($stranger)->create();
        $profilePostByJonh = ProfilePostFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($john, $profilePostByFollowingMember, $profilePostByJonh, $profilePostByStranger) {

            $response = $browser
                ->loginAs($john)
                ->visit(route('profile-posts.index'));

            $response
                ->assertSee('Profile posts')
                ->assertSee('Filters')
                ->click('@profile-post-filters-dropdown-button')
                ->check('@by-following-filter-checkbox')
                ->click('@apply-filters-button')
                ->assertSee('Show only: By following')
                ->assertQueryStringHas('by_following', "true")
                ->assertSee($profilePostByFollowingMember->body)
                ->assertSee($profilePostByJonh->body)
                ->assertDontSee($profilePostByStranger->body);
        });
    }
}