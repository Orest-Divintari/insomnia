<?php

namespace Tests\Unit\Statistics;

use App\Models\User;
use App\Statistics\UserStatistics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected $userStatistics;

    public function setUp(): void
    {
        parent::setUp();
        unset(app()[UserStatistics::class]);
        app()->instance(UserStatistics::class, new RealUserStatistics);
        $this->userStatistics = app(UserStatistics::class);
        config(['cache.default' => 'redis']);
        $this->userStatistics->resetCount();
    }

    /** @test */
    public function it_returns_the_users_count_from_cache()
    {
        $this->assertEquals(0, $this->userStatistics->count());

        create(User::class);

        $this->assertEquals(1, $this->userStatistics->count());

        $this->userStatistics->resetCount();
    }

    /** @test */
    public function it_increments_the_users_count_in_cache_when_a_new_user_is_created()
    {
        $this->assertEmpty(User::all());
        create(User::class);

        $this->assertEquals(1, $this->userStatistics->count());

        $this->userStatistics->resetCount();
    }

    /** @test */
    public function it_decrements_the_users_count_in_cache_when_a_user_is_deleted()
    {
        $this->assertEmpty(User::all());
        $thread = create(User::class);
        $this->assertEquals(1, $this->userStatistics->count());

        $thread->delete();

        $this->assertEquals(0, $this->userStatistics->count());
    }

    /** @test */
    public function it_resets_the_users_count_in_cache()
    {
        $this->assertEmpty(User::all());
        $user = create(User::class);
        $anotherUser = create(User::class);
        $this->assertEquals(2, $this->userStatistics->count());

        $this->userStatistics->resetCount();

        $this->assertEquals(0, $this->userStatistics->count());
    }
}