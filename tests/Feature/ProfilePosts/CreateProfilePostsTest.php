<?php

namespace Tests\Feature\ProfilePosts;

use App\Exceptions\PostThrottlingException;
use App\Http\Middleware\ThrottlePosts;
use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateProfilePostsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $errorMessage = 'Please enter a valid message.';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function guests_cannot_create_a_profile_post()
    {
        $profileOwner = create(User::class);
        $post = ['body' => 'some news'];

        $response = $this->post(
            route('ajax.profile-posts.store', $profileOwner),
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
            route('ajax.profile-posts.store', $profileOwner),
            []
        );

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function the_profile_owner_can_always_create_posts_on_their_profile()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowNoone('post_on_profile');
        $post = ['body' => 'some news'];

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            $post
        );

        $this->assertDatabaseHas('profile_posts', [
            'body' => $post['body'],
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $profileOwner->id,
        ]);
    }

    /** @test */
    public function members_can_create_a_profile_post_only_when_the_profile_owners_allow_posts_from_members()
    {
        $profileOwner = $this->signIn();
        $poster = $this->signIn();
        $profileOwner->allowMembers('post_on_profile');
        $post = ['body' => 'some news'];

        $this->postJson(
            route('ajax.profile-posts.store', $profileOwner),
            $post
        );

        $this->assertDatabaseHas('profile_posts', [
            'body' => $post['body'],
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);
    }

    /** @test */
    public function members_may_not_create_a_profile_post_when_the_profile_owners_do_not_allow_posts_from_anyone()
    {
        $profileOwner = $this->signIn();
        $poster = $this->signIn();
        $profileOwner->allowNoone('post_on_profile');
        $post = ['body' => 'some news'];

        $response = $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            $post
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_member_cannot_create_a_profile_post_when_is_not_followed_by_the_profile_owner_when_the_profile_owner_allow_posts_only_from_users_they_follow()
    {
        $profileOwner = $this->signIn();
        $poster = $this->signIn();
        $profileOwner->allowFollowing('post_on_profile');
        $post = ['body' => 'some news'];

        $response = $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            $post
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_member_can_create_a_profile_post_when_is_followed_by_the_profile_owner_when_the_profile_owner_allow_posts_only_from_users_they_follow()
    {
        $profileOwner = $this->signIn();
        $poster = $this->signIn();
        $profileOwner->follow($poster);
        $profileOwner->allowFollowing('post_on_profile');
        $post = ['body' => 'some news'];

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
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
            route('ajax.profile-posts.store', $profileOwner),
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
            route('ajax.profile-posts.store', $profileOwner),
            ['body' => $nonStringBody]
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_user_cannot_create_a_profile_post_if_has_exceeded_the_post_rate_limit()
    {
        $this->withMiddleware([ThrottlePosts::class]);
        $this->withoutExceptionHandling();
        $profileOwner = create(User::class);
        $user = $this->signIn();
        $errorMessage = 'You must wait';
        $this->expectException(PostThrottlingException::class);

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            raw(ProfilePost::class)
        );
        $response = $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            raw(ProfilePost::class)
        );

        $this->assertTrue(str_contains(
            $response->getContent(),
            $errorMessage
        ));
    }

}