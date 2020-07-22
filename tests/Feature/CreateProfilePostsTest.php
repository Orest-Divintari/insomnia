<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_a_profile_post()
    {
        $profileUser = create(User::class);

        $post = [
            'body' => 'some news',
        ];

        $this->post(route('api.profile-posts.store', $profileUser), $post)
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_has_to_verify_the_email_before_creating_a_profile_post()
    {
        $this->withoutExceptionHandling();

        $profileUser = create(User::class);

        $poster = create(User::class, [
            'email_verified_at' => null,
        ]);

        $this->signIn($poster);

        $this->post(route('api.profile-posts.store', $profileUser), [])
            ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_verified_the_email_can_create_a_profile_post()
    {
        $this->withoutExceptionHandling();

        $poster = $this->signIn();

        $profileUser = create(User::class);

        $post = [
            'body' => 'some news',
        ];

        $response = $this->post(route('api.profile-posts.store', $profileUser), $post)->json();
        dd($response);
        $this->assertDatabaseHas('profile_posts', [
            'body' => $post['body'],
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);
    }

    /** @test */
    public function a_post_required_a_body()
    {
        $profileUser = create(User::class);

        $this->signIn();

        $this->post(route('api.profile-posts.store', $profileUser), ['body' => ''])
            ->assertSessionHasErrors('body');
    }
}