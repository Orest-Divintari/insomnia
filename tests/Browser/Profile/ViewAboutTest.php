<?php

namespace Tests\Browser\Profile;

use App\Follow;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewAboutTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function it_displays_a_message_when_profile_owner_has_not_about_data()
    {
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user) {

            $response = $browser->loginAs($user)
                ->visit("/profiles/{$user->name}")
                ->clickLink('About')
                ->assertSee("{$user->name} has not provided any additional information");
        });
    }

    /** @test */
    public function members_may_view_the_about_information_of_a_profile()
    {
        $details['location'] = 'netherlads';
        $details['website'] = 'insomnia';
        $details['birth_date'] = "1993-08-25";
        $details['occupation'] = 'developer';
        $details['gender'] = 'male';
        $details['about'] = 'My name is orestis and i am 28 years old.';
        $details['skype'] = 'orestis';
        $details['google_talk'] = 'orestis@gmail.com';
        $details['facebook'] = 'orestis uric';
        $details['twitter'] = 'OrestisDivintari';
        $details['instagram'] = 'Orestis';
        $details['location'] = 'netherlads';
        $profileOwner = create(User::class);
        $profileOwner->details()->merge($details);
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $details) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            unset($details['birth_date']);
            foreach ($details as $key => $value) {
                $response->assertSee($value);
            }
        });
    }

    /** @test */
    public function users_may_view_only_the_month_and_the_day_of_birth()
    {
        $profileOwner = create(User::class);
        $birthDay = Carbon::parse('1993-08-25');
        $birth_date = $birthDay->format('Y-m-d');
        $profileOwner->details()->merge(compact('birth_date'));
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $birthDay) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Birthday:');

            $response->assertSee($birthDay->format('M d'));
        });
    }

    /** @test */
    public function users_may_view_the_full_birthday()
    {
        $profileOwner = create(User::class);
        $birthDay = Carbon::parse('1993-08-25');
        $birth_date = $birthDay->format('Y-m-d');
        $profileOwner->details()->merge(compact('birth_date'));
        $profileOwner->allow('show_birth_year');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $birthDay) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Birthday');

            $response->assertSee($birthDay->format('M d, Y') . " ( Age: {$birthDay->age} )");
        });
    }

    /** @test */
    public function users_may_not_see_birthday_information_at_all()
    {
        $profileOwner = create(User::class);
        $birthDay = Carbon::parse('1993-08-25');
        $birth_date = $birthDay->format('Y-m-d');
        $profileOwner->details()->merge(compact('birth_date'));
        $profileOwner->disallow('show_birth_date');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $birthDay) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response->assertDontSee('Birthday');
        });
    }

    /** @test */
    public function members_may_not_view_the_contact_information_when_the_profile_owner_hides_it_from_everyone()
    {
        $profileOwner = create(User::class);
        $facebook = 'orestis';
        $profileOwner->details()->merge(compact('facebook'));
        $profileOwner->allowNoone('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response->assertDontSee('Contact');
        });
    }

    /** @test */
    public function members_may_view_the_contact_information_only_when_are_followed_by_the_profile_owner()
    {
        $profileOwner = create(User::class);
        $facebook = 'orestis';
        $profileOwner->details()->merge(compact('facebook'));
        $profileOwner->allowFollowing('show_identities');
        $visitor = create(User::class);
        $profileOwner->follow($visitor);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $facebook) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertSee('Contact')
                ->assertSee('Facebook')
                ->assertSee($facebook);
        });
    }

    /** @test */
    public function members_may_not_view_the_contact_information_if_are_not_followed_by_the_profile_owner()
    {
        $profileOwner = create(User::class);
        $facebook = 'orestis';
        $profileOwner->details()->merge(compact('facebook'));
        $profileOwner->allowFollowing('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $facebook) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertDontSee('Contact')
                ->assertDontSee('Facebook')
                ->assertDontSee($facebook);
        });
    }

    /** @test */
    public function members_may_view_the_users_the_profile_owner_follows()
    {
        $profileOwner = create(User::class);
        $visitor = create(User::class);
        $profileOwner->follow($visitor);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Following');

            $response
                ->assertSee('Following')
                ->assertVue('profileOwner.name', $visitor->name, '@profile-popover');
        });
    }

    /** @test */
    public function members_may_view_the_followers_of_profile_owner()
    {
        $profileOwner = create(User::class);
        $visitor = create(User::class);
        $visitor->follow($profileOwner);

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Followers');

            $response
                ->assertSee('Followers')
                ->assertVue('profileOwner.name', $visitor->name, '@profile-popover');
        });
    }

    /** @test */
    public function visitor_may_click_to_view_more_users_that_follow_the_profile_owner()
    {
        $profileOwner = create(User::class);
        $visitor = create(User::class);
        $users = createMany(User::class, Follow::FOLLOWED_BY_PER_PAGE * 2 + 1);
        $users->each(function ($user) use ($profileOwner) {
            $user->follow($profileOwner);
        });
        $firstFollower = $users->first();

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $firstFollower, $users) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Followers')
                ->click('@follow-list-button')
                ->waitFor('@fetch-more-button')
                ->click('@fetch-more-button')
                ->click('@fetch-more-button');

            $users->each(function ($user) use ($response) {
                $response
                    ->assertSee($user->name)
                    ->assertSee("Like score: {$user->receivedLikes()->count()}")
                    ->assertSee("Messages: {$user->profilePosts()->count()}");
            });
        });
    }

    /** @test */
    public function visitor_may_click_to_view_more_users_that_the_profile_owner_follows()
    {
        $profileOwner = create(User::class);
        $visitor = create(User::class);
        $users = createMany(User::class, Follow::FOLLOWS_PER_PAGE * 2 + 1);
        $users->each(function ($user) use ($profileOwner) {
            $profileOwner->follow($user);
        });
        $firstFollower = $users->first();

        $this->browse(function (Browser $browser) use ($visitor, $profileOwner, $firstFollower, $users) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About')
                ->waitForText('Following')
                ->click('@follow-list-button')
                ->waitFor('@fetch-more-button')
                ->click('@fetch-more-button');

            $users->each(function ($user) use ($response) {
                $response
                    ->assertSee($user->name)
                    ->assertSee("Like score: {$user->receivedLikes()->count()}")
                    ->assertSee("Messages: {$user->profilePosts()->count()}");
            });
        });
    }

}