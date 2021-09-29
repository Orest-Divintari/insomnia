<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\User;
use App\Notifications\YouHaveBeenMentionedInAComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MentionInCommentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->desiredChannels = ['database'];
        $this->profilePost = create(ProfilePost::class);
        $this->mentionedUser = create(User::class);
        $this->commentPoster = $this->signIn();
    }

    /** @test */
    public function a_user_receives_a_database_notification_when_is_mentioned_in_a_comment()
    {
        $comment = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $comment);

        Notification::assertSentTo($this->mentionedUser,
            function (YouHaveBeenMentionedInAComment $notification, $channels) {
                return $notification->profilePost->is($this->profilePost) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_notifications_only_to_the_newly_mentioned_users_when_a_comment_is_updated()
    {
        $this->withoutExceptionHandling();
        unset(app()[ChannelManager::class]);
        $this->withoutMiddleware([ThrottlePosts::class]);
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $poster = $this->signIn();
        $post = create(ProfilePost::class);
        $mentionedUser = create(User::class);
        $comment = ['body' => "hello @{$mentionedUser->name}"];
        $newMentionedUser = create(User::class);

        $comment = $this->postJson(route('ajax.comments.store', $post), $comment)->json();

        $this->assertCount(1, $mentionedUser->notifications);
        $this->assertEquals($comment['id'], $mentionedUser->notifications()->first()['data']['comment']['id']);
        $this->assertCount(0, $newMentionedUser->notifications);

        $updatedComment = ['body' => "hello @{$mentionedUser->name} and @{$newMentionedUser->name}"];
        $comment = Reply::where('repliable_type', ProfilePost::class)->where('id', $comment['id'])->first();

        $this->patchJson(route('ajax.comments.update', $comment), $updatedComment);

        $this->assertCount(1, $mentionedUser->fresh()->notifications);
        $this->assertEquals($comment->id, $mentionedUser->notifications()->first()['data']['comment']['id']);
        $this->assertCount(1, $newMentionedUser->fresh()->notifications);
        $this->assertEquals($comment->id, $newMentionedUser->notifications()->first()['data']['comment']['id']);

        $post->delete();
        $mentionedUser->delete();
        $poster->delete();
        $newMentionedUser->delete();
    }

    /** @test */
    public function unverified_users_should_not_receive_notification_when_are_mentioned_in_a_comment()
    {
        $this->mentionedUser->update(['email_verified_at' => null]);
        $comment = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $comment);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAComment::class);
    }

    /** @test */
    public function users_should_not_receive_database_notificationsns_when_they_are_mentioned_in_a_comment_if_mention_notifications_are_disabled()
    {
        $this->mentionedUser->preferences()->merge(['mentioned_in_comment' => []]);
        $comment = ['body' => "hello @{$this->mentionedUser->name}"];
        $this->desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $comment);

        Notification::assertSentTo($this->mentionedUser,
            function (YouHaveBeenMentionedInAComment $notification, $channels) {
                return $notification->profilePost->is($this->profilePost) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function users_should_not_receive_database_notifications_when_are_mentioned_by_ignored_users()
    {
        $this->mentionedUser->ignore($this->commentPoster);
        $comment = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $comment);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAComment::class);
    }

    /** @test */
    public function users_should_not_receive_a_database_notification_when_they_mention_their_own_name()
    {
        $comment = ['body' => "hello @{$this->commentPoster->name}"];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $comment);

        Notification::assertNotSentTo($this->commentPoster, YouHaveBeenMentionedInAComment::class);
    }
}