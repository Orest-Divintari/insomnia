<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateFollowPreferencesTest extends TestCase
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
    public function users_may_enable_database_notifications_when_they_have_a_new_follower()
    {
        $attributes = [
            'user_followed_you' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['user_followed_you'], $this->user->preferences()->user_followed_you);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_they_have_a_new_follower()
    {
        unset($this->preferences['user_followed_you']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->user_followed_you);
    }

    /** @test */
    public function sometimes_the_user_followed_you_attribute_is_required()
    {
        $attributes = [
            'user_followed_you' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('user_followed_you');
    }

    /** @test */
    public function the_user_followed_you_attribute_must_be_array()
    {
        $attributes = [
            'user_followed_you' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('user_followed_you');
    }

    /** @test */
    public function the_values_of_the_user_followed_you_attribute_must_be_string()
    {
        $attributes = [
            'user_followed_you' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('user_followed_you.*');
    }

    /** @test */
    public function the_value_of_the_user_followed_you_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'user_followed_you' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('user_followed_you.*');
    }
}