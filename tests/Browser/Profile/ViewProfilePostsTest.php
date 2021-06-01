<?php

namespace Tests\Browser\Profile;

use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewProfilePostsTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function members_may_jump_to_a_specific_profile_post()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->first();

        $this->browse(function (Browser $browser) use ($orestis, $lastPost, $john) {

            $response = $browser
                ->loginAs($orestis)
                ->visit(route('profile-posts.show', $lastPost));

            $response
                ->assertSee($lastPost->body)
                ->assertSee($lastPost->date_created)
                ->assertSee($john->name)
                ->assertAttribute('@profile-post', 'id', 'profile-post-' . $lastPost->id);
        });
    }

    /** @test */
    public function members_may_jump_to_a_specific_comment_in_profile_posts()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->first();
        $comment = CommentFactory::by($john)->toProfilePost($lastPost)->create();

        $this->browse(function (Browser $browser) use ($orestis, $john, $comment, $lastPost) {

            $response = $browser
                ->loginAs($orestis)
                ->visit(route('comments.show', $comment));

            $response
                ->assertSee($lastPost->body)
                ->assertSee($lastPost->date_created)
                ->assertSee($comment->body)
                ->assertSee($comment->date_created)
                ->assertSee($john->name)
                ->assertAttribute('@profile-post-comment', 'id', 'profile-post-comment-' . $comment->id);
        });
    }

    /** @test */
    public function members__will_not_see_the_input_to_post_on_a_profile_if_the_profile_owner_does_not_allow_it()
    {
        $profileOwner = create(User::class);
        ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $profileOwner->allowNoone('post_on_profile');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}");

            $response
                ->assertMissing('@new-profile-post')
                ->assertMissing('@new-comment');
        });
    }

    /** @test */
    public function members_will_not_see_the_input_to_post_on_profile_if_is_not_followed_by_the_profile_owner()
    {
        $profileOwner = create(User::class);
        $profileOwner->allowFollowing('post_on_profile');
        ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}");

            $response
                ->assertMissing('@new-profile-post')
                ->assertMissing('@new-comment');
        });
    }

    /** @test */
    public function members_will_see_the_form_to_post_on_profile_if_is_followed_by_the_profile_owner()
    {
        $profileOwner = create(User::class);
        $profileOwner->allowFollowing('post_on_profile');
        ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $visitor = create(User::class);
        $profileOwner->follow($visitor);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}");

            $response
                ->assertPresent('@new-profile-post')
                ->assertPresent('@new-comment');
        });
    }
    /** @test */
    public function members_may_view_profile_posts_and_associated_comments()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $post = ProfilePostFactory::toProfile($orestis)->by($john)->create();
        $comment = CommentFactory::by($john)->toProfilePost($post)->create();

        $this->browse(function (Browser $browser) use ($orestis, $john, $comment, $post) {

            $response = $browser
                ->loginAs($orestis)
                ->visit("/profiles/{$orestis->name}");

            $response->assertSee($post->body)
                ->assertSee($post->date_created)
                ->assertSee($comment->body)
                ->assertSee($comment->date_created)
                ->assertSee($john->name);
        });
    }

    /** @test */
    public function members_may_view_previous_comments_when_there_are_many_comments_in_a_profile_post()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $post = ProfilePostFactory::toProfile($orestis)->by($john)->create();
        $comments = CommentFactory::by($john)
            ->toProfilePost($post)
            ->createMany(ProfilePost::REPLIES_PER_PAGE * 2);

        $this->browse(function (Browser $browser) use ($orestis, $john, $comments, $post) {

            $response = $browser
                ->loginAs($orestis)
                ->visit(route('profiles.show', $orestis))
                ->click('@previous-comments-button');

            $comments->each(function ($comment) use ($response) {
                $response->assertSee($comment->body);
            });
        });
    }

    /** @test */
    public function visitors_may_view_more_profile_posts()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::toProfile($orestis)
            ->by($john)
            ->createMany(ProfilePost::PER_PAGE * 2);
        $secondPagePosts = $posts->sortByDesc('id')->take(ProfilePost::PER_PAGE);

        $this->browse(function (Browser $browser) use ($orestis, $john, $secondPagePosts) {

            $response = $browser
                ->loginAs($orestis)
                ->visit(route('profiles.show', $orestis) . "?page=2");

            $secondPagePosts->each(function ($post) use ($response) {
                $response->assertSee($post->body);
            });
        });
    }
}