<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateThreadRepliesPreferencesTest extends TestCase
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
    public function users_may_enable_database_notifications_when_a_new_reply_is_added_to_a_subscribed_thread()
    {
        $attributes = [
            'thread_reply_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['thread_reply_created'], $this->user->preferences()->thread_reply_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_reply_is_added_to_a_subscribed_thread()
    {
        unset($this->preferences['thread_reply_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->thread_reply_created);
    }

    /** @test */
    public function sometimes_the_thread_reply_created_attribute_is_required()
    {
        $attributes = [
            'thread_reply_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_created');
    }

    /** @test */
    public function the_thread_reply_created_attribute_must_be_array()
    {
        $attributes = [
            'thread_reply_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_created');
    }

    /** @test */
    public function the_values_of_the_thread_reply_created_attribute_must_be_string()
    {
        $attributes = [
            'thread_reply_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_created.*');
    }

    /** @test */
    public function the_value_of_the_thread_reply_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'thread_reply_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_created.*');
    }

    /** */

    /** @test */
    public function users_may_enable_database_notifications_when_their_thread_reply_is_liked()
    {
        $attributes = [
            'thread_reply_liked' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['thread_reply_liked'], $this->user->preferences()->thread_reply_liked);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_their_thread_reply_is_liked()
    {
        unset($this->preferences['thread_reply_liked']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->thread_reply_liked);
    }

    /** @test */
    public function sometimes_the_thread_reply_liked_attribute_is_required()
    {
        $attributes = [
            'thread_reply_liked' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_liked');
    }

    /** @test */
    public function the_thread_reply_liked_attribute_must_be_array()
    {
        $attributes = [
            'thread_reply_liked' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_liked');
    }

    /** @test */
    public function the_values_of_the_thread_reply_liked_attribute_must_be_string()
    {
        $attributes = [
            'thread_reply_liked' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_liked.*');
    }

    /** @test */
    public function the_value_of_the_thread_reply_liked_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'thread_reply_liked' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('thread_reply_liked.*');
    }

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_they_are_mentioned_in_a_thread_reply()
    {
        $attributes = [
            'mentioned_in_thread_reply' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['mentioned_in_thread_reply'], $this->user->preferences()->mentioned_in_thread_reply);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_they_are_mentioned_in_a_thread_reply()
    {
        unset($this->preferences['mentioned_in_thread_reply']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->mentioned_in_thread_reply);
    }

    /** @test */
    public function sometimes_the_mentioned_in_thread_reply_attribute_is_required()
    {
        $attributes = [
            'mentioned_in_thread_reply' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread_reply');
    }

    /** @test */
    public function the_mentioned_in_thread_reply_attribute_must_be_array()
    {
        $attributes = [
            'mentioned_in_thread_reply' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread_reply');
    }

    /** @test */
    public function the_values_of_the_mentioned_in_thread_reply_attribute_must_be_string()
    {
        $attributes = [
            'mentioned_in_thread_reply' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread_reply.*');
    }

    /** @test */
    public function the_value_of_the_mentioned_in_thread_reply_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'mentioned_in_thread_reply' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_thread_reply.*');
    }

}