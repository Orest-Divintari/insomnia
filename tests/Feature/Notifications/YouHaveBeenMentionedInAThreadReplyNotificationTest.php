<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\YouHaveBeenMentionedInAThreadReply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class YouHaveBeenMentionedInAThreadReplyNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->desiredChannels = ['database'];
        $this->thread = create(Thread::class);
        $this->mentionedUser = create(User::class);
        $this->replyPoster = $this->signIn();
    }

    /** @test */
    public function a_user_receives_a_database_notification_when_is_mentioned_in_a_thread_reply()
    {
        $reply = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.replies.store', $this->thread), $reply);

        Notification::assertSentTo($this->mentionedUser,
            function (YouHaveBeenMentionedInAThreadReply $notification, $channels) {
                return $notification->thread->is($this->thread) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_notifications_only_to_the_newly_mentioned_users_when_a_reply_is_updated()
    {
        $this->withoutExceptionHandling();
        unset(app()[ChannelManager::class]);
        $this->withoutMiddleware([ThrottlePosts::class]);
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $poster = create(User::class, ['name' => 'papandreou']);
        $this->signIn($poster);
        $thread = create(Thread::class);
        $mentionedUser = create(User::class, ['name' => 'mitsotakis']);
        $reply = ['body' => "hello @{$mentionedUser->name}"];
        $newMentionedUser = create(User::class, ['name' => 'pagalos']);

        $reply = $this->postJson(route('ajax.replies.store', $thread), $reply)->json();

        $this->assertCount(1, $mentionedUser->notifications);
        $this->assertEquals($reply['id'], $mentionedUser->notifications()->first()['data']['reply']['id']);
        $this->assertCount(0, $newMentionedUser->notifications);

        $updatedReply = ['body' => "hello @{$mentionedUser->name} and @{$newMentionedUser->name}"];
        $reply = Reply::where('repliable_type', Thread::class)->where('id', $reply['id'])->first();

        $this->patchJson(route('ajax.replies.update', $reply), $updatedReply);

        $this->assertCount(1, $mentionedUser->fresh()->notifications);
        $this->assertEquals($reply->id, $mentionedUser->notifications()->first()['data']['reply']['id']);
        $this->assertCount(1, $newMentionedUser->fresh()->notifications);
        $this->assertEquals($reply->id, $newMentionedUser->notifications()->first()['data']['reply']['id']);

        $thread->category->delete();
        $mentionedUser->delete();
        $poster->delete();
        $newMentionedUser->delete();
    }

    /** @test */
    public function unverified_users_should_not_receive_notification_when_are_mentioned_in_a_thread_reply()
    {
        $this->mentionedUser->update(['email_verified_at' => null]);
        $reply = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.replies.store', $this->thread), $reply);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAThreadReply::class);
    }

    /** @test */
    public function users_should_not_receive_database_notificationsns_when_they_are_mentioned_in_a_reply_if_mention_notifications_are_disabled()
    {
        $this->mentionedUser->preferences()->merge(['mentioned_in_thread_reply' => []]);
        $reply = ['body' => "hello @{$this->mentionedUser->name}"];
        $this->desiredChannels = [];

        $this->postJson(route('ajax.replies.store', $this->thread), $reply);

        Notification::assertSentTo($this->mentionedUser,
            function (YouHaveBeenMentionedInAThreadReply $notification, $channels) {
                return $notification->thread->is($this->thread) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function users_should_not_receive_database_notifications_when_are_mentioned_by_ignored_users()
    {
        $this->mentionedUser->ignore($this->replyPoster);
        $reply = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->postJson(route('ajax.replies.store', $this->thread), $reply);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAThreadReply::class);
    }

    /** @test */
    public function users_should_not_receive_a_database_notification_when_they_mention_their_own_name()
    {
        $reply = ['body' => "hello @{$this->replyPoster->name}"];

        $this->postJson(route('ajax.replies.store', $this->thread), $reply);

        Notification::assertNotSentTo($this->replyPoster, YouHaveBeenMentionedInAThreadReply::class);
    }
}