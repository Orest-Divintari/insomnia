<?php

namespace Tests\Unit;

use App\User;
use App\User\Privacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandlesPrivacyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_determines_whether_the_given_setting_is_true()
    {
        $user = create(User::class);

        $user->allow('show_birth_date');

        $this->assertTrue($user->allows('show_birth_date'));
    }

    /** @test */
    public function it_determnines_whether_a_given_setting_is_false()
    {
        $user = create(User::class);

        $user->disallow('show_birth_date');

        $this->assertTrue($user->denies('show_birth_date'));
    }

    /** @test */
    public function it_gives_permission_to_following_for_a_given_setting()
    {
        $user = create(User::class);

        $user->allowFollowing('post_on_profile');

        $this->assertEquals('following', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function it_gives_permissions_to_members_to_for_a_given_setting()
    {
        $user = create(User::class);

        $user->allowMembers('post_on_profile');

        $this->assertEquals('members', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function it_gives_permission_to_noone_for_a_given_setting()
    {
        $user = create(User::class);

        $user->allowNoone('post_on_profile');

        $this->assertEquals('noone', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function it_returns_an_instance_of_the_privacy_settings()
    {
        $user = create(User::class);

        $this->assertInstanceOf(Privacy::class, $user->privacy());
    }
}