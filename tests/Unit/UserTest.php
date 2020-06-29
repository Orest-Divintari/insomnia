<?php

namespace Tests\Unit;

use App\Like;
use App\Reply;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /** @test */
    public function a_user_can_determine_the_path_to_his_avatar()
    {

        $avatar = '/avatars/users/user_logo.png';
        $user = create(User::class, ['avatar_path' => $avatar]);

        $this->assertEquals(asset($avatar), $user->avatar_path);
    }

    /** @test */
    public function user_has_a_shorter_version_of_his_name()
    {
        $user = create(User::class, ['name' => $this->faker->sentence()]);
        $this->assertEquals(
            Str::limit($user, config('contants.user.name_limit'), ''),
            $user->shorName
        );

    }

    /** @test */
    public function an_authenticated_user_can_mark_a_thread_as_read()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->assertTrue($thread->hasBeenUpdated);

        $user->read($thread);
        $this->assertFalse($thread->hasBeenUpdated);

        Cache::forever($user->visitedThreadCacheKey($thread), Carbon::now()->subDay());

        $this->assertTrue($thread->hasBeenUpdated);

    }

    /** @test */
    public function a_user_may_like_a_post()
    {
        $user = create(User::class);
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        Like::create([
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $user->likes);

    }

    /** @test */
    public function a_user_has_subscriptions()
    {
        $user = create(User::class);

        $thread = create(Thread::class);

        $thread->subscribe($user->id);

        $this->assertCount(1, $user->subscriptions);

    }

}