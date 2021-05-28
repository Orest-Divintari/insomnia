<?php

namespace Tests\Feature\ProfilePosts;

use App\Listeners\Subscription\NotifyProfilePostPosterOfNewLike;
use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ProfilePostWasLikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_adds_a_post_to_a_profile_then_an_event_is_fired()
    {
        $profileOwner = create(User::class);
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $liker = $this->signIn();
        $listener = Mockery::spy(NotifyProfilePostPosterOfNewLike::class);
        app()->instance(NotifyProfilePostPosterOfNewLike::class, $listener);

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        $like = $profilePost->likes()->first();
        $listener->shouldHaveReceived('handle', function ($event) use (
            $profileOwner,
            $poster,
            $liker,
            $like,
            $profilePost
        ) {
            return $event->profilePost->is($profilePost) &&
            $event->profileOwner->is($profileOwner) &&
            $event->poster->is($poster) &&
            $event->liker->is($liker) &&
            $event->like->is($like);
        });
    }
}