<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateProfilePostPreferencesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $preferences;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->signIn();
        $this->preferences = $this->user->preferences;
    }

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_profile_post_is_added_to_their_profile_by_another_user()
    {
        $attributes = [
            'profile_post_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['profile_post_created'], $this->user->preferences()->profile_post_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_profile_post_is_added_to_their_profile_by_another_user()
    {
        unset($this->preferences['profile_post_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->profile_post_created);
    }

    /** @test */
    public function sometimes_the_profile_post_created_attribute_is_required()
    {
        $attributes = [
            'profile_post_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('profile_post_created');
    }

    /** @test */
    public function the_profile_post_created_attribute_must_be_array()
    {
        $attributes = [
            'profile_post_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('profile_post_created');
    }

    /** @test */
    public function the_values_of_the_profile_post_created_attribute_must_be_string()
    {
        $attributes = [
            'profile_post_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('profile_post_created.*');
    }

    /** @test */
    public function the_value_of_the_profile_post_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'profile_post_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('profile_post_created.*');
    }

    /** @test */
    public function users_may_enable_database_notifications_when_they_are_mentioned_in_a_profile_post()
    {
        $attributes = [
            'mentioned_in_profile_post' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['mentioned_in_profile_post'], $this->user->preferences()->mentioned_in_profile_post);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_they_are_mentioned_in_a_profile_post()
    {
        unset($this->preferences['mentioned_in_profile_post']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->mentioned_in_profile_post);
    }

    /** @test */
    public function sometimes_the_mentioned_in_profile_post_attribute_is_required()
    {
        $attributes = [
            'mentioned_in_profile_post' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_profile_post');
    }

    /** @test */
    public function the_mentioned_in_profile_post_attribute_must_be_array()
    {
        $attributes = [
            'mentioned_in_profile_post' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_profile_post');
    }

    /** @test */
    public function the_values_of_the_mentioned_in_profile_post_attribute_must_be_string()
    {
        $attributes = [
            'mentioned_in_profile_post' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_profile_post.*');
    }

    /** @test */
    public function the_value_of_the_mentioned_in_profile_post_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'mentioned_in_profile_post' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_profile_post.*');
    }

}