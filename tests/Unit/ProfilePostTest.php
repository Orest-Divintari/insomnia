<?php

namespace Tests\Unit;

use App\ProfilePost;
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
            'poster_id' => $user->id,

        ]);
        $this->assertEquals($user->id, $profilePost->poster->id);
    }

    /** @test */
    public function a_post_has_a_formatted_date_of_creation()
    {
        $user = create(User::class);

        $profilePost = create(ProfilePost::class, [
            'poster_id' => $user->id,
        ]);

        $this->assertEquals(Carbon::now()->calendar(), $profilePost->date_created);
    }
}