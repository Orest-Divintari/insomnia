<?php

namespace Tests\Feature;

use App\ProfilePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_all_the_comments_associated_with_a_post()
    {
        $post = create(ProfilePost::class);

        $this->signIn();

        $response = $this->get(route('api.comments.index', $post))->json();
        dd($response);
    }
}