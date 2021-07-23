<?php

namespace Tests\Feature\ProfilePosts;

use App\Models\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdatesProfilePostsTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';
    /** @test */
    public function unauthorized_users_cannot_update_a_profile_post()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $unathorizedUser = $this->signIn();

        $response = $this->patch(
            route('ajax.profile-posts.update', $profilePost->id),
            ['body' => 'new body']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function authorized_users_can_update_a_profile_post()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();

        $this->patch(
            route('ajax.profile-posts.update', $profilePost->id),
            ['body' => 'new body']
        );

        $this->assertDatabaseHas('profile_posts', [
            'body' => 'new body',
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);
        $this->assertDatabaseMissing('profile_posts', [
            'body' => $profilePost->body,
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);
    }

    /** @test */
    public function a_profile_post_requires_a_body()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();

        $response = $this->patchJson(
            route('ajax.profile-posts.update', $profilePost->id),
            ['body' => '']
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);

    }

    /** @test */
    public function the_body_of_a_profile_post_must_be_of_type_string()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();

        $nonStringBody = array(15);

        $response = $this->patchJson(
            route('ajax.profile-posts.update', $profilePost->id),
            ['body' => $nonStringBody]
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);

    }
}
