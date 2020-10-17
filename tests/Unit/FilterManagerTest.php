<?php

namespace Tests\Unit;

use App\Filters\FilterManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_filter_can_be_applied_twice_only_by_the_same_model_filter()
    {
        $modelFilterClassA = 'ModelFilterClassA';
        $modelFilterClassB = 'ModelFilterClassB';
        $filter = 'postedBy';

        $filterManager = new FilterManager();
        $filterManager->appliedFilters = [];

        $this->assertTrue(
            $filterManager->canBeApplied($modelFilterClassA, $filter)
        );

        $filterManager->appliedFilters[$modelFilterClassA][] = $filter;

        $this->assertTrue(
            $filterManager->canBeApplied($modelFilterClassA, $filter)
        );

        $this->assertFalse(
            $filterManager->canBeApplied($modelFilterClassB, $filter)
        );

    }
}