<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateThreadPreferencesTest extends TestCase
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

    public function users_may_enable_the_option_to_automatically_subscribe_to_the_thread_they_create()
    {
        $attributes = [
            'subscribe_on_creation' => "true",
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertTrue($this->user->preferences()->subscribe_on_creation);
    }

    /** @test */
    public function users_may_disable_the_option_to_automatically_subscribe_to_the_thread_they_create()
    {
        unset($this->preferences['subscribe_on_creation']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertFalse($this->user->preferences()->subscribe_on_creation);
    }

    /** @test */
    public function the_subscribe_on_creation_attribute_is_required_when_is_presenet()
    {
        $attributes = [
            'subscribe_on_creation' => "",
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_creation');
    }

    /** @test */
    public function the_subscribe_on_creation_attribute_must_be_of_type_accepted_when_is_present()
    {
        $attributes = [
            'subscribe_on_creation' => false,
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_creation');
    }

    /** */

    /** @test */
    public function users_may_enable_the_option_to_automatically_receive_email_notifications_from_the_subscription_to_the_thread_they_create()
    {
        $attributes = [
            'subscribe_on_creation_with_email' => "true",
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertTrue($this->user->preferences()->subscribe_on_creation_with_email);
    }

    /** @test */
    public function users_may_disable_the_option_to_automatically_receive_email_notifications_from_the_subscription_to_the_thread_they_create()
    {
        unset($this->preferences['subscribe_on_creation_with_email']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertFalse($this->user->preferences()->subscribe_on_creation_with_email);
    }

    /** @test */
    public function the_subscribe_on_creation_with_email_attribute_is_required_when_is_presenet()
    {
        $attributes = [
            'subscribe_on_creation_with_email' => "",
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_creation_with_email');
    }

    /** @test */
    public function the_subscribe_on_creation_with_email_attribute_must_be_of_type_accepted_when_is_present()
    {
        $attributes = [
            'subscribe_on_creation_with_email' => false,
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_creation_with_email');
    }

    /** */

    /** @test */
    public function users_may_enable_the_option_to_automatically_subscribe_to_the_thread_they_interact_with()
    {
        $this->withoutExceptionHandling();
        $attributes = [
            'subscribe_on_interaction' => 1,
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertTrue($this->user->preferences()->subscribe_on_interaction);
    }

    /** @test */
    public function users_may_disable_the_option_to_automatically_subscribe_to_the_thread_they_interact_with()
    {
        unset($this->preferences['subscribe_on_interaction']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertFalse($this->user->preferences()->subscribe_on_interaction);
    }

    /** @test */
    public function the_subscribe_on_interaction_attribute_is_required_when_is_presenet()
    {
        $attributes = [
            'subscribe_on_interaction' => "",
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_interaction');
    }

    /** @test */
    public function the_subscribe_on_interaction_attribute_must_be_of_type_accepted_when_is_present()
    {
        $attributes = [
            'subscribe_on_interaction' => false,
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_interaction');
    }

    /**  */

    /** @test */
    public function users_may_enable_the_option_to_automatically_receive_email_notifications_from_the_subscription_to_the_thread_they_interact_with()
    {
        $attributes = [
            'subscribe_on_interaction_with_email' => "true",
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertTrue($this->user->preferences()->subscribe_on_interaction_with_email);
    }

    /** @test */
    public function users_may_disable_the_option_to_automatically_receive_email_notifications_from_the_subscription_to_the_thread_they_interact_with()
    {
        unset($this->preferences['subscribe_on_interaction_with_email']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertFalse($this->user->preferences()->subscribe_on_interaction_with_email);
    }

    /** @test */
    public function the_subscribe_on_interaction_with_email_attribute_is_required_when_is_presenet()
    {
        $attributes = [
            'subscribe_on_interaction_with_email' => "",
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_interaction_with_email');
    }

    /** @test */
    public function the_subscribe_on_interaction_with_email_attribute_must_be_of_type_accepted_when_is_present()
    {
        $attributes = [
            'subscribe_on_interaction_with_email' => false,
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('subscribe_on_interaction_with_email');
    }

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_they_are_mentioned_in_a_thread_body()
    {
        $attributes = [
            'mentioned_in_thread' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['mentioned_in_thread'], $this->user->preferences()->mentioned_in_thread);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_they_are_mentioned_in_a_thread_body()
    {
        unset($this->preferences['mentioned_in_thread']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->mentioned_in_thread);
    }

    /** @test */
    public function sometimes_the_mentioned_in_thread_attribute_is_required()
    {
        $attributes = [
            'mentioned_in_thread' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread');
    }

    /** @test */
    public function the_mentioned_in_thread_attribute_must_be_array()
    {
        $attributes = [
            'mentioned_in_thread' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread');
    }

    /** @test */
    public function the_values_of_the_mentioned_in_thread_attribute_must_be_string()
    {
        $attributes = [
            'mentioned_in_thread' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread.*');
    }

    /** @test */
    public function the_value_of_the_mentioned_in_thread_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'mentioned_in_thread' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread.*');
    }

}