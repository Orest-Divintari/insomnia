<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewFollowingUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_list_of_the_users_that_follows()
    {
        $orestis = $this->signIn();
        $john = create(User::class);
        $orestis->follow($john);

        $response = $this->get(route('account.followings.index'));

        $response
            ->assertSee($john->name)
            ->assertSeeInOrder(['Messages:', $john->profile_posts_count])
            ->assertSeeInOrder(['Likes score:', $john->profile_posts_count])
            ->assertSee($john->received_likes_count);
    }
}
