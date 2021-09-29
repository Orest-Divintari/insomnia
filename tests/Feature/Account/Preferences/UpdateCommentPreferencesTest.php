<?php

namespace Tests\Feature\Account\Preferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCommentPreferencesTest extends TestCase
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

    /** @test */
    public function users_may_enable_database_notifications_when_they_are_mentioned_in_a_commment()
    {
        $attributes = [
            'mentioned_in_comment' => ['database'],
        ];

        $this->patch(route('account.preferences.update'), $attributes);

        $this->assertEquals($attributes['mentioned_in_comment'], $this->user->preferences()->mentioned_in_comment);
    }

    /** @test */
    public function users_may_disable_database_notifications_when_they_are_mentioned_in_a_comment()
    {
        unset($this->preferences['mentioned_in_comment']);

        $this->patch(route('account.preferences.update'), $this->preferences);

        $this->assertEmpty($this->user->preferences()->mentioned_in_comment);
    }

    /** @test */
    public function sometimes_the_mentioned_in_comment_attribute_is_required()
    {
        $attributes = [
            'mentioned_in_comment' => [],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_comment');
    }

    /** @test */
    public function the_mentioned_in_comment_attribute_must_be_array()
    {
        $attributes = [
            'mentioned_in_comment' => 'notArray',
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_comment');
    }

    /** @test */
    public function the_values_of_the_mentioned_in_comment_attribute_must_be_string()
    {
        $attributes = [
            'mentioned_in_comment' => [5],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_comment.*');
    }

    /** @test */
    public function the_value_of_the_mentioned_in_comment_attribute_must_be_equal_to_database()
    {
        $attributes = [
            'mentioned_in_comment' => [$this->faker()->word()],
        ];

        $response = $this->patch(route('account.preferences.update'), $attributes);

        $response->assertSessionHasErrors('mentioned_in_comment.*');
    }

}