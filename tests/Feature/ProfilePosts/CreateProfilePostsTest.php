<?php

namespace Tests\Feature\ProfilePosts;

use App\Exceptions\ThrottlePostsException;
use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';
    /** @test */
    public function guests_cannot_create_a_profile_post()
    {
        $profileOwner = create(User::class);
        $post = ['body' => 'some news'];

        $response = $this->post(
            route('api.profile-posts.store', $profileOwner),
            $post
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unferified_users_must_not_add_profile_posts()
    {
        $profileOwner = create(User::class);
        $poster = create(User::class, [
            'email_verified_at' => null,
        ]);
        $this->signIn($poster);

        $response = $this->post(
            route('api.profile-posts.store', $profileOwner),
            []
        );

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_verified_the_email_can_create_a_profile_post()
    {
        $poster = $this->signIn();
        $profileOwner = create(User::class);
        $post = ['body' => 'some news'];

        $this->post(
            route('api.profile-posts.store', $profileOwner),
            $post
        );

        $this->assertDatabaseHas('profile_posts', [
            'body' => $post['body'],
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);
    }

    /** @test */
    public function a_profile_post_requires_a_body()
    {
        $profileOwner = create(User::class);
        $this->signIn();

        $response = $this->postJson(
            route('api.profile-posts.store', $profileOwner),
            ['body' => '']
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_post_body_must_be_of_type_string()
    {
        $profileOwner = create(User::class);
        $this->signIn();
        $nonStringBody = array(15);

        $response = $this->postJson(
            route('api.profile-posts.store', $profileOwner),
            ['body' => $nonStringBody]
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_user_cannot_create_a_profile_post_if_has_exceeded_the_post_rate_limit()
    {
        $this->withoutExceptionHandling();
        $profileOwner = create(User::class);
        $user = $this->signIn();
        $errorMessage = 'You must wait';
        $this->expectException(ThrottlePostsException::class);

        $this->post(
            route('api.profile-posts.store', $profileOwner),
            raw(ProfilePost::class)
        );
        $response = $this->post(
            route('api.profile-posts.store', $profileOwner),
            raw(ProfilePost::class)
        );

        $this->assertTrue(str_contains(
            $response->getContent(),
            $errorMessage
        ));
    }

}