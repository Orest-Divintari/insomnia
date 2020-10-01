<?php

namespace Tests\Unit;

use App\Filters\ProfilePostFilters;
use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePostFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The filters that apply to profile posts
     *
     * @var ProfilePostFilters
     */
    protected $profilePostFilters;

    public function setUp(): void
    {
        parent::setUp();
        $this->profilePostFilters = app(
            ProfilePostFilters::class,
            ['builder' => ProfilePost::query()]
        );
    }

    /** @test */
    public function get_the_profile_posts_that_belong_to_a_given_profile()
    {
        create(ProfilePost::class);
        $user = create(User::class);
        $desiredProfilePost = create(ProfilePost::class, ['profile_owner_id' => $user->id]);

        $this->profilePostFilters->profileOwner($user->name);
        $profilePosts = $this->profilePostFilters->getBuilder()->get();

        $this->assertCount(1, $profilePosts);
        $this->assertEquals($desiredProfilePost->id, $profilePosts[0]->id);
    }

    /** @test */
    public function get_the_profile_posts_that_were_created_a_given_number_of_days_ago()
    {
        create(
            ProfilePost::class,
            ['created_at' => Carbon::now()->subDays(10)]
        );

        $daysAgo = 5;
        $numberOfDesiredProfilePosts = 2;
        $desiredProfilePosts = createMany(
            ProfilePost::class,
            $numberOfDesiredProfilePosts,
            ['created_at' => Carbon::now()->subDays($daysAgo)]
        );

        $this->profilePostFilters->lastCreated($daysAgo);
        $profilePosts = $this->profilePostFilters->getBuilder()->get();

        $this->assertCount(
            $numberOfDesiredProfilePosts,
            $profilePosts
        );
        $this->assertTrue(
            $profilePosts->contains(
                'id',
                $desiredProfilePosts[0]->id
            )
        );
        $this->assertTrue(
            $profilePosts->contains(
                'id',
                $desiredProfilePosts[1]->id
            )
        );
    }
}