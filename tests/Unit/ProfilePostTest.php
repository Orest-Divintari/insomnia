<?php

namespace Tests\Unit;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfilePostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_profile_post_has_an_poster()
    {
        $user = create(User::class);

        $profilePost = ProfilePostFactory::by($user)->create();

        $this->assertEquals($user->id, $profilePost->poster->id);
    }

    /** @test */
    public function a_post_has_a_formatted_date_of_creation()
    {
        $user = create(User::class);

        $profilePost = ProfilePostFactory::by($user)->create();

        $this->assertEquals(
            Carbon::now()->calendar(),
            $profilePost->date_created
        );
    }

    /** @test */
    public function a_post_has_comments()
    {
        $post = create(ProfilePost::class);

        $comment = CommentFactory::toProfilePost($post)->create();

        $this->assertCount(1, $post->comments);
    }

    /** @test */
    public function a_post_can_add_a_new_comment()
    {
        $post = create(ProfilePost::class);
        $poster = $this->signIn();

        $post->addComment($this->faker->sentence, $poster);

        $this->assertCount(1, $post->comments);
    }

    /** @test */
    public function a_profile_post_knows_the_owner_of_the_profile_in_which_was_posted()
    {
        $profileOwner = create(User::class);

        $post = ProfilePostFactory::toProfile($profileOwner)->create();

        $this->assertEquals($profileOwner->id, $post->profileOwner->id);
    }

    /** @test */
    public function a_profile_post_has_activities()
    {
        $user = $this->signIn();

        $profilePost = ProfilePostFactory::by($user)->create();

        $this->assertCount(1, $profilePost->activities);
    }

    /** @test */
    public function a_profile_post_has_search_info()
    {
        create(ProfilePost::class);

        $profilePost = ProfilePost::withSearchInfo()->get()->toArray()[0];

        $this->assertArrayHasKey('poster', $profilePost);
        $this->assertArrayHasKey('profile_owner', $profilePost);
    }

    /** @test */
    public function it_knows_its_path()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $numberOfPages = 5;
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);

        $lastPost = $posts->last();

        $this->assertEquals(
            route('profiles.show', $orestis) .
            '?page=' . $numberOfPages .
            '#profile-post-' . $lastPost->id
            , $lastPost->path
        );
    }

}