<?php

namespace Tests\Feature\Account;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAccountPrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected $settings;

    public function setUp(): void
    {
        parent::setUp();

        $this->settings = [
            'show_details' => 'members',
            'post_on_profile' => 'members',
            'start_conversation' => 'members',
            'show_identities' => 'members',
        ];
    }

    /** @test */
    public function view_the_privacy_settings()
    {
        $this->signIn();

        $response = $this->get(route('account.privacy.edit'));

        $response->assertOk();
    }

    /** @test */
    public function users_can_hide_current_activity()
    {
        $user = $this->signIn();
        $this->assertTrue($user->allows('show_current_activity'));

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertFalse($user->allows('show_current_activity'));
    }

    /** @test */
    public function users_can_make_current_activity_visible()
    {
        $user = $this->signIn();
        $user->disallow('show_current_activity');
        $this->assertFalse($user->allows('show_current_activity'));
        $settings = array_merge($this->settings, ['show_current_activity' => 1]);

        $this->patch(route('account.privacy.update'), $settings);

        $this->assertTrue($user->allows('show_current_activity'));
    }

    /** @test */
    public function the_current_activity_is_required_when_is_present()
    {
        $this->signIn();
        $settings = array_merge($this->settings, ['show_current_activity' => '']);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_current_activity');
    }

    /** @test */
    public function the_current_activity_must_be_of_type_accepted_when_is_present()
    {
        $this->signIn();
        $settings = array_merge($this->settings, ['show_current_activity' => false]);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_current_activity');
    }

    /** @test */
    public function users_can_hide_the_birth_date()
    {
        $user = $this->signIn();

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertFalse($user->allows('show_birth_date'));
    }

    /** @test */
    public function users_can_make_birth_date_visible()
    {
        $user = $this->signIn();
        $user->disallow('show_birth_date');
        $this->assertFalse($user->allows('show_birth_date'));
        $settings = array_merge($this->settings, ['show_birth_date' => 1]);

        $this->patch(route('account.privacy.update'), $settings);

        $this->assertTrue($user->allows('show_birth_date'));
    }

    /** @test */
    public function the_birth_date_is_required_when_is_present()
    {
        $user = $this->signIn();
        $settings = array_merge($this->settings, ['show_birth_date' => '']);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_birth_date');
    }

    /** @test */
    public function the_birht_date_must_be_of_type_accepted_when_is_present()
    {
        $user = $this->signIn();
        $settings = array_merge($this->settings, ['show_birth_date' => false]);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_birth_date');
    }

    /** @test */
    public function users_can_reveal_the_birth_year()
    {
        $user = $this->signIn();
        $this->assertFalse($user->allows('show_birth_year'));
        $settings = array_merge($this->settings, ['show_birth_year' => 1]);

        $this->patch(route('account.privacy.update'), $settings);

        $this->assertTrue($user->allows('show_birth_year'));
    }

    /** @test */
    public function users_can_hide_the_birth_year()
    {
        $user = $this->signIn();
        $user->disallow('show_birth_year');
        $this->assertFalse($user->allows('show_birth_year'));

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertFalse($user->allows('show_birth_year'));
    }

    /** @test */
    public function the_birth_year_is_required_when_is_present()
    {
        $this->signIn();
        $settings = array_merge($this->settings, ['show_birth_year' => '']);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_birth_year');
    }

    /** @test */
    public function the_birth_year_must_be_of_type_accepted_when_is_presenet()
    {
        $this->signIn();
        $settings = array_merge($this->settings, ['show_birth_year' => false]);

        $response = $this->patch(route('account.privacy.update'), $settings);

        $response->assertSessionHasErrors('show_birth_year');
    }

    /** @test */
    public function set_details_visibile_to_noone()
    {
        $user = $this->signIn();
        $this->settings['show_details'] = 'noone';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('noone', $user->privacy()->show_details);
    }

    /** @test */
    public function set_details_visibile_to_members()
    {
        $user = $this->signIn();
        $this->settings['show_details'] = 'members';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('members', $user->privacy()->show_details);
    }

    /** @test */
    public function set_details_visibile_to_users_who_you_follow()
    {
        $user = $this->signIn();
        $this->settings['show_details'] = 'following';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('following', $user->privacy()->show_details);
    }

    /** @test */
    public function the_details_settings_cannot_have_other_value_other_than_the_predefined()
    {
        $user = $this->signIn();
        $this->settings['show_details'] = 'stranger';

        $response = $this->patch(route('account.privacy.update'), $this->settings);

        $response->assertSessionHasErrors('show_details');
    }

    /** @test */
    public function make_posts_on_profile_visibile_to_noone()
    {
        $user = $this->signIn();
        $this->settings['post_on_profile'] = 'noone';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('noone', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function makeposts_on_profile_visibile_to_members()
    {
        $user = $this->signIn();
        $this->settings['post_on_profile'] = 'members';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('members', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function makeposts_on_profile_visibile_to_users_who_you_follow()
    {
        $user = $this->signIn();
        $this->settings['post_on_profile'] = 'following';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('following', $user->privacy()->post_on_profile);
    }

    /** @test */
    public function prevent_everyone_from_starting_a_conversation_with_you()
    {
        $user = $this->signIn();
        $this->settings['start_conversation'] = 'noone';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('noone', $user->privacy()->start_conversation);
    }

    /** @test */
    public function allow_members_to_start_a_conversation_with_you()
    {
        $user = $this->signIn();
        $this->settings['start_conversation'] = 'members';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('members', $user->privacy()->start_conversation);
    }

    /** @test */
    public function allow_users_who_you_follow_to_start_a_conversation_with_you()
    {
        $user = $this->signIn();
        $this->settings['start_conversation'] = 'following';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('following', $user->privacy()->start_conversation);
    }

    /** @test */
    public function the_start_conversation_setting_cannot_have_other_value_other_than_the_predefined()
    {
        $user = $this->signIn();
        $this->settings['start_conversation'] = 'everyone';

        $response = $this->patch(route('account.privacy.update'), $this->settings);

        $response->assertSessionHasErrors('start_conversation');
    }

    //

    /** @test */
    public function set_identities_visibile_to_noone()
    {
        $user = $this->signIn();
        $this->settings['show_identities'] = 'noone';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('noone', $user->privacy()->show_identities);
    }

    /** @test */
    public function set_identities_visibile_to_members()
    {
        $user = $this->signIn();
        $this->settings['show_identities'] = 'members';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('members', $user->privacy()->show_identities);
    }

    /** @test */
    public function set_identities_visibile_to_users_who_you_follow()
    {
        $user = $this->signIn();
        $this->settings['show_identities'] = 'following';

        $this->patch(route('account.privacy.update'), $this->settings);

        $this->assertEquals('following', $user->privacy()->show_identities);
    }

    /** @test */
    public function the_identities_setting_cannot_have_other_value_other_than_the_predefined()
    {
        $user = $this->signIn();
        $this->settings['show_identities'] = 'stranger';

        $response = $this->patch(route('account.privacy.update'), $this->settings);

        $response->assertSessionHasErrors('show_identities');
    }
}