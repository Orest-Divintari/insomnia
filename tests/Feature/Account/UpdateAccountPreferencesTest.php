<?php

namespace Tests\Feature\Account;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateAccountPreferencesTest extends TestCase
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
    public function get_the_account_preferences_form()
    {
        $response = $this->get(route('account.preferences.edit'));

        $response->assertViewHas(['user' => $this->user]);
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

    /** */

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

    /**  */

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_comment_is_added_on_a_post_on_their_profile()
    {
        $attributes = [
            'comment_on_a_post_on_your_profile_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['comment_on_a_post_on_your_profile_created'], $this->user->preferences()->comment_on_a_post_on_your_profile_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_comment_is_added_on_a_post_on_their_profile()
    {
        unset($this->preferences['comment_on_a_post_on_your_profile_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->comment_on_a_post_on_your_profile_created);
    }

    /** @test */
    public function sometimes_the_comment_on_a_post_on_your_profile_created_attribute_is_required()
    {
        $attributes = [
            'comment_on_a_post_on_your_profile_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_a_post_on_your_profile_created');
    }

    /** @test */
    public function the_comment_on_a_post_on_your_profile_created_attribute_must_be_array()
    {
        $attributes = [
            'comment_on_a_post_on_your_profile_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_a_post_on_your_profile_created');
    }

    /** @test */
    public function the_values_of_the_comment_on_a_post_on_your_profile_created_attribute_must_be_string()
    {
        $attributes = [
            'comment_on_a_post_on_your_profile_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_a_post_on_your_profile_created.*');
    }

    /** @test */
    public function the_value_of_the_comment_on_a_post_on_your_profile_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'comment_on_a_post_on_your_profile_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_a_post_on_your_profile_created.*');
    }

    /**  */

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_comment_is_added_on_their_post_on_another_user_profile()
    {
        $attributes = [
            'comment_on_your_profile_post_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['comment_on_your_profile_post_created'], $this->user->preferences()->comment_on_your_profile_post_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_comment_is_added_on_their_post_on_another_user_profile()
    {
        unset($this->preferences['comment_on_your_profile_post_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->comment_on_your_profile_post_created);
    }

    /** @test */
    public function sometimes_the_comment_on_your_profile_post_created_attribute_is_required()
    {
        $attributes = [
            'comment_on_your_profile_post_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_profile_post_created');
    }

    /** @test */
    public function the_comment_on_your_profile_post_created_attribute_must_be_array()
    {
        $attributes = [
            'comment_on_your_profile_post_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_profile_post_created');
    }

    /** @test */
    public function the_values_of_the_comment_on_your_profile_post_created_attribute_must_be_string()
    {
        $attributes = [
            'comment_on_your_profile_post_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_profile_post_created.*');
    }

    /** @test */
    public function the_value_of_the_comment_on_your_profile_post_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'comment_on_your_profile_post_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_profile_post_created.*');
    }

    /**  */

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_comment_is_added_on_their_post_on_their_profile()
    {
        $attributes = [
            'comment_on_your_post_on_your_profile_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['comment_on_your_post_on_your_profile_created'], $this->user->preferences()->comment_on_your_post_on_your_profile_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_comment_is_added_on_their_post_on_their_profile()
    {
        unset($this->preferences['comment_on_your_post_on_your_profile_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->comment_on_your_post_on_your_profile_created);
    }

    /** @test */
    public function sometimes_the_comment_on_your_post_on_your_profile_created_attribute_is_required()
    {
        $attributes = [
            'comment_on_your_post_on_your_profile_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_post_on_your_profile_created');
    }

    /** @test */
    public function the_comment_on_your_post_on_your_profile_created_attribute_must_be_array()
    {
        $attributes = [
            'comment_on_your_post_on_your_profile_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_post_on_your_profile_created');
    }

    /** @test */
    public function the_values_of_the_comment_on_your_post_on_your_profile_created_attribute_must_be_string()
    {
        $attributes = [
            'comment_on_your_post_on_your_profile_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_post_on_your_profile_created.*');
    }

    /** @test */
    public function the_value_of_the_comment_on_your_post_on_your_profile_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'comment_on_your_post_on_your_profile_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_your_post_on_your_profile_created.*');
    }

    /**  */

    /** @test */
    public function users_may_enable_database_notifications_when_a_new_comment_is_added_on_a_profile_post_they_have_participated()
    {
        $attributes = [
            'comment_on_participated_profile_post_created' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['comment_on_participated_profile_post_created'], $this->user->preferences()->comment_on_participated_profile_post_created);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_a_new_comment_is_added_on_a_profile_post_they_have_participated()
    {
        unset($this->preferences['comment_on_participated_profile_post_created']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->comment_on_participated_profile_post_created);
    }

    /** @test */
    public function sometimes_the_comment_on_participated_profile_post_created_attribute_is_required()
    {
        $attributes = [
            'comment_on_participated_profile_post_created' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_participated_profile_post_created');
    }

    /** @test */
    public function the_comment_on_participated_profile_post_created_attribute_must_be_array()
    {
        $attributes = [
            'comment_on_participated_profile_post_created' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_participated_profile_post_created');
    }

    /** @test */
    public function the_values_of_the_comment_on_participated_profile_post_created_attribute_must_be_string()
    {
        $attributes = [
            'comment_on_participated_profile_post_created' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_participated_profile_post_created.*');
    }

    /** @test */
    public function the_value_of_the_comment_on_participated_profile_post_created_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'comment_on_participated_profile_post_created' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_on_participated_profile_post_created.*');
    }

    /**  */

    /** @test */
    public function users_may_enable_database_notifications_when_their_profile_post_comment_is_liked()
    {
        $attributes = [
            'comment_liked' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['comment_liked'], $this->user->preferences()->comment_liked);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_their_profile_post_comment_is_liked()
    {
        unset($this->preferences['comment_liked']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->comment_liked);
    }

    /** @test */
    public function sometimes_the_comment_liked_attribute_is_required()
    {
        $attributes = [
            'comment_liked' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_liked');
    }

    /** @test */
    public function the_comment_liked_attribute_must_be_array()
    {
        $attributes = [
            'comment_liked' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_liked');
    }

    /** @test */
    public function the_values_of_the_comment_liked_attribute_must_be_string()
    {
        $attributes = [
            'comment_liked' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_liked.*');
    }

    /** @test */
    public function the_value_of_the_comment_liked_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'comment_liked' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('comment_liked.*');
    }

    /**  */

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

    /**  */

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

    /** */

    /** @test */
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

}