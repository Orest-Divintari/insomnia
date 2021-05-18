<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_filter_can_be_applied_twice_only_by_the_same_model_filter()
    {
        // $modelFilterClassA = ThreadFilters::class;
        // $modelFilterClassB = ProfilePostFilters::class;
        // $chain = new ModelFilterChain();
        // $chain->addFilter($modelFilterClassA);
        // $chain->addFilter($modelFilterClassB);
        // $filter = 'postedBy';
        // $filterManager = new FilterManager($chain);
        // $filterManager->appliedFilters = [];
        // $this->assertTrue(
        //     $filterManager->canBeApplied($modelFilterClassA, $filter)
        // );

        // $filterManager->appliedFilters[$modelFilterClassA][] = $filter;

        // $this->assertTrue(
        //     $filterManager->canBeApplied($modelFilterClassA, $filter)
        // );
        // $this->assertFalse(
        //     $filterManager->canBeApplied($modelFilterClassB, $filter)
        // );
    }
}