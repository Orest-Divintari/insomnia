<?php

namespace Tests\Feature\Events;

use App\Listeners\Profile\NotifyMentionedUsersInComment;
use App\Models\ProfilePost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;

class CommentWasUpdatedEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_notifies_the_mentioned_users_when_a_comment_is_updated()
    {
        $this->withoutExceptionHandling();
        $profilePost = create(ProfilePost::class);
        $commentPoster = create(User::class);
        $profileOwner = $profilePost->profileOwner;
        $comment = CommentFactory::by($commentPoster)
            ->toProfilePost($profilePost)
            ->create();
        $this->signIn($commentPoster);
        $updatedComment = ['body' => $this->faker->sentence()];
        $listener = Mockery::spy(NotifyMentionedUsersInComment::class);
        app()->instance(NotifyMentionedUsersInComment::class, $listener);

        $this->patchJson(route('ajax.comments.update', $comment), $updatedComment);

        $listener->shouldHaveReceived('handle', function ($event)
             use ($profilePost, $profileOwner, $comment, $commentPoster) {
                return $event->profilePost->is($profilePost)
                && $event->comment->is($comment)
                && $event->profileOwner->is($profileOwner)
                && $event->commentPoster->is($commentPoster);
            });
    }
}