<?php

namespace Tests\Feature\ProfilePosts;

use App\Listeners\Profile\NotifyProfileOwnerOfNewPost;
use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NewPostWasAddedToProfileEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_adds_a_post_to_a_profile_then_an_event_is_fired()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ['body' => 'some body'];
        $listener = Mockery::spy(NotifyProfileOwnerOfNewPost::class);
        app()->instance(NotifyProfileOwnerOfNewPost::class, $listener);

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            ['body' => $profilePost['body']]
        );

        $profilePost = ProfilePost::whereBody($profilePost['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event) use (
            $profileOwner,
            $poster,
            $profilePost
        ) {
            return $event->profilePost->id == $profilePost->id
            && $event->profileOwner->id == $profileOwner->id
            && $event->postPoster->id == $poster->id;
        });
    }
}
