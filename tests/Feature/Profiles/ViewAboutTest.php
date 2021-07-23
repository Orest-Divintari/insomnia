<?php

namespace Tests\Feature\Profiles;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewAboutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_profile_owners_details()
    {
        $profileOwner = $this->signIn();
        $details = $profileOwner->details;
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
        $response = $this->patch(route('account.details.update', $details));
        $visitor = $this->signIn();

        $response = $this->get(route('ajax.about.show', $profileOwner));

        $this->assertTrue(
            collect($profileOwner->details)
                ->every(function ($value, $key) use ($details, $response) {
                    return $value == $response['user']['details'][$key];
                })
        );
        $this->assertEquals(
            $response['user']['date_of_birth'],
            $profileOwner->date_of_birth
        );
    }

    /** @test */
    public function it_returns_the_permissions_of_the_profile_owner()
    {
        $user = $this->signIn();

        $response = $this->get(route('ajax.about.show', $user));

        $this->assertTrue(array_key_exists('permissions', $response['user']));

    }

    /** @test */
    public function guests_may_not_view_the_about_information_of_a_user()
    {
        $response = $this->getJson(route('ajax.about.show', create(User::class)));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_returns_the_list_of_followers_of_profile_user()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $this->signIn($followerA);
        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('ajax.about.show', $profileOwner)
        )->json()['followers'];

        $data = $response['data'];
        $this->assertEquals(
            $data[0]['id'],
            $followerA->id
        );
        $this->assertEquals(
            $data[1]['id'],
            $followerB->id
        );
        $this->assertEquals(
            route('ajax.followers.index', $profileOwner),
            $response['path']
        );
    }

    /** @test */
    public function it_returns_the_list_of_users_that_profile_user_is_following()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $this->signIn($profileOwner);
        $profileOwner->follow($followerA);
        $profileOwner->follow($followerB);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('ajax.about.show', $profileOwner)
        )->json()['followings'];

        $data = $response['data'];
        $this->assertEquals(
            $data[0]['id'],
            $followerA->id
        );
        $this->assertEquals(
            $data[1]['id'],
            $followerB->id
        );
        $this->assertEquals(
            route('ajax.followings.index', $profileOwner),
            $response['path']
        );
    }

}
