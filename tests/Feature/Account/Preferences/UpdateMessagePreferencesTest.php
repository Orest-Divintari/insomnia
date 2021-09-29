<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateMessagePreferencesTest extends TestCase
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
    public function users_may_enable_email_notifications_when_their_conversation_message_is_liked()
    {
        $attributes = [
            'message_liked' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['message_liked'], $this->user->preferences()->message_liked);
    }

    /** @test */
    public function users_may_disable_email_notifications_when_their_conversation_message_is_liked()
    {
        unset($this->preferences['message_liked']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->message_liked);
    }

    /** @test */
    public function sometimes_the_message_liked_attribute_is_required()
    {
        $attributes = [
            'message_liked' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_liked');
    }

    /** @test */
    public function the_message_liked_attribute_must_be_array()
    {
        $attributes = [
            'message_liked' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_liked');
    }

    /** @test */
    public function the_values_of_the_message_liked_attribute_must_be_string()
    {
        $attributes = [
            'message_liked' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_liked.*');
    }

    /** @test */
    public function the_value_of_the_message_liked_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'message_liked' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_liked.*');
    }

    /**  */

    /** @test */
    public function users_may_enable_email_notifications_when_they_receive_a_new_conversation_message()
    {
        $attributes = [
            'message_created' => ['mail'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['message_created'], $this->user->preferences()->message_created);
    }

    /** @test */
    public function users_may_disable_email_notifications_when_they_receive_a_new_conversation_message()
    {
        unset($this->preferences['message_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->message_created);
    }

    /** @test */
    public function sometimes_the_message_created_attribute_is_required()
    {
        $attributes = [
            'message_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_created');
    }

    /** @test */
    public function the_message_created_attribute_must_be_array()
    {
        $attributes = [
            'message_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_created');
    }

    /** @test */
    public function the_values_of_the_message_created_attribute_must_be_string()
    {
        $attributes = [
            'message_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_created.*');
    }

    /** @test */
    public function the_value_of_the_message_created_attribute_must_be_equal_to_mail()
    {
        $attributes = [
            'message_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('message_created.*');
    }
}