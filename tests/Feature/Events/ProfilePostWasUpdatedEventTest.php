<?php

namespace Tests\Feature\Events;

use App\Listeners\Profile\NotifyMentionedUsersInProfilePost;
use App\Models\ProfilePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ProfilePostWasUpdatedEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_notifies_the_mentioned_users_when_a_profile_post_is_updated()
    {
        $this->withoutExceptionHandling();
        $listener = Mockery::spy(NotifyMentionedUsersInProfilePost::class);
        app()->instance(NotifyMentionedUsersInProfilePost::class, $listener);
        $profilePost = create(ProfilePost::class);
        $this->signIn($profilePost->poster);
        $updatedProfilePsot = ['body' => $this->faker->sentence()];

        $this->patchJson(route('ajax.profile-posts.update', $profilePost), $updatedProfilePsot);

        $listener->shouldHaveReceived('handle', function ($event) use ($profilePost) {
            return $event->profilePost->is($profilePost)
            && $event->profileOwner->is($profilePost->profileOwner)
            && $event->poster->is($profilePost->poster);
        });
    }
}