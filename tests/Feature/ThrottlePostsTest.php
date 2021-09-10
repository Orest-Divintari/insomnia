<?php

namespace Tests\Feature;

use App\Http\Middleware\ThrottlePosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ThrottlePostsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_should_not_throttle_the_very_first_post_of_a_user()
    {
        $this->withMiddleware(ThrottlePosts::class);
        $user = $this->signIn();
        $attributes = [
            'body' => $this->faker()->sentence(),
        ];
        $this->get(route('profiles.show', $user));

        $response = $this->postJson(route('ajax.profile-posts.store', $user), $attributes);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}