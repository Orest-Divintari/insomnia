<?php

namespace Tests\Unit;

use App\Filters\Filters;
use App\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_searches_for_records_given_a_username_that_does_not_exist_then_throw_error()
    {
        $filter = app(Filters::class, ['builder' => Thread::query()]);

        $this->expectException(ModelNotFoundException::class);
        $filter->postedBy('random name');
    }
}