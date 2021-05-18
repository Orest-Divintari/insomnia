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

class ViewProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function members_may_see_the_profile_information_of_a_user()
    {
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit("/profiles/{$user->name}")
                ->assertSee($user->name)
                ->assertSee('Macrumors newbie')
                ->assertSee('Messages')
                ->assertSeeIn('@messages-count', 0)
                ->assertSee('Likes Score')
                ->assertSeeIn('@likes-count', 0)
                ->assertSee('Points')
                ->assertSeeIn('@points', 0)
                ->assertSee('Joined')
                ->assertSee($user->join_date)
                ->assertSeeLink($user->name);
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
    public function the_profile_owner_may_always_see_the_contact_identities()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName) {

            $response = $browser->loginAs($profileOwner)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertSee('Contact')
                ->assertSee('Facebook')
                ->assertSee($facebookName);
        });
    }

    /** @test */
    public function noone_can_see_the_contact_identities_of_a_user_if_that_user_does_not_allow_it()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);
        $profileOwner->allowNoone('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName, $visitor) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink("About");

            $response
                ->assertDontSee('Contact')
                ->assertDontSee('Facebook')
                ->assertDontSee($facebookName);
        });
    }

    /** @test */
    public function a_member_may_see_the_contact_identities_of_a_user_if_the_user_allows_it()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);
        $profileOwner->allowMembers('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName, $visitor) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertSee('Contact')
                ->assertSee('Facebook')
                ->assertSee($facebookName);
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
}