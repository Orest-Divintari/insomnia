<?php

namespace Tests\Unit;

use App\ProfilePost;
use App\Reply;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_profile_post_has_an_poster()
    {
        $user = create(User::class);

        $profilePost = create(ProfilePost::class, [
            'user_id' => $user->id,

        ]);
        $this->assertEquals($user->id, $profilePost->poster->id);
    }

    /** @test */
    public function a_post_has_a_formatted_date_of_creation()
    {
        $user = create(User::class);

        $profilePost = create(ProfilePost::class, [
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Carbon::now()->calendar(), $profilePost->date_created);
    }

    /** @test */
    public function a_post_has_comments()
    {
        $post = create(ProfilePost::class);

        $comment = create(Reply::class, [
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $this->assertCount(1, $post->comments);
    }

    /** @test */
    public function a_post_can_add_a_new_comment()
    {
        $post = create(ProfilePost::class);

        $poster = $this->signIn();
        $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $poster->id,
            ],
            $poster
        );

        $this->assertCount(1, $post->comments);
    }

    /** @test */
    public function a_profile_post_knows_the_owner_of_the_profile_in_which_was_posted()
    {
        $profileOwner = create(User::class);

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);

        $this->assertEquals($profileOwner->id, $post->profileOwner->id);
    }

    /** @test */
    public function a_profile_post_has_activities()
    {
        $user = $this->signIn();

        $profilePost = create(ProfilePost::class, ['user_id' => $user->id]);

        $this->assertCount(1, $profilePost->activities);
    }

}